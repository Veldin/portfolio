<?php
//Een class met al mijn paginas als functies
class Pages {  
	//Hoofd pagina
//Hoofd pagina
	function home(){
		global $core;
		global $pages;
		global $dbc;
		global $user;
		
		
		
		//$levelid = $user->get()['levelid'];
		$currentuser = $user->get()['levelid'];
		
		//if login true
		if($user->isLoggedIn()){
		
			echo '<div id="containerOuter">';
			echo '<div id="containerInner">';
			echo '<h1>Welkom!</h1>';
			
			
				//if student
				
			if($currentuser == 1){	//$value['levelid'] wordt $levelid
				echo $pages->studentHome();
			//if teacher 
			}elseif($currentuser == 2){
					echo $pages->teacherHome();
			//if admin
			}elseif($currentuser == 3){
				echo $pages->adminHome();
			}

			echo '<h2>Chatbox</h2>';
			echo '<p>Voel je vrij om te chatten!</p>';
			echo '<iframe class="iframechat" src="chat.php"></iframe>';

			if(isset($_POST['message']) && !empty($_POST['message'])){
				
				$_POST['message'] = preg_replace('/[^a-z0-9]/i', ' ', $_POST['message']);
				
				$stmt = $dbc->prepare("INSERT INTO `chat` (
				`userid` ,
				`timestamp` ,
				`targetid` ,
				`message`
				)
				VALUES (
				'".$user->get()['id']."',  '".TIME()."',  '0',  '".$_POST['message']."'
				);");
				
				$stmt->execute();
			}
			
			
			
			
			
			/* foreach ($result as &$message) {
				echo '<tr>';
					echo '<td class="firstname">';
						echo $message['firstname'];
					echo '</td>';
					echo '<td class="message">';
						echo $message['message'];
					echo '</td>';
				echo '</tr>';
			} */
			
			
			echo '<form method="Post" action="?p=home">';
				echo '<input class="form-control" type="text" name="message" placeholder="Chat!" value=" ">';
			echo '</form>';
			echo '</div>';
			echo '</div>';
			
		//if not logged in show login pages
		}else{
			header("Location: index.php?p=login");
			echo $pages->login();
		}				
	}
	
	function login(){
		global $user;
		global $mysqliconnect;
		global $pages;
		global $core;
		
		echo '<div id="containerOuter">';
		echo '<div id="containerInner">';
		echo '<h1>Login</h1>';
		
		if($user->isLoggedIn() == FALSE){
			if(isset($_POST['submit'])){
				if(empty($_POST['email']) OR empty($_POST['password'])){
					echo'<div class="alert alert-danger">
					  <strong>:( </strong> vul alle velden in.
					</div>';
					
				}else{
					$email = $_POST['email'];
					$password = $_POST['password'];
					
					$user->login($email, $password);
					if($user->login($email, $password) == TRUE){
						header("Location: index.php");
					}else{
						echo'<div class="alert alert-danger">
						  <strong>:( </strong> Inloggegevens ongeldig
						</div>';
					}
				}
			}else{
				$email = "";
			}
			
			if(!isset($email)){
				$email = "";
			}
			
			if(isset($_GET["register"])){
				echo '<div class="alert alert-success">
				  <strong>Success!</strong> Uw account is aangemaakt! U kunt nu inloggen.
				</div>';
			}
			
			echo "<form method='POST' action='#'>
					<input type='text' name='email' placeholder='E-mail' value='$email'><br>";
			echo "	<input type='password' placeholder='Wachtwoord' name='password'><br>";
			echo "<input type='submit' name='submit' value='Login'></form>";
			echo "<a href='index.php?p=register'>Registreer</a>";
		}else{ //Als $isLoggedIn == TRUE
			echo "U bent al ingelogd.";
		}
		
		echo '</div>';
		echo '</div>';
		
		
	}
	
	function logout(){
		global $user;
		//logout(){
		
		$user->logout();
		
		header("Location: index.php");
	}
	
	function register(){
		global $user;
		$errors = array(
				99 => "Je hebt niet alle velden ingevuld.",
				100 => "Het ingevoerde email adres is niet correct.",
				101 => "De ingevoerde wachtwoorden komen niet overeen.",
				102 => "Het ingevoerde wachtwoord voldoet niet aan de eisen. (Minstens 1 kleine letter, 1 hoofdletter, 1 cijfer en een speciaal teken)",
				103 => "Het ingevoerde telefoonnummer is niet numeriek of 10 karakters lang.",
				104 => "De ingevoerde postcode is niet volgens het 1234AB formaat.",
				105 => "Het email adres is al in gebruik.",
				106 => "Er is een fout opgetreden bij het aanmaken van het portfolio."
		);
		if(isset($_POST['register'])){
				if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_repeat']) && !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['phone']) && !empty($_POST['zipcode']) && !empty($_POST['address'])){
						$resultCode = $user->register($_POST['email'], $_POST['password'], $_POST['password_repeat'], $_POST['firstname'], $_POST['lastname'], $_POST['phone'], $_POST['zipcode'], $_POST['address']);
						if(is_numeric($resultCode) && array_key_exists($resultCode, $errors)){
								echo "<div class='alert alert-danger' role='alert'><strong>Oops!</strong> " . $errors[$resultCode] . "</div>";
						}else{
								header('Location: index.php?p=login?register');
						}
				}else{
						echo "<div class='alert alert-danger' role='alert'><strong>Oops!</strong> Je hebt niet alle velden ingevuld.</div>";
				}
		}
		echo "<h2>Maak een account aan</h2>";
		?>

		<form method='post' action='#'>
				<input type='email' name='email' placeholder='E-mail' required value=<?php if(isset($_POST['email'])) echo $_POST['email']; ?>><br>
				<input type='password' name='password' placeholder='Wachtwoord' required value=<?php if(isset($_POST['password'])) echo $_POST['password']; ?>><br>
				<input type='password' name='password_repeat' placeholder='Herhaal wachtwoord' required value=<?php if(isset($_POST['password_repeat'])) echo $_POST['password_repeat']; ?>><br>
				<input type='text' name='firstname' placeholder='Voornaam' required value=<?php if(isset($_POST['firstname'])) echo $_POST['firstname']; ?>><br>
				<input type='text' name='lastname' placeholder='Achternaam' required value=<?php if(isset($_POST['lastname'])) echo $_POST['lastname']; ?>><br>
				<input type='number' name='phone' placeholder='Telefoonnummer' required value=<?php if(isset($_POST['phone'])) echo $_POST['phone']; ?>><br>
				<input type='text' name='zipcode' placeholder='Postcode' required value=<?php if(isset($_POST['zipcode'])) echo $_POST['zipcode']; ?>><br>
				<input type='text' name='address' placeholder='Huisnummer' required value=<?php if(isset($_POST['address'])) echo $_POST['address']; ?>><br>
				<input type='submit' name='register' value='Registreer'>
				</form>

				<?php
		echo "<p>Heb je al een account? <a href='index.php?p=login'>Log in</a></p>";
	}
	
	function adminHome(){
		global $dbc;
		global $user;
		global $pages;

			$levelid = $user->get()['levelid'];

			$firstname = $user->get()['firstname'];
			$lastname = $user->get()['lastname'];
			
			echo "<h2>Welkom ".$firstname." ".$lastname.".</h2>";
			
			$SQLstring = $dbc->prepare("SELECT COUNT(user.id) AS useramount FROM user");
			$SQLstring->execute();
			foreach($SQLstring as $key => $value){
				echo "<p>Er zijn ".$value['useramount']." accounts aangemaakt:</p>";
			}
			$SQLstring = $dbc->prepare("SELECT COUNT(user.id) AS useramount FROM user WHERE levelid = 1");
			$SQLstring->execute();
			foreach($SQLstring as $key => $value){
				echo "<p>	".$value['useramount']." studenten accounts.<br>";
			}
			
			$SQLstring = $dbc->prepare("SELECT COUNT(user.id) AS useramount FROM user WHERE levelid = 2");
			$SQLstring->execute();
			foreach($SQLstring as $key => $value){
				echo $value['useramount']." docenten accounts.</p>";
			}
			
			echo "<p><a href='LINK'>Overzicht accounts</a><p>";	//Link naar overzicht account
			echo "<p><a href='LINK'>Aanpassen accounts</a><p>";	//Link naar de wijzig account pagina
			
			//if not logged in go to home
	}
	
	function teacherHome(){
		global $dbc;
		global $user;
		global $pages;
		
		$pages->search();
		
		/* $levelid = $user->get()['levelid'];
		if($user->isLoggedIn() AND $levelid == 2){
		
		$firstname = $user->get()['firstname'];
		$lastname = $user->get()['lastname'];
		
		echo "<h2>Welkom ".$firstname." ".$lastname.".</h2>";
		
		$SQLstring = $dbc->prepare('SELECT lastname, firstname, zipcode, phone, email, url FROM user, portfolio
						WHERE user.levelid = 1 AND user.id = portfolio.userid 
						ORDER BY lastname ASC'
						);
		$SQLstring->execute();
		$SQLstring = $SQLstring->fetchAll(PDO::FETCH_ASSOC);
		
		echo "<table border='1'>";
		echo "<tr><th>Achternaam</th> <th>Voornaam</th> <th>Postcode</th> <th>Telefoonnummer</th> <th>E-mail</th> <th>url</th></tr>";
		foreach($SQLstring as $key => $value)
                    {
                        echo "<tr><td>{$value['lastname']}</td>";
                        echo "<td>{$value['firstname']}</td>";
                        echo "<td>{$value['zipcode']}</td>";
                        echo "<td>{$value['phone']}</td>";
                        echo "<td>{$value['email']}</td>";
						echo "<td><a href='{$value['url']}'>portfolio</a></td></tr>";
                        
                    }
                
		echo "</table>"; */
		
		//if not logged in go to home
		/* }else{
			
			echo $pages->home();	//HOME REGELT DE REST
		} */
	}
	
	function studentHome(){
		global $dbc;
		global $user;
		global $pages;
		
		$levelid = $user->get()['levelid'];
		if($user->isLoggedIn() AND $levelid == 1){
		
		$editprofile = "Link";	//Link naar de profile edit
		$viewprofile = "Link";	//Link naar het profile overzicht
		$viewportfolio = "Link";	//Link naar je portfolio
		$createportfolio = "Link";	//Link naar de pagina voor het aanmaken van je portfolio
		
		$currentuser = $user->get()['id'];
		
		$SQLstring = $dbc->prepare("SELECT user.timestamp, user.firstname, user.lastname FROM user 
						WHERE user.id = $currentuser");
		$SQLstring->execute();
		$SQLstring = $SQLstring->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($SQLstring as $key => $value){
		echo "<p>Hallo {$value['firstname']} {$value['lastname']}.</p>";
		echo "<p>Dit account is aangemaakt op {$value['timestamp']}.</p>";
		}		
		$SQLstring2 = $dbc->prepare("SELECT COUNT(chat.targetid) AS commentamount, MIN(chat.timestamp) AS lasttimestamp FROM chat 
									WHERE chat.targetid = $currentuser");
		$SQLstring2->execute();
		$SQLstring2 = $SQLstring2->fetchAll(PDO::FETCH_ASSOC);
		foreach($SQLstring2 as $key => $value){
		echo "<p>Je hebt ".$value['commentamount']." comment(s) op jou portfolio.</p>";
		if($value['lasttimestamp'] < 60){
			$seconds = $value['lasttimestamp'];
			echo "<p>De laatste comment was ".floor($seconds)." seconden geleden.</p>";
		}elseif($value['lasttimestamp'] > 60 AND $value['lasttimestamp'] < 3600){
			$minutes = $value['lasttimestamp']/60;	
			echo "<p>De laatste comment was ".floor($minutes)." minuten geleden.</p>";
		}elseif($value['lasttimestamp'] > 3600 AND $value['lasttimestamp'] < 86400){
			$hours = $value['lasttimestamp']/3600;
			echo "<p>De laatste comment was ".floor($hours)." uur geleden.</p>";
		}elseif($value['lasttimestamp'] > 86400){
			$days = $value['lasttimestamp']/86400;
			echo "<p>De laatste comment was ".floor($days)." dag(en) geleden.</p>";
		}else{
			echo "<p>Je hebt nog geen comments.</p>";
		}
		}
		
		//if not logged in go to home
		}else{
			
			echo $pages->home();	//HOME REGELT DE REST
		}
	}
	
	function generatecsscolls(){
	
		for ($x = 0; $x <= 100; $x++) {
			echo "<br>";
			echo ".coll-".$x."{";
				echo"width: ".$x."%;";
				echo"min-height: 1px;";
				echo"float: left;";
				echo"position: relative;";
			echo"}";
		}
		
		echo"<br>@media only screen and (max-width: 700px) {<br>";
		for ($x = 0; $x <= 99; $x++) {
			echo".coll-".$x.",<br>";
		}
			echo"{<br>";
				echo"width: 100%;<br>";
			echo"}<br>";
		echo"}<br>";
		
		
	}
	
	function header(){
		global $pages;
		
		echo $pages->navigation();
	}
	
	
	function navigation() {
		global $user;
		global $core;
			
			if($user->isLoggedIn()){
				$currentSLB = $user->get()['slb'];
				$userID = $user->get()['id'];
				$levelID = $user->get()['levelid'];
				
				if(($user->get()['slb'] == $user->get()['id']) && $user->get()['levelid'] == 2){
					$isSLB = TRUE;
				}else{
					$isSLB = FALSE;
				}
			}else{
				$levelID = 0;
				$isSLB = false;
			}
			
			
			
			// Deze variabelen zijn voor de linkjes naar de bijbehorende paginas.
			$viewFiles = "showUploads";
			$uploadFiles = "uploadFile"; //
			$viewPortfolio = "eigenportfolio";
			$editPortfolio = "portfoliooverzicht"; //leerling eigen
			$overviewFiles = "overzichtUploads"; //admin / slb
			$guidedStudents = "search"; //admin / slb <-search
			$profiel = "profile|right";
			$login = "login|right";
			$logout = "logout|right";
			$overviewPort = "search";
			
			$menu = array();
			
			// checked of levelID het ID van een student is.
			if($levelID == 1){
				// maakt de array aan met de navigatie structuur.
				$menu = array(
					"Bekijk jou portfolio" => $viewPortfolio,
					"Bewerk jou portfolio" => $editPortfolio,
					"Bekijk jou bestanden" => $viewFiles,
					"Upload bestanden" => $uploadFiles
				);
			}
			
			// checked of levelID het ID van een docent is //
			if($levelID == 2){
				// checked of de docent een studieloopbaanbegeleider is //
				if($isSLB == TRUE){
					// voegt extra menu opties toe voor SLBers //
					$menu = array(
						"Overzicht van studenten" => $guidedStudents,
						"Overzicht van bestanden" => $overviewFiles
					);
					// Als het geen SLBer is dan maakt hij een overzicht voor een gewone leraar aan.
				}else{
					$menu = array(
						"Overzicht van portfolios" => $overviewPort
					);
				}
			}
			if($levelID == 3){
				$menu = array(
					"Overzicht van alle gebruikers" => $guidedStudents,
					"Overzicht van bestanden" => $overviewFiles
				);
			}
			if($levelID > 0){
				$menu += array(
					"Uitloggen" => $logout,
					"Jouw profiel" => $profiel
				);
			}else{
				$menu += array(
					"Inloggen" => $login
				);
			}
/* 			echo "<pre>";
			var_export($menu);
			echo "</pre>"; */
			
			
			
			echo "<ul>";

			if($levelID > 0){
			echo '<li><a role="button" class="btn btn-default madebystudent" href="index.php?p=home"></a></li>';
			}
			
			if($levelID == 3 || $isSLB == TRUE){
				echo "<li><form method='post' action='index.php?p=search'>
							<input class='btn btn-zoeken' type='text' name='search' placeholder='Zoeken'>
							<button type='submit' name='submit' class='btn btn-default'>
								 <i class='fa fa-search' aria-hidden='true'></i>
							</button>
							

						</form></li>";
			}

			if($levelID > 0){
				foreach($menu as $optionDesc => $option){
					$option = explode("|", $option);
					
					if(isset($_GET["p"])){
						$page = htmlspecialchars($_GET["p"]);
					}else{
						$page = 'home';
					}
					
					$liClass = ''; //Voor het houden van de classes op de LI
					$AClass = ''; //Voor het houden van de classes op de A
					
					//Extra meegegeven classes toevoegne aan de LI. (left/right ect)
					if(isset($option[1])){
						$liClass .= $option[1];
					}
					
					//Extra meegegeven classes toevoegne aan de A. (button color ect)
					if((isset($_GET["p"]) && htmlspecialchars($_GET["p"]) == $option[0]) || ($page == 'portfolio' && $option[0] == 'eigenportfolio')){
						$AClass .= 'btn btn-primary';
					}else{
						$AClass .= 'btn btn-default';
					}

					echo "<li class=".$liClass." ><a role='button' class='".$AClass."' href=index.php?p=$option[0]>$optionDesc</a></li>";
				}
				echo "</ul>";
			}
		
		echo '<div class="clear"></div>';
	}

	function footer(){
		echo '<div id="footerOuter"><div id="footerInner"></div></div>';
		
	}

	
	function search(){
		global $user;
		global $core;
		global $dbc;
		global $mysqlconn;
		
		echo '<div id="containerOuter">';
		echo '<div id="containerInner">';
		echo '<h1>Gebruiker Overzicht</h1>';
		
		if($user->isLoggedIn()){
			$currentSLB = $user->get()['slb'];
			$userID = $user->get()['id'];
			$levelid = $user->get()['levelid'];
			
			if($levelid < 2){
				header("Location: index.php");
			}
			
			if($userID == $currentSLB){
				$isSLB = TRUE;
			}else{
				$isSLB = FALSE;
			}
		}
		$tablename = "user";

		
			
		if(isset($_POST['waarmerk']) AND $isSLB == TRUE){
		
			$query = "SELECT id FROM $tablename WHERE levelid = 1 AND slb = $currentSLB";
			$result = mysqli_query($mysqlconn, $query);
			while($fetch = mysqli_fetch_assoc($result)){
				$userid = $fetch['id'];
				
				
				
				if(isset($_POST[$userid])){
					$waarmerk = 1;
				}else{
					$waarmerk = 0;
				}
				$query2 = "UPDATE portfolio SET approved=$waarmerk WHERE userid=$userid";
				mysqli_query($mysqlconn, $query2);
			}
			echo "<p>De portfolios zijn gewaarmerkt!";
		}
			
			
			if(isset($_POST['submit']) AND $levelid == 3){
				if(!empty($_POST['search'])){
					$searchName = $_POST['search'];
					$query = "SELECT * FROM user, portfolio WHERE concat(firstname, ' ', lastname) LIKE '%$searchName%' AND user.id = portfolio.userid ORDER BY lastname ASC, firstname ASC";
					$queryresult = mysqli_query($mysqlconn, $query);
				}else{
					$query = "SELECT * FROM user, portfolio 
					WHERE user.id = portfolio.userid
					ORDER BY lastname ASC, firstname ASC,id ASC";
					$queryresult = mysqli_query($mysqlconn, $query);
				}
			}else if(isset($_POST['submit']) AND $levelid == 2){
				if(!empty($_POST['search'])){
					$searchName = $_POST['search'];
					$query = "SELECT * FROM user, portfolio WHERE concat(firstname, ' ', lastname) LIKE '%$searchName%' AND levelid = 1 AND user.id = portfolio.userid ORDER BY lastname ASC, firstname ASC";
					$queryresult = mysqli_query($mysqlconn, $query);
				}else{
					$query = "SELECT * FROM user, portfolio 
					WHERE user.id = portfolio.userid
					AND user.levelid = 1
					ORDER BY lastname ASC, firstname ASC,id ASC";
					$queryresult = mysqli_query($mysqlconn, $query);
				}
			}else if($levelid == 2){
					$query = "SELECT * FROM user, portfolio 
					WHERE user.id = portfolio.userid
					AND user.levelid = 1
					ORDER BY lastname ASC, firstname ASC,id ASC";
					$queryresult = mysqli_query($mysqlconn, $query);
			}else if($levelid == 3){
					$query = "SELECT * FROM user, portfolio 
					WHERE user.id = portfolio.userid
					ORDER BY lastname ASC, firstname ASC,id ASC";
					$queryresult = mysqli_query($mysqlconn, $query);
			}
			
			
			
				echo "<table class='table table-striped table-responsive'>";
				echo "<thead><tr><th>Voornaam</th><th>Achternaam</th><th>Email</th>";
				if($levelid == 3){
					echo "<th>ID</th><th>Rank</th>";
				}
				echo "<th>Gegevens</th>";
				echo "<th>Portfolio</th>";
				if($isSLB == TRUE && $levelid == 2){
					echo "<th>Waarmerk</th>";
				}
				echo "</tr></thead></tbody>";
				while ($fetch = mysqli_fetch_assoc($queryresult)){
					if($fetch['levelid'] == 1){
						$rank = "Student";
					}
					if($fetch['levelid'] == 2){
						$rank = "Docent";
					}
					if($fetch['levelid'] == 3){
						$rank = "Admin";
					}
					$id = $fetch['id'];
					$query2 = "SELECT id FROM user WHERE slb = $currentSLB AND id = $id";
					$result2 = mysqli_query($mysqlconn, $query2);
					$num_rows = mysqli_num_rows($result2);
					$query3 = "SELECT approved FROM portfolio WHERE userid = $id AND approved = 1";
					$result3 = mysqli_query($mysqlconn, $query3);
					$num_rows2 = mysqli_num_rows($result3);
					$id = $fetch['id'];
					$url = $fetch['url'];
					if($num_rows2 > 0){
						$checked = true;
					}else{
						$checked = false;
					}
					echo "<tr>";
					echo "<td>" . $fetch['firstname'] . "</td>";
					echo "<td>" . $fetch['lastname'] . "</td>";
					echo "<td>" . $fetch['email'] . "</td>";
					if($levelid == 3){
						echo "<td>" . $fetch['id'] . "</td>";
						echo "<td>" . $rank . "</td>";
						echo "<td><a href='?p=search&userid=$id'>Gegevens</a></td>";
						echo "<td><a href='?p=portfolio&u=$url'>Portfolio</a></td>";
					}
					
					if($levelid == 2){
						echo "<td><a href='?p=search&userid=$id'>Gegevens</a></td>";
						echo "<td><a href='?p=portfolio&u=$url'>Portfolio</a></td>";
						if($checked && $fetch['levelid'] == 1){
							echo "<td><form method='post' action='index.php?p=search'> GEWAARMERKT </td>";
						}else{
							$currentslb = $fetch['slb'];
							
							if(($currentslb == $currentSLB) && $fetch['levelid'] == 1){
								echo "<td><form method='post' action='index.php?p=search'><input type='checkbox' value=1 name='$id'></td>";
							}
						}
					}
					echo "</tr>";
				}
				if($levelid == 2 AND $currentSLB != 0){
					echo "<tr><td></td><td></td><td></td><td></td><td></td><td><input type='submit' name='waarmerk' value='Waarmerk'></td></tr>"; 
					echo "</form>";
				}
				echo "</tbody></table>";
			
			if(!empty($_GET['userid']) AND $levelid == 3){
				$userid = $_GET['userid'];
				$query = "SELECT * FROM $tablename WHERE id = $userid";
				$queryresult = mysqli_query($mysqlconn, $query);
				$fetch = mysqli_fetch_assoc($queryresult);
				$firstname = $fetch['firstname'];
				$lastname = $fetch['lastname'];
				$email = $fetch['email'];
				$phone = $fetch['phone'];
				$zipcode = $fetch['zipcode'];
				$levelid = $fetch['levelid'];
				$currentslb = $fetch['slb'];
				if($levelid == 1){$selected1 = 'selected';}else{$selected1 = FALSE;}
				if($levelid == 2){$selected2 = 'selected';}else{$selected2 = FALSE;}
				if($levelid == 3){$selected3 = 'selected';}else{$selected3 = FALSE;}
				if($zipcode == "NULL"){
					$zipcode = "";
				}
				echo "<p>De volgende velden zijn verplicht: Voornaam, Achternaam, E-mail, Rank</p>";
				echo "<p>Informatie over de student " . $firstname . " " . $lastname;
				if($levelid == 3){
					echo " met ID " . $userid . ":";
				}
				echo "</p>";
				echo "<form method='post' action='index.php?p=search&userid=".htmlentities($_GET['userid'])."'>";
				echo "<p>Voornaam: <input type='text' name='firstname' value='$firstname'></p>";
				echo "<p>Achternaam: <input type='text' name='lastname' value='$lastname'></p>";
				echo "<p>E-mail: <input type='text' name='email' value='$email'></p>";
				echo "<p>Telefoon: <input type='text' name='phone' value='$phone'></p>";
				echo "<p>Postcode: <input type='text' name='zipcode' value='$zipcode'></p>";
				echo "<p>Rank: <select name='rank'>";
				echo "<option value='1' $selected1>Student</option>";
				echo "<option value='2' $selected2>Docent</option>";
				echo "<option value='3' $selected3>Admin</option>";
				echo "</select></p>";
				$query = "SELECT id FROM $tablename WHERE slb = $userid";
				$queryresult = mysqli_query($mysqlconn, $query);
				$fetch = mysqli_fetch_assoc($queryresult);
				if(!empty($fetch['id'])){
					$checked = "checked";
				}else{
					$checked = FALSE;
				}
				if($levelid == 1){
					$query = "SELECT id, firstname, lastname FROM $tablename WHERE id = slb AND levelid = 2";
					$queryresult = mysqli_query($mysqlconn, $query);
					echo "<p>Studieloopbaanbegeleider: <select name='newslb'>";
					echo "<option value='' >Geen SLBer</option>";
					while ($fetch = mysqli_fetch_assoc($queryresult)){
						if($currentslb == $fetch['id']){
							$selected = " selected='selected'";
						}else{
							$selected = FALSE;
						}
						echo "<option value='" . $fetch['id'] ."' $selected'>" . $fetch['firstname'] . " " . $fetch['lastname'] . "</option>";
					}
					echo "</select></p>";
				}
				if($levelid == 2){
					echo "<p>SLB?: <input type='checkbox' value='slb' name='slb' $checked></p>";
				}
				echo "<p><input type='submit' name='update' value='update'></p>";
				echo "</form>";
				if(isset($_POST['update'])){
				
				
					echo '<hr>TRYING TO UPDATE!';
				
					if($levelid == 1){
						$newslb = $_POST['newslb'];
						$slb = ", slb='$newslb'";
					}
					if($levelid == 2){
						if(!empty($_POST['slb'])){
							$slb = ", slb='$userid'";
						}else{
							$slb = ", slb=''";
						}
					}
					$firstname = $_POST['firstname'];
					$lastname = $_POST['lastname'];
					$email = $_POST['email'];
					$phone = $_POST['phone'];
					$zipcode = $_POST['zipcode'];
					$levelid = $_POST['rank'];
					$query = "UPDATE $tablename SET firstname='$firstname', lastname='$lastname', email='$email', phone=$phone, zipcode='$zipcode', levelid='$levelid'$slb WHERE id = $userid";
					if(mysqli_query($mysqlconn, $query) == TRUE){
						
					}else{
						echo mysqli_error($mysqlconn) . "<br />";
					}
					echo '<script>window.location.href = "index.php?p=search";</script>';
				}
			}
			if(!empty($_GET['userid']) AND $levelid == 2 AND $isSLB == TRUE){
				$userid = $_GET['userid'];
				$query = "SELECT * FROM $tablename WHERE id = $userid";
				$queryresult = mysqli_query($mysqlconn, $query);
				$fetch = mysqli_fetch_assoc($queryresult);
				$firstname = $fetch['firstname'];
				$lastname = $fetch['lastname'];
				$email = $fetch['email'];
				$phone = $fetch['phone'];
				$zipcode = $fetch['zipcode'];
				/* echo "<p>Voornaam: " . $firstname;
				echo "<p>Achternaam: " . $lastname;
				echo "<p>e-mail: " . $email;
				echo "<p>telefoon nummer: " . $phone;
				echo "<p>Adres: " . $zipcode; */

			}
		echo '<div></div>';
	}
	
	//Test voor de database.
	//Toon alle info in de database.
	function testdb(){
		global $dbc;

		//Test 
		$sth = $dbc->prepare('show tables');
		$sth->execute();
		
		$all = $sth->fetchAll(PDO::FETCH_OBJ);
		
		foreach ($all as $value) {
			echo '<hr>';
			echo '<h2>'.$value->Tables_in_md253219db370063.'</h2>';
			echo '<br>';
			
			echo '<b>Describe</b><br>';
			$describe = $dbc->prepare('describe '.$value->Tables_in_md253219db370063);
			$describe->execute();
			$describe = $describe->fetchAll(PDO::FETCH_OBJ);
			foreach ($describe as $describe_value) {
				print_r($describe_value);
				echo '<br>';
			}
			
			echo '<b>Select</b><br>';
			$display = $dbc->prepare('select * from '.$value->Tables_in_md253219db370063);
			$display->execute();
			$display = $display->fetchAll(PDO::FETCH_BOTH);
			foreach ($display as $value) {
				echo '<br>';
				print_r($value);
				echo '<br>';
			}
			
		}
	}
	
	//Laden Custom CSS van potfolio
	function portfolioCSS(){
		global $dbc;
	
		if(isset($_GET["u"])){
			$userId = htmlspecialchars($_GET["u"]);
			$requestedPortfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$userId.'"');
			$requestedPortfolio->execute();
			$requestedPortfolio = $requestedPortfolio->fetchAll(PDO::FETCH_ASSOC);
			
			
			
			if(!empty($requestedPortfolio)){

				$requestedPortfolio = $requestedPortfolio[0];
			
				echo '<style>
				
					.inhoudsopgave {
					  list-style-type: none;
					}
					
					.inhoudsopgave li { 
						padding-left: 1em; 
						text-indent: -.7em;
					}

					.inhoudsopgave li:before {
						content: "• ";
						color: #'.$requestedPortfolio['tertiarycolour'].';
					}
					
					.moduleSeparator a{
						color: #'.$requestedPortfolio['colour'].';
						text-decoration: underline;
						-moz-text-decoration-color: #'.$requestedPortfolio['tertiarycolour'].';
						text-decoration-color: #'.$requestedPortfolio['tertiarycolour'].';
					}
					
					.moduleSeparator.odd a{
						color: #'.$requestedPortfolio['secondarycolour'].';
						text-decoration: underline;
						-moz-text-decoration-color: #'.$requestedPortfolio['tertiarycolour'].';
						text-decoration-color: #'.$requestedPortfolio['tertiarycolour'].';
					}
				
					.moduleSeparator{
						color: #'.$requestedPortfolio['colour'].';
						background-color: #'.$requestedPortfolio['secondarycolour'].';
						border-bottom: 2px solid #'.$requestedPortfolio['tertiarycolour'].';
						
						padding-top: 10px;
						padding-bottom: 10px;
						
						padding-top: 10vh;
						padding-bottom: 10vh;
					}
					.moduleSeparator.odd{
						padding-top: 10px;
						padding-bottom: 10px;
				
						padding-top: 10vh;
						padding-bottom: 10vh;
						
						color: #'.$requestedPortfolio['secondarycolour'].';
						background-color: #'.$requestedPortfolio['colour'].';
					}
				</style>';
			}
		}
	}
	
	//redirecten eigen portfolio
	function eigenportfolio(){
		global $dbc;
		global $core;
		global $user;
	
		if($user->isLoggedIn()){
			$userID = $user->get()['id'];

			header('Location: index.php?p=portfolio&u='.$core->getPortfolioURL($userID));
		}else{
			header('Location: index.php');
		}
	}
	
	//Laten zien van portfolio
	function portfolio(){
		global $dbc;
		global $core;
		global $portfolio;
		global $user;
		
		if(isset($_GET["u"])){
			$userId = htmlspecialchars($_GET["u"]);
			
			$requestedPortfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$userId.'"');
			$requestedPortfolio->execute();
			$requestedPortfolio = $requestedPortfolio->fetchAll(PDO::FETCH_ASSOC);
			
			if(!empty($requestedPortfolio)){
				//user bestaat
			
				$requestedPortfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$userId.'" LIMIT 1');
				$requestedPortfolio->execute();
				$requestedPortfolio = $requestedPortfolio->fetchAll(PDO::FETCH_ASSOC)[0];
				
				//print_r($requestedPortfolio);
				$requestedUser = $dbc->prepare('SELECT * FROM `user` WHERE `id` = "'.$requestedPortfolio['userid'].'" LIMIT 1');
				$requestedUser->execute();
				$requestedUser = $requestedUser->fetchAll(PDO::FETCH_ASSOC)[0];
				
				if($requestedUser['levelid'] == 1){
					$modules = $dbc->prepare('SELECT * FROM `module` WHERE `portfolioid` = "'.$requestedPortfolio['userid'].'" ORDER BY `position`');
					$modules->execute();
					$modules = $modules->fetchAll(PDO::FETCH_ASSOC);
					
					
					echo '<div id="containerOuter">';
						echo '<div class="portfolio" id="containerInner">';
					
						//variable om op teslaan waneer er een nieuwe break toegevoegd moet worden.
						$break = 0;
						$countbreaks = 0;
						
						if($user->get()['id'] == $core->getUserFromURL($userId) && isset($_GET["edit"])){
							echo '<a href="index.php?p=portfolio&u='.$userId.'"><div class="EditModusAan">Edit modus aan.</div></a>';
						}
						
						if($user->get()['id'] == $core->getUserFromURL($userId) && !isset($_GET["edit"])){
							echo '<a href="index.php?p=portfolio&u='.$userId.'&edit"><div class="EditModusUit">Edit modus uit.</div></a>';
						}
					
						
					
						echo '<div class="moduleSeparator">';
						echo '<div class="coll-100">';
					
						foreach ($modules as $module) {
							$moduletemplate = $dbc->prepare('SELECT * FROM `moduletemplate` WHERE `id` = "'.$module['moduleid'].'" LIMIT 1');
							$moduletemplate->execute();
							$moduletemplate = $moduletemplate->fetchAll(PDO::FETCH_ASSOC)[0];

							if (method_exists($portfolio,$moduletemplate['function'])){
								//echo 'Function Found';
								$input = explode(",", $module['input']);
								$fields = explode(",", $moduletemplate['field']);
								
								//Headers tellen niet mee met de break!
								if($moduletemplate['function'] !== 'header'){
									$break += $module['size'];
								}
								

								echo '<div class="module coll-'.$module['size'].'">';
									echo '<div class="contentMargin">';
									if(count($input) == count($fields)){
										if(count($input) == 1){
											echo $portfolio->$moduletemplate['function']($input[0]);
										}else if(count($input) == 2){
											echo $portfolio->$moduletemplate['function']($input[0], $input[1]);
										}
										
										//$user = new User("amr.jonkman@gmail.com", "pass", $dbc);
										//print_r($user);
										if($user->isLoggedIn()){
											if($user->get()['id'] == $core->getUserFromURL($userId) && isset($_GET["edit"])){
												echo '<a href="?p=editmodule&m='.$module['id'].'">';
												echo "<div class='editModule'> Edit </div>";
												echo '</a>';
											}
										}
									}else{
										echo '<div class="alert alert-danger">
										  <strong>:(</strong> Aantal inputs komt niet overeen met het aantal benodigde velden.
										</div>';
									}
									
									
									
									echo '</div>';
								echo '</div>';
								
								if($break > 99){
										echo '</div>';
										echo '<div class="clear"></div>';
									echo '</div>';
									
									if ($countbreaks % 2 == 0) {
										echo '<div class="moduleSeparator odd">';
											echo '<div class="coll-100">';
												
									}else{
										echo '<div class="moduleSeparator">';
											echo '<div class="coll-100">';
									}
									$break = 0;
									$countbreaks++;
								}

							//echo $portfolio->$moduletemplate['function']('sdfdsf');
							}else{
								echo 'Methode niet gevonden!';
							}
						}
						
						echo '</div>';
						
						echo '<div class="clear"></div>';
						echo '</div>';
						
						
					echo '</div>';
				}else{
					//Gebruiker is geen leerling.
					echo '<div id="containerOuter">';
						echo '<div id="containerInner">';
							echo 'Gebruiker niet gevonden.';
						echo '</div>';
					echo '</div>';
				}
			}else{
				//Gebruiker niet gevonden.
				echo '<div id="containerOuter">';
					echo '<div id="containerInner">';
						echo 'Gebruiker niet gevonden.';
					echo '</div>';
				echo '</div>';
			}
		}else{
			//Portfolio niet gevonden.
			echo '<div id="containerOuter">';
				echo '<div id="containerInner">';
					echo 'Portfolio niet gevonden.';
				echo '</div>';
			echo '</div>';
		}
	}
	
		
	function addmodule(){
		global $user;
		global $dbc;
	
		//echo "Gebruiker is ingelogd";
		if($user->isLoggedIn()){

			echo '<div id="containerOuter">';
				echo '<div id="containerInner">';
					echo '<h1>Module toevoegen</h1>';
				
					echo '<p>Selecteer een module die u wilt toevoegen, deze word dan direct toegevoegd.</P>';
				
					if(!isset($_GET["id"])){
						$moduleTemplates = $dbc->prepare('SELECT * FROM `moduletemplate`');
						$moduleTemplates->execute();
						$moduleTemplates = $moduleTemplates->fetchAll(PDO::FETCH_ASSOC);
						
						if(!empty($moduleTemplates)){

							echo '<div class="coll-100">';
								$count = 0;
								foreach($moduleTemplates as $moduleTemplate){
								
									if($count == 3){
										echo '<div class="clear"></div>';
										$count = 0;
									}
									
									echo '<div class="coll-33 selectModule">';
										
										echo '<h3 class=>'.$moduleTemplate['name'].'</h2>';
										echo $moduleTemplate['description'];
										echo '<br>';
										echo '<br>';
										
										echo '<a href="index.php?p=addmodule&id='.$moduleTemplate['id'].'" class="btn btn-default" role="button">Toevoegen</a>';
									echo '</div>';
									
									$count++;
								}
							echo '</div>';
							echo '<div class="clear"></div>';
						}
					}else{
						$id = htmlentities($_GET["id"]);
						
						$moduleUser = $dbc->prepare('SELECT * FROM `module` WHERE `portfolioid` = "'.$user->get()['id'].'"');
						$moduleUser->execute();
						$moduleUser = $moduleUser->fetchAll(PDO::FETCH_ASSOC);
						
						$moduleTemplates = $dbc->prepare('SELECT * FROM `moduletemplate` WHERE `id` = "'.$id .'"');
						$moduleTemplates->execute();
						$moduleTemplates = $moduleTemplates->fetchAll(PDO::FETCH_ASSOC);
						
						
						if(!empty($moduleTemplates)){
							$moduleTemplates = $moduleTemplates[0];
							
							$position = -1;
							foreach ($moduleUser as &$module) {
								if ($module['position'] >= $position){
									$position = $module['position'];
								}
							}
							
							$position = $position + 1;
						}else{
							$position = 0;
						}
						

						
						$insert = "INSERT INTO `module` (portfolioid, moduleid, position, size, input, timestamp)
						VALUES (".$user->get()['id'].",".$id.",".$position.",100,' ',".TIME().")";
						
						$dbcInsert = $dbc->prepare($insert);
						$dbcInsert->execute();
						
						
						if($dbcInsert){
							$lastId = $dbc->lastInsertId();
						
							echo '<div class="alert alert-success">
							  <strong>Success!</strong> De module is toegevoegd!.
							</div>';
							
							header('Location: index.php?p=editmodule&m='.$lastId.'&add');
							
						}else{
							echo '<div class="alert alert-danger">
							  <strong>:(</strong> Er is iets fout gegaan probeer het later opnieuw.
							</div>';
						}
						
						
						
					}
					
					echo '<div class="clear"></div>';

				echo '</div>';
			echo '</div>';
		}else{ // "Gebruiker is niet ingelogd";
			echo '<div id="containerOuter">';
				echo '<div id="containerInner">';
					echo 'U bent niet ingelogd.';
				echo '</div>';
			echo '</div>';
		}
	}
	
	
	//functie voor het editen van een module
	function editmodule(){
		global $dbc;
		global $core;
		global $portfolio;
		global $user;
		
		if(isset($_GET["m"])){
				$moduleId = htmlspecialchars($_GET["m"]);
				$moduleId = preg_replace("/[^0-9,.]/", "", $moduleId);
				
				$userID = $user->get()['id']; //loged in userID

				$module = $dbc->prepare('SELECT * FROM `module` WHERE `id` = "'.$moduleId.'" AND `portfolioid` = '.$userID.' LIMIT 1');
				$module->execute();
				$module = $module->fetchAll(PDO::FETCH_ASSOC);

				//print_r($moduletemplate );
				if(!empty($module)){
					$module = $module[0];
					echo '<div id="containerOuter">';
						echo '<div id="containerInner">';
							//verwerken van POST (module aanpassen)
						
							echo '<h1>Aanpassen Module</h1>';
						
							//Als het een nieuwe module is
							if(isset($_GET["add"])){
								echo '<div class="alert alert-success">';
								  echo '<strong>success!</strong> Module is toegevoegd.';
								echo '</div>';
							}
						
						
							if(isset($_POST['Submit'])){
								$input = ''; 
								
								//De elementen van de module worden genummerd opgestuurd.
								for ($x = 0; $x < 10; $x++) {
									if(isset($_POST[$x])){
										//Replace commas
										$input .= ','.str_replace(",","、",htmlspecialchars($_POST[$x]));
									}
								}
								//replace '
								$input = str_replace("'","`",$input);
								
								//eerste comma verwijderen
								$input = substr($input, 1);
								
								$size = 100; //Standaard breete
								if(isset($_POST['size'])){		
									$size = htmlspecialchars($_POST['size']);
									$size = preg_replace("/[^0-9,.]/", "", $size);
								}
								
								$sql = "UPDATE `module` SET `input`='".$input."',`size`='".$size."'  WHERE id=".$moduleId;
								
								$update = $dbc->prepare($sql);
								$update->execute();
								
								//berichtgeving
								
								
								if($update == true){
									echo '<div class="alert alert-success">';
									  echo '<strong>success!</strong> Module is aangepast.';
									echo '</div>';
								}else{
									echo '<div class="alert alert-success">';
									  echo '<strong>:(</strong> Er is een fout ondstaan, probeer het later nog eens.';
									echo '</div>';
								}
							}
						
							//ophalen module
							$module = $dbc->prepare('SELECT * FROM `module` WHERE `id` = "'.$moduleId.'" AND `portfolioid` = '.$userID.' LIMIT 1');
							$module->execute();
							$module = $module->fetchAll(PDO::FETCH_ASSOC)[0];
							
							$moduletemplate = $dbc->prepare('SELECT * FROM `moduletemplate` WHERE `id` = "'.$module['moduleid'].'" LIMIT 1');
							$moduletemplate->execute();
							$moduletemplate = $moduletemplate->fetchAll(PDO::FETCH_ASSOC)[0];
						
							/* echo '<pre>';
							print_r($module);
							echo '</pre>';
							echo '<pre>';
							print_r($moduletemplate);	
							echo '</pre>'; */
							
							
							
							//URL van deze gebruiker
							$url = $core->getPortfolioURL($module['portfolioid']);

							
							echo '<div class="coll-75">';
								echo '<h2>'.ucfirst($moduletemplate['name']).'</h2>';
								echo '<p>'.ucfirst($moduletemplate['description']).'</p>';
								
								$inputs = explode(",", $module['input']);
								$fields = explode(",", $moduletemplate['field']);
								$titles = explode(",", $moduletemplate['fieldTitle']);
								
								echo '<div class="coll-90">';
									echo '<form action="?p=editmodule&m='.$moduleId.'" method="post">';
									
										//Voeg alle inputvelden toe die bij deze module horen.
										
										for ($x = 0; $x < count($fields); $x++) {
											if(!empty($fields[$x])){
												if(!isset($inputs[$x])){
													$inputs[$x] = '';
												}
											
												echo $core->input($fields[$x],$titles[$x],$x,$inputs[$x]);
											}
										} 
										
										//Range slider voor breete
										echo '<div class="form-group">';
											echo '<label>Breedte van de module:</label>';
											echo '<input type="range" name="size" min="0" max="100" step="5" value="'.$module['size'].'">';
										echo '</div>';
						
										echo '<input type="submit" class="btn btn-default" type="submit" name="Submit" value="Verstuur">';
									echo '</form>';
								echo '</div>';
							echo '</div>';
							
							echo '<div class="coll-25">';
								echo '<h2>Portfolio Layout</h2>';
								

									
									echo '<div class="coll-100">';
										//Portfolio layout met links tonen.
										$core->portfoliolayout($module['portfolioid'], $moduleId);
									echo '</div>';
			
									
									echo '<div class="clear"></div>';

								echo '<div class="clear"></div>';
							echo '</div>';
							
							echo '<div class="clear"></div>';
							
							//Tonen van de uitkomst
							echo '<h2>Live module</h2>';
							echo '<p>Dit is hoe de module eruit ziet op uw portfolio!</p>';
							
							if (method_exists($portfolio,$moduletemplate['function'])){
								//echo 'Function Found';
								$input = explode(",", $module['input']);
								$fields = explode(",", $moduletemplate['field']);
								
								/* print_r($moduletemplate);*/
								echo '<div class="coll-100 borderSmall">';
									echo '<div class="module coll-'.$module['size'].'">';
										echo '<div class="contentMargin">';
										if(count($input) == count($fields)){
											if(count($input) == 1){
												echo $portfolio->$moduletemplate['function']($input[0]);
											}else if(count($input) == 2){
												echo $portfolio->$moduletemplate['function']($input[0], $input[1]);
											}
										}else{
											echo 'Aantal inputs komt niet overeen met het aantal benodigde velden.';
										}
										echo '</div>';
									echo '</div>';
									
									echo '<div class="clear"></div>';
								echo '</div>';
							//echo $portfolio->$moduletemplate['function']('sdfdsf');
							}else{
								echo 'Methode niet gevonden!';
							}
							echo '<div class="clear"></div>';
						echo '</div>';
					echo '</div>';
				}else{
					echo '<div id="containerOuter">';
						echo '<div id="containerInner">';
							echo 'Module niet gevonden.';
						echo '</div>';
					echo '</div>';
				}	
		}else{
			//Portfolio niet gevonden.
			echo '<div id="containerOuter">';
				echo '<div id="containerInner">';
					echo 'Module niet gevonden.';
				echo '</div>';
			echo '</div>';
		}
	
	}
	
	function portfoliooverzicht(){
		global $user;
		global $mysqlconn;
		global $core;
		
		echo '<div id="containerOuter">';
		echo '<div id="containerInner">';
		
		echo "<h1>Portfolio Overzicht</h1>";
		
		echo '<a class="button" href="index.php?p=addmodule">Voeg een module toe.</a>';
		
	
		
		$dbc = $mysqlconn;
		$DBName = "md253219db370063";
		if(!mysqli_select_db ($dbc, $DBName)){
			echo "<p>There is no database...</p>";
		}else{
			if(isset ($_POST["up"]) && ($_POST["ID"])){	
				//Getting the position of the selected row
				$UID = $_POST["ID"];
				$changed = $_POST["ID"];

				$getposition = "SELECT position FROM module WHERE id = $UID LIMIT 1";

				$result = mysqli_query($dbc, $getposition);

				$fetch = mysqli_fetch_array($result);
				//Selecting the position and calculate the new position for the row that needs to be placed down
				$initialpos = $fetch['position'];
				$up = $initialpos - 1;

				//$moveup = "UPDATE module SET position = $initialpos WHERE position = $up; UPDATE module SET position = $up WHERE id = $UID;"; Initial query.
				//The query's below are a slpit up of the above because otherwise it doesn't work.
				$moveup = "UPDATE module SET position = $initialpos WHERE position = $up;";
				$moveup2 = "UPDATE module SET position = $up WHERE id = $UID;";

				mysqli_query($dbc, $moveup);
				mysqli_query($dbc, $moveup2);
				
				//Checking if the query's are working
				if(mysqli_query($dbc, $moveup) == TRUE){

				}else{
						echo mysqli_error($dbc);
				}

				if(mysqli_query($dbc, $moveup2) == TRUE){
				}else{
						echo mysqli_error($dbc) . "2";
				}

			}elseif(isset ($_POST["down"]) && ($_POST["ID"])){
			//Getting the position of the selected row
				$UID = $_POST["ID"];
				$changed = $_POST["ID"];
				
				$getposition = "SELECT position FROM module WHERE id = $UID LIMIT 1";

				$result = mysqli_query($dbc, $getposition);

				$fetch = mysqli_fetch_array($result);
				//Selecting the position and calculate the new position for the row that needs to be placed down
				$initialpos = $fetch['position'];
				$down = $initialpos + 1;

				$moveup = "UPDATE module SET position = $initialpos WHERE position = $down;";
				$moveup2 = "UPDATE module SET position = $down WHERE id = $UID;";

				mysqli_query($dbc, $moveup);
				mysqli_query($dbc, $moveup2);

				//Checking if the query's are working
				if(mysqli_query($dbc, $moveup) == TRUE){

				}else{
						echo mysqli_error($dbc);
				}

				if(mysqli_query($dbc, $moveup2) == TRUE){
				}else{
						echo mysqli_error($dbc) . "2";
				}								
			}elseif(isset($_POST["delete"]) && ($_POST["ID"])){
				//Getting the position of the selected row
				$UID = $_POST["ID"];

				$getposition = "SELECT position FROM module WHERE id = $UID LIMIT 1";

				$result = mysqli_query($dbc, $getposition);

				$fetch = mysqli_fetch_array($result);
				
				$initialpos = $fetch['position'];
				
				//Getting the last position out of the DB 
				$getlastpos = "SELECT count(*) - 1 AS maxpos FROM module WHERE portfolioid = ".$user->get()['id'].";";
			
				$lastposresult = mysqli_query($dbc, $getlastpos);
		
				$fetchlastpos = mysqli_fetch_array($lastposresult);
		
				$lastposition = $fetchlastpos['maxpos'];
				
				//Checking if you want to delete the last position, if so then we don't need an update
				if($initialpos === $lastposition){
					
				//Deleting the row that needs to be deleted
				$deletemodule = "DELETE FROM module WHERE id = $UID;";

				mysqli_query($dbc, $deletemodule);
				
				//echo $deletemodule;
					
				}else{
				//Deleting the row that needs to be deleted
				$deletemodule = "DELETE FROM module WHERE id = $UID;";

				mysqli_query($dbc, $deletemodule);
				
				//echo $deletemodule;
				
				//Updating all the above rows of the deleted modules
				$updaterows = "UPDATE module SET position = position - 1 WHERE position > $initialpos;";
				
				mysqli_query($dbc, $updaterows);
				
				//echo $updaterows;
				}
			}
			//The portfolioID is required to retrieve the correct modules for this user
			$portfolioid = $user->get()['id'];
			//Generating the overview
			$Generateoverview = "SELECT module.id, module.portfolioid, module.moduleid, module.position, moduletemplate.name FROM module, moduletemplate WHERE portfolioid = ".$portfolioid." AND module.moduleid = moduletemplate.id ORDER BY position ASC;";
		
			$result = mysqli_query($dbc, $Generateoverview);
			if (mysqli_num_rows($result) == 0){
					echo "<p>You have no modules yet!</p>"; 
			}else{
					echo '<div class="coll-100">';
						echo '<div class="coll-75">';
							echo '<div class="coll-90">';
							echo '<h2>Modules</h2>';
							echo "<table width='100%' class='table table-striped'>";
							echo "<thead><tr><th>Position</th>
									  <th>UP/DOWN</th>

									  <th>Input</th>
									  <th>EDIT</th><th>DELETE</th></tr></thead><tbody>";
							//Getting the last position out of the database
							$getlastpos = "SELECT count(*) - 1 AS maxpos FROM module WHERE portfolioid = ".$user->get()['id'].";";
							
							$lastposresult = mysqli_query($dbc, $getlastpos);
							
							$fetchlastpos = mysqli_fetch_array($lastposresult);

							$lastposition = $fetchlastpos['maxpos'];
							
							while($row = mysqli_fetch_assoc($result)){
							
								$ID = $row['id'];
								//Printing the table

								if(!isset($changed)){
									$changed = '';
								}
								
								if($ID == $changed){
									echo "<tr class='changed'><td>{$row['position']}</td>";
								}else{
									echo "<tr><td>{$row['position']}</td>";
								}
								
								if(mysqli_num_rows($result) > 1){
									if($row['position'] == 0){
									echo '<td><form action="#" method="POST">
													<input type="submit"  name="down" value="Down" />
													<input type="hidden" name="ID" value="'. $ID .'" />
													</form></td>';	
									}elseif($row['position'] > 0 && $row['position'] < $lastposition){
									echo '<td><form action="#" method="POST">
													<input type="submit" name="up" value="Up" /><input type="submit"  name="down" value="Down" />
													<input type="hidden" name="ID" value="'. $ID .'" />
													</form></td>';
									}elseif(($row['position'] == $lastposition)){
									echo '<td><form action="#" method="POST">
													<input type="submit" name="up" value="Up" />
													<input type="hidden" name="ID" value="'. $ID .'" />
													</form></td>';	
									}
								}else{
									echo '<td></td>';
								}
								
								echo "<td>{$row['name']}</td>";
								echo '<td><p><a href="index.php?p=editmodule&m='. $ID .'">Edit</a></p></td>';
								echo '<td><form action="#" method="POST"><input type="submit" name="delete" value="Delete" />
												<input type="hidden" name="ID" value="'. $ID .'" /></form></td></tr>';
								
								
							}
							echo "</tbody></table>";
							echo "</div>";
						echo "</div>";
						
						echo '<div class="coll-25">';
							echo '<h2>Portfolio Layout</h2>';
							
							$core->portfoliolayout($user->get()['id']);
						echo '</div>';
						
					echo "</div>";
				}
		}
			// Selecting and setting the portfolio colours
			if(isset ($_POST["sendcolours"])){
				$updmaincolour = substr($_POST["maincolour"],1);
				$updsecondarycolour = substr($_POST["secondarycolour"],1);
				$updtertiarycolour = substr($_POST["tertiarycolour"],1);
				
				$updatecolours = "UPDATE portfolio SET colour = '$updmaincolour', secondarycolour = '$updsecondarycolour', tertiarycolour = '$updtertiarycolour' WHERE userid = $portfolioid;";
				
				//echo $updatecolours;  <-- Used for testing the Query
				mysqli_query($dbc, $updatecolours);
			}	
			
			//Getting the colors out of the database
			
				$retrievecolours = "SELECT colour, secondarycolour, tertiarycolour FROM portfolio WHERE userid = $portfolioid;";
				
				$colourinsert = mysqli_query($dbc, $retrievecolours);
					
				$fetchcolours = mysqli_fetch_array($colourinsert);
					
				$maincolour = $fetchcolours['colour'];
				$secondarycolour = $fetchcolours['secondarycolour'];
				$tertiarycolour = $fetchcolours['tertiarycolour'];
				echo '<div class="clear"></div>';
				echo '<form action="#" method="POST">
				<p>Maincolour <input type="color" name="maincolour" value="#'. $maincolour .'"></p>
				<p>Secondarycolour <input type="color" name="secondarycolour" value="#'. $secondarycolour .'"></p>
				<p>Tertiarycolour <input type="color" name="tertiarycolour" value="#'. $tertiarycolour .'"></p>
				<input type="submit" name="sendcolours" value="Update" /></form>';
				
		
		
		echo '</div>';
		echo '</div>';
	}

	function overzichtUploads(){
		global $user;
		global $dbc;
		global $core;
		
		echo '<div id="containerOuter">';
		echo '<div id="containerInner">';
		echo '<h1>Bestanden Overzicht</h1>';
		
		$uploads = new Uploads;

		$id = $user->get()['id'];
		$levelid = $user->get()['levelid'];
		$slb = $user->get()['slb'];
		if(isset($_POST['submit'])){
			if($id == $slb AND $levelid == 2){
				$stmt = $dbc->prepare("SELECT id, firstname, lastname FROM user WHERE levelid = 1 AND slb = $slb");
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach($result AS $row){
					$useridcreator = $row['id'];
					foreach($uploads->getUserUploads($row['id'], TRUE) AS $upload){
						$stmt2 = $dbc->prepare("SELECT * FROM approved WHERE approvedid = :uploadid");
						$stmt2->bindParam(":uploadid", $upload['id']);
						$stmt2->execute();
						$approvedresult = $stmt2->fetch(PDO::FETCH_ASSOC);
						$uploadid = $upload['id'];
						if(!empty($_POST[$uploadid])){		
							$note = $_POST[$uploadid];
							$stmt3 = $dbc->prepare("INSERT INTO approved (approvedid, useridcreator, useridgrader, note) 
													VALUES ($uploadid, $useridcreator, $id, '$note')");
							$stmt3->bindParam(":uploadid", $upload['id']);
							$stmt3->execute();
						}
					}
				}
			}
			//URL MOET ANDERS WORDEN!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			header("Refresh:0; url=index.php?p=overzichtUploads");
		}
		if($id == $slb AND $levelid == 2){
			echo "<form method='post' action='#'>";
			$stmt = $dbc->prepare("SELECT id, firstname, lastname FROM user WHERE levelid = 1 AND slb = $slb");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$count = 0;
			foreach($result AS $row){
				$count++;
			
				echo '<b>';
				echo $row['firstname'] . " " 
				. $row['lastname'] . "<br />";
				echo '</b>';
				
				if(!empty($uploads->getUserUploads($row['id'], TRUE))){
					foreach($uploads->getUserUploads($row['id'], TRUE) AS $upload){
						echo "<p>" . $upload['fileicon'] . " " . $upload['name'];
						echo "<br />";
						echo $upload['description'];
						echo "</p>";
						$stmt2 = $dbc->prepare("SELECT * FROM approved WHERE approvedid = :uploadid");
						$stmt2->bindParam(":uploadid", $upload['id']);
						$stmt2->execute();
						$approvedresult = $stmt2->fetch(PDO::FETCH_ASSOC);
						if($stmt2->rowCount() > 0){
							echo '<i>Commentaar: </i>';
							echo $approvedresult['note'];
						}else{
							$uploadid = $upload['id'];
							echo "<textarea placeholder='Commentaar' name=$uploadid></textarea>";
						}
					}
				}else{
						echo 'Deze gebruiker heeft geen bestanden.';
				}
				
				echo "<hr>";
			}
			
			if($count == 0){
					echo '<div class="alert alert-danger">
					  <strong>:(</strong> Er zijn geen bestanden geupload.
					</div>';
			}else{
				echo "<input type='submit' name='submit' value='Waarmerken'>";
			}
			
			echo "</form>";
		}elseif($levelid == 3){
			$stmt = $dbc->prepare("SELECT id, firstname, lastname FROM user WHERE levelid = 1");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($result AS $row){
				echo '<b>';
			
				echo $row['firstname'] . " " 
				. $row['lastname'] . "<br />";
				echo '</b>';
		
				
				if(!empty($uploads->getUserUploads($row['id']))){
					foreach($uploads->getUserUploads($row['id']) AS $upload){
						echo "<p>" . $upload['fileicon'] . " " . $upload['name'];
						echo "<br />";
						echo $upload['description'];
						echo "</p>";
						$stmt2 = $dbc->prepare("SELECT * FROM approved WHERE approvedid = :uploadid");
						$stmt2->bindParam(":uploadid", $upload['id']);
						$stmt2->execute();
						$approvedresult = $stmt2->fetch(PDO::FETCH_ASSOC);
						if($stmt2->rowCount() > 0){
							echo '<i>Commentaar: </i>';
							echo $approvedresult['note'];
						}else{
							$uploadid = $upload['id'];
							echo "Not approved";
						}
					}
				}else{
						echo 'Deze gebruiker heeft geen publieke bestanden.';
				}
				echo "<hr>";
			}
		}
	
		echo '</div>';
		echo '</div>';
	}

	function showUploads(){
			global $dbc;
			global $user;

			echo '<div id="containerOuter">';
			echo '<div id="containerInner">';

			echo '<h1>Overzicht bestanden</h1>';

			$uploads = new Uploads;
			// Wanneer een get request wordt gedaan om een bestand te verwijderen.
			if(isset($_GET['id'])){
					if(isset($_GET['action'])){
							if($_GET['action'] == "remove"){
									if($uploads->hasRemovePermission($_GET['id'])){
											unlink($uploads->getFileLocationById($_GET['id']));
											if($uploads->deleteFile($_GET['id'])){
													header("Location: ?p=showUploads" . $url);
											}
									}else{
										echo '<div class="alert alert-danger">
										  <strong>:(</strong> U heeft geen rechten voor deze actie.
										</div>';
									}
							}
					}
			}
			if(isset($_POST['saveChanges'])){
					foreach($uploads->getUserUploads($user->get()['id']) as $id){
							if($_POST['public_' . $id['id']] == "non_public"){
									$records[$id['id']] = 0;
							}else if($_POST['public_' . $id['id']] == "public"){
									$records[$id['id']] = 1;
							}
					}
					if($uploads->updateFile($records)){
							echo "<div class='alert alert-success alert-dismissible' role='alert'><strong>Succes!</strong> Wijzigingen zijn opgeslagen.</div>";
					}else{
							echo "<div class='alert alert-danger' role='alert'><strong>Oops!</strong> De wijzingen konden niet worden opgeslagen, probeer het later opnieuw.</div>";
					}
			}
			$hasFiles = $uploads->getUserUploads($user->get()['id']);

			echo
			"
			<div class='table-responsive'>
				<form action='#' method='post'>
					<table class='table table-bordered'>";
						if($hasFiles){
								echo "<tr>
									<th>Bestandsnaam</th>
									<th>Beschrijving</th>
									<th>Publiekelijk</th>
									<th>Download</th>
									<th>Verwijder</th>
								</tr>";
						}
						if($uploads->getUserUploads($user->get()['id'])){
								$arrayLength = count($uploads->getUserUploads($user->get()['id']));
								foreach($uploads->getUserUploads($user->get()['id']) as $upload){
										$id = $upload['id'];
										$extension = $upload['extension'];
										$fileIcon = $upload['fileicon'];
										if(empty($fileIcon)) $fileIcon = "<i class='fa fa-file-o' aria-hidden='true'></i>";
										$name = $upload['name'];
										$description = $upload['description'];
										$downloadUrl = $upload['url'];
										$public = $upload['public'];
										echo
										"<tr>
											<td>$fileIcon $name</td>
											<td>$description</td>
											<input type='hidden' name='public_$id' value='non_public'>";
											if($public)
													echo "<td><input type='checkbox' name='public_$id' value='public' checked></td>";
												else
													echo "<td><input type='checkbox' name='public_$id' value='public'></td>";
										echo
											"<td><a href='$downloadUrl'><i class='fa fa-download' aria-hidden='true'></i> .$extension</a></td>
											<td><a href='?p=showUploads&id=$id&action=remove'><i class='fa fa-trash' aria-hidden='true'></i></a></td></tr>";
								}
								echo "<input type='hidden' name='size' value='$arrayLength'>";
						}else{
								echo "<div class='alert alert-warning' role='alert'><strong>Oh nee!</strong> Er zijn nog geen bestanden geüpload.</div>";
						}
						echo
						"</table>";
						if($hasFiles){
								echo "<button type='submit' name='saveChanges' class='btn btn-info'>Wijzigingen opslaan</button>";
						}
				echo "</form>
			</div>";

			echo "</div>";
			echo "</div>";
	}
	
	// Functie voor het uploaden van files
	function uploadFile(){
			global $dbc;
			global $user;
			
			echo '<div id="containerOuter">';
			echo '<div id="containerInner">';
			
			echo '<h1>Bestand Uploaden</h1>';
			
			$userID = $user->get()['id'];
			
			if(isset($_POST["upload"])){
					if(!empty($_POST['fileName']) && !empty($_POST['fileDescription']) && !empty($_FILES['fileToUpload']['name'])){
							$name = stripslashes($_POST['fileName']);
							$description = stripslashes($_POST['fileDescription']);
							$uploads = new Uploads;
							if($uploads->uploadFile($_FILES["fileToUpload"], $name, $description) == "OK"){
									//header("Location: " . $_POST["previous_page"]);
									echo "<div class='alert alert-success' role='alert'><strong>Succes!</strong> Uw bestand is geüpload</div>";
							}else if($uploads->uploadFile($_FILES["fileToUpload"], $name, $description) == "FILE_EXISTS"){
									echo "<div class='alert alert-danger' role='alert'><strong>Oh nee!</strong> Een bestand met die naam bestaat al!</div>";
							}else if($uploads->uploadFile($_FILES["fileToUpload"], $name, $description) == "FILE_NOT_ALLOWED"){
									echo "<div class='alert alert-danger' role='alert'><strong>Oops!</strong> U bent niet bevoegd om bestanden met deze extensie te uploaden.</div>";
							}else{
									echo "<div class='alert alert-warning' role='alert'><strong>Oops!</strong> Het bestand kon niet worden geüpload, probeer het later opnieuw.</div>";
							}
					}else{
							echo "<div class='alert alert-danger' role='alert'><strong>Mislukt!</strong> Je moet elk veld invullen en/of een bestand selecteren.</div>";
					}
			}
			echo
				"<form action='#' method='post' enctype='multipart/form-data'>
					<div class='form-group'>
		    		<label for='fileName'>Bestandsnaam</label>
						<input type='text'' class='form-control' id='fileName' name='fileName' placeholder='Powerpoint Project'>
					</div>
					<div class='form-group'>
		    		<label for='fileDescription'>Beschrijving</label>
						<input type='text'' class='form-control' id='fileDescription' name='fileDescription' placeholder='Een presentatie van het school project'>
					</div>
					<div class='form-group'>
				    <label for='fileUpload'>Bestand</label>
				    <input type='file' id='fileToUpload' name='fileToUpload'>
				    <p class='help-block'>Selecteer hier boven het bestand dat u wilt uploaden.</p>
  				</div>
		    		<button type='submit' name='upload' class='btn btn-info'>Upload</button>
				</form>";
				
			echo '</div>';
			echo '</div>';
	}
	
	function profile() {
        global $dbc;
        global $user;
				$message = "";
				if(isset($_GET['message'])){
						$message = "<div class='alert alert-success'><strong>Succes </strong> U heeft uw profiel aangepast.</div>";
				}

				if(isset($_POST['submit'])){
						if (isset($_POST['address']) || (isset($_POST['zipcode'])) || (isset($_POST['phone'])) || (isset($_POST['newslb']))) {
								if(Validate::zipcode($_POST['zipcode']) && Validate::phone($_POST['phone'])){
										$id = $user->get()['id'];
										$adress = htmlentities($_POST['address']);
										$zipcode = htmlentities($_POST['zipcode']);
										$phone = htmlentities($_POST['phone']);
										$slb = htmlentities($_POST['newslb']);

										$query = "UPDATE user SET address='$adress', zipcode='$zipcode', phone='".$phone."', slb='$slb' WHERE id='$id'";
										$result = $dbc->prepare($query);
										$result->execute();
										header("Location: ?p=profile&message=succes");
								}else{
										$message = "<div class='alert alert-danger'><strong>:( </strong> U heeft geen geldige postcode of telefoonnummer ingevuld.</div>";
								}
						}else{
								$message = "<div class='alert alert-danger'><strong>:( </strong> U dient elk veld in te vullen.</div>";
						}
				}

        $query = "SELECT * from user WHERE id=" . $user->get()['slb'];
        $getSLB = $dbc->prepare($query);
        $getSLB->execute();
        $getSLB = $getSLB->fetchAll(PDO::FETCH_ASSOC);
				if (!empty($getSLB[0])) {
            $getSLB = $getSLB[0];
            $slber = $getSLB['firstname'] . " " . $getSLB['lastname'];
        } else {
            $slber = "Geen";
        }

				echo
				"
					<div class='container'>
						<div class='row'>
							<br>
							<h1>Uw profiel</h1>
							{$message}
							<table class='table table-striped'>
								<thead>
									<tr>
										<th>Voornaam</th>
										<th>Achternaam</th>
										<th>Adres</th>
										<th>Postcode</th>
										<th>Telefoonnummer</th>
										<th>Emailadres</th>
										<th>Studieloopbaanbegeleider</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>" . $user->get()['firstname'] . "</td>
										<td>" . $user->get()['lastname'] . "</td>
										<td>" . $user->get()['address'] . "</td>
										<td>" . $user->get()['zipcode'] . "</td>
										<td>" . $user->get()['phone'] . "</td>
										<td>" . $user->get()['email'] . "</td>
										<td>" . $slber . "</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				";

        echo
				"
					<div class='container'>
						<div class='row'>
							<div class='col-s12'>
								<form id='button' method='POST' action='?p=editprofile'>
									<button type='submit' class='btn btn-success' data-toggle='dropdown' type='submit' name='submit'>Aanpassen</button>
								</form>
							</div>
        		</div>
					</div>
				";
    }

    function editprofile() {
        global $mysqlconn;
        global $user;

				$query = "SELECT id, firstname, lastname FROM user WHERE id = slb AND levelid = 2";
        $result = mysqli_query($mysqlconn, $query);
        $query2 = "SELECT slb FROM user WHERE id = " . $user->get()['id'] . "";
        $result2 = mysqli_query($mysqlconn, $query2);
        $fetch2 = mysqli_fetch_assoc($result2);
        $currentslb = $user->get()['slb'];

				echo
				"
					<div class='container'>
						<div class='row'><h1>Profiel Aanpassen</h1></div>
						<div class='row'><p>Hier kun je jouw profiel aanpassen als je verhuist bent of van telefoon nummer veranderd bent.</p></div>
							<form method='post' action='index.php?p=profile'>
								<div class='row'>
									Adres
									<input style='width:100%' name='address' type='text' value=" . $user->get()['address'] . " required>
								</div>
								<br>
								<div class='row'>
									Postcode
									<input style='width:100%' name='zipcode' type='text' value=" . $user->get()['zipcode'] . " required>
								</div>
								<br>
								<div class='row'>
									Telefoonnummer
									<input style='width:100%' name='phone' type='text' value=" . $user->get()['phone'] . " required>
								</div>";

								if(($user->get()['id'] != $user->get()['slb']) && $user->get()['levelid'] != 3 && $user->get()['levelid'] != 2){
										echo "<br><div class='row'>
											Studieloopbaanbegeleider<br>
											<select name='newslb'>
												<option value='0'>Geen SLBer</option>";
												while ($fetch = mysqli_fetch_assoc($result)) {
														if ($fetch['id'] == $fetch2['slb']) {
																$selected = " selected='selected'";
														} else {
																$selected = FALSE;
														}
														echo "<option value='" . $fetch['id'] . "' $selected'>" . $fetch['firstname'] . " " . $fetch['lastname'] . "</option>";
												}
												echo "
											</select>
										</div>";
								}

								echo "<br>
								<div class='row'><button type='submit' class='btn btn-success' data-toggle='dropdown' type='submit' name='submit'>Aanpassen</button></div>
							</form>
						</div>
					</div>
				";
    }
	
	//404
	function chat(){
		global $core;
		global $pages;
		global $dbc;
		global $user;
		
		$stmt = $dbc->prepare("SELECT * FROM chat, user WHERE user.id = chat.userid AND chat.targetid = 0 ORDER BY  `chat`.`timestamp` DESC LIMIT 20");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		echo '<div class="refresh"><a href="chat.php"><i class="fa fa-refresh" aria-hidden="true"></i></a></div>';
		
		
		echo '<table class="chatbox">';
		
		
		$result = array_reverse($result);
		
		foreach ($result as &$message) {
			echo '<tr>';
				echo '<td class="firstname" style="color:#'.$core->stringToColorCode($message['firstname']).';" green;">';
					echo $message['firstname'].':';
				echo '</td>';
				echo '<td class="message">';
					echo htmlentities($message['message']);
				echo '</td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		//print_r($result);
	}
	
	function notfound(){
		echo ' ';
	}
}
?>