<?php
//Een class met al mijn paginas als functies
class Pages {  

	
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
	
		// $levelID is het niveau wat aan de user mee gegeven wordt, 1 student, 2 docent en 3 admin.
		$levelID = 1;
		// $hasPortfolio is het niveau wat wordt meegegeven wanneer de gebruiker een portfolio heeft, 1 voor ja 0 voor nee.
		$hasPortfolio = 1;
		// $isSLB geeft aan of een docent een studieloopbaanbegeleider is, 1 voor ja 0 voor nee.
		$isSLB = 1;
		// Deze variabelen zijn voor de linkjes naar de bijbehorende paginas.
		$viewFiles = "linkie1";
		$uploadFiles = "linkie2";
		$viewPortfolio = "linkie3";
		$editPortfolio = "linkie4";
		$createPortfolio = "linkie5";
		$overviewPort = "linkie6";
		$overviewFiles = "linkie7";
		$guidedStudents = "linkie8";
		// checked of levelID het ID van een student is.
		if($levelID === 1){
			// maakt de array aan met de navigatie structuur.
			$menu = array(
				"Bekijk jou bestanden" => $viewFiles,
				"Upload jou bestanden" => $uploadFiles,
			);
			// checked of de leerling een portfolio heeft.
			if($hasPortfolio === 1){
				// voegt extra opties toe voor leerling met portfolio.
				$menu += array(
					"Bekijk jou portfolio" => $viewPortfolio,
					"Bewerk jou portfolio" => $editPortfolio
				);
			// checked of de leerling geen portfolio heeft.
			}elseif($hasPortfolio === 0){
				// voegt extra opties toe voor leerling zonder portfolio.
				$menu += array(
					"Creëer een portfolio" => $createPortfolio
				);
			}
		}
		// checked of levelID het ID van een docent is //
		if($levelID === 2){
			// maakt de array aan met de navigatie structuur //
			$menu = array(
				"Overzicht van portfolios" => $overviewPort,
				"Overzicht van bestanden" => $overviewFiles
			);
			// checked of de docent een studieloopbaanbegeleider is //
			if($isSLB === 1){
				// voegt extra menu opties toe voor SLBers //
				$menu += array(
					"Overzicht van begeleide studenten" => $guidedStudents
				);
			}
		}
		if($levelID === 3){
			$menu = array(
				"Overzicht van portfolios" => $overviewPort,
				"Overzicht van bestanden" => $overviewFiles,
				"Overzicht van gegevens studenten" => $guidedStudents
			);
		}
		/* echo "<pre>";
		var_export($menu);
		echo "</pre>"; */
		//je krijgt een lijst doormiddel van deze foreach:
		
		echo "<ul>";
		foreach($menu as $optionDesc => $option){
			echo "<li><a href=$option>$optionDesc</a></li>";
		}
		echo "</ul>";
	}
	
	function footer(){
		echo 'FOOTER';
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
	
	//Laten zien van portfolio
	function portfolio(){
		global $dbc;
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
				
				/* echo '<pre>';
				print_r($requestedPortfolio);
				echo '</pre>'; */
				
				$modules = $dbc->prepare('SELECT * FROM `module` WHERE `portfolioid` = "'.$requestedPortfolio['userid'].'" ORDER BY `position`');
				$modules->execute();
				$modules = $modules->fetchAll(PDO::FETCH_ASSOC);
				
				/*  echo '<pre>';
				print_r($modules);
				echo '</pre>'; */
				
				echo '<div id="containerOuter">';
					echo '<div class="portfolio" id="containerInner">';
				
					//variable om op teslaan waneer er een nieuwe break toegevoegd moet worden.
					$break = 0;
				
					echo '<div class="moduleSeparator coll-100">';
				
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
									//TODO: meer dan 2 inputs (loop)
									if(count($input) == 1){
										echo $portfolio->$moduletemplate['function']($input[0]);
									}else if(count($input) == 2){
										echo $portfolio->$moduletemplate['function']($input[0], $input[1]);
									}
									
									//$user = new User("amr.jonkman@gmail.com", "pass", $dbc);
									//print_r($user);
									if($user->isLoggedIn()){
										echo '<a href="?p=editmodule&m='.$module['id'].'">';
										echo "<div class='editModule'> EDIT </div>";
										echo '</a>';
									}
								}else{
									echo 'Aantal inputs komt niet overeen met het aantal benodigde velden.';
								}
								
								
								
								echo '</div>';
							echo '</div>';
							
							if($break > 99){
								echo '</div>';
								echo '<div class="moduleSeparator coll-100">';
								$break = 0;
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
	
	//Hoofd pagina
	function home(){
		global $core;
		global $pages;
		global $dbc;
		global $user;
		
		//$levelid = $user->get()['levelid'];
		$currentuser = 1;
		
		//if login true
		if($user->isLoggedIn()){
						
			$SQLstring = $dbc->prepare("SELECT user.levelid FROM user
						WHERE user.id = $currentuser");
			$SQLstring->execute();
			foreach($SQLstring as $key => $value)
			{
				//if student
				if($value['levelid'] == 1){	//$value['levelid'] wordt $levelid
					echo $pages->studentHome();
				//if teacher 
				}elseif($value['levelid'] == 2){
					echo $pages->teacherHome();
				//if admin
				}elseif($value['levelid'] == 3){
					echo $pages->adminHome();
				}
			}
		//if not logged in show login pages
		}else{
			
			echo $pages->login();
		}				
	}
	
	function login(){
		global $user;
		global $mysqliconnect;
		global $pages;
		global $core;
		
		if($user->isLoggedIn() == FALSE){
			if(isset($_POST['submit'])){
				if(empty($_POST['email']) OR empty($_POST['password'])){
					echo "<p>Vul alle velden in.</p>";
				}else{
					$email = $_POST['email'];
					$password = $_POST['password'];
					
					$user->login($email, $password);
				}
			}
			echo "<h2>Login</h2>";
			echo "<form method='POST' action='#'>
					E-mail:<br><input type='text' name='email' pattern='/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/' required><br>";
			echo "	Wachtwoord:<br><input type='password' name='password' required><br>";
			echo "<input type='submit' name='submit' value='Login'></form>";
			echo "<a href='index.php?p=register'>Registreer</a>";
		}else{ //Als $isLoggedIn == TRUE
			echo "U bent al ingelogd.";
		}
		
		
	}
	
	function register(){
		echo "<h2>Maak een account aan</h2>";
			
		echo "	<form method='' action=''>
				<input type='text' placeholder='E-mail' required><br>
				<input type='password' placeholder='Wachtwoord' required><br>
				<input type='password' placeholder='Herhaal wachtwoord' required><br>
				<input type='text' placeholder='Voornaam' required><br>
				<input type='text' placeholder='Achternaam' required><br>
				<input type='number' placeholder='Telefoonnummer' required><br>
				<input type='text' placeholder='Postcode' required><br>
				<input type='text' placeholder='Adres' required><br>
				<input type='submit' value='Registreer'>
				</form>";
				
		echo "<p>Heb je al een account? <a href='index.php?p=login'>Log in</a></p>";
	}
	
	function adminHome(){
		global $dbc;
		global $user;
		global $pages;
		
		$levelid = $user->get()['levelid'];
		if($user->isLoggedIn() AND $levelid == 3){
												
		
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
		}else{
			
			echo $pages->home();
		}
	}
	
	function teacherHome(){
		global $dbc;
		global $user;
		global $pages;
		
		$levelid = $user->get()['levelid'];
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
                
		echo "</table>";
		
		//if not logged in go to home
		}else{
			
			echo $pages->home();	//HOME REGELT DE REST
		}
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
									echo '<p class="alert">Module is bijgewerkt.</p>';
								}else{
									echo '<p class="alert">Er is een fout voorgekomen. Probeer het opnieuw.</p>';
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
							
							echo '<h1>Aanpassen Module</h1>';
							
							//URL van deze gebruiker
							$url = $core->getPortfolioURL($module['portfolioid']);
							
							echo '<div class="coll-100">';
								echo 'links voor module toevoegen en module verwijderen hier!';
							echo '</div>';
							
							echo '<div class="coll-50">';
								echo '<h2>'.ucfirst($moduletemplate['name']).'</h2>';
								echo '<p>'.ucfirst($moduletemplate['description']).'</p>';
								
								$inputs = explode(",", $module['input']);
								$fields = explode(",", $moduletemplate['field']);
								$titles = explode(",", $moduletemplate['fieldTitle']);
								
								echo '<form action="#" method="post">';
								
									//Voeg alle inputvelden toe die bij deze module horen.
									for ($x = 0; $x < count($fields); $x++) {
										echo $core->input($fields[$x],$titles[$x],$x,$inputs[$x]);
									} 
									
									// Plus de standart inputvelden.
									echo 'Breedte van de module in procent:<br> <input min="0" min="100" type="number" name="size" value="'.$module['size'].'" ><br><br>';
									
									echo '<input type="submit" name="Submit" value="Verstuur">';
								echo '</form>';
							echo '</div>';
							
							echo '<div class="coll-50">';
								echo '<h2>Portfolio Layout</h2>';
								
									echo '<div class="coll-33">';
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
											//TODO: meer dan 2 inputs (loop)
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
	
	//404
	function notfound(){
	
		echo '404';
	}
}
?>