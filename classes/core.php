<?php
//Een class met Core functies
class Core { 
	//Functie om de pagina te laden.
	function load(){
		global $pages;
		global $dbc;
		global $user;
		
		//Haal de rechten van de user op
		if($user->isLoggedIn()){
			$levelid = $user->get()['levelid'];
		}else{
			$levelid = 0;
		}
		$blockedPages = $dbc->prepare('SELECT * FROM `level` WHERE `id` = '.$levelid);
		$blockedPages->execute();
		$blockedPages = $blockedPages->fetchAll(PDO::FETCH_ASSOC)[0];
		$blockedPages = explode(",", $blockedPages['blocked']);
		$blockedPages = array_map('strtolower', $blockedPages);
		
		//Als pagina is aangegeven set de Pagina variabele.
		//Als de pagina niet is aangegeven - ga naar de Homepage.
		if (empty($_GET["p"])){ 
			$page = 'home';
		}else{
			$page = strtolower($_GET["p"]);
		}
		
		//Laad of de aangegeven pagina bestaat. 
		//Als deze niet bestaat - ga naar de notfound pagina.
		if (method_exists($pages,$page)){
			//echo $pages->$page();
			
			if (in_array($page,$blockedPages)) {
				//page is blocked
				header("Location: index.php?p=home");
			}else{
				echo $pages->$page();
			}
			
			//check if page is blocked.
			
			
		}else{
			echo $pages->notfound();
		}	
	}
	
	// Geeft een kleur terug gebaseerd op de string.
	function stringToColorCode($str) {
	  $code = dechex(crc32($str));
	  $code = substr($code, 0, 6);
	  return $code;
	}

	// Functie die de pagina titel returned voor in de titel. (Lagestreep _ voor spatie)
	function paginaTitel($spatie = true){
		if (empty($_GET["p"])){ 
			$page = 'home';
		}else{
			if($spatie == true){
				$page = str_replace("_"," ",$_GET["p"]); //Voor het tonen op pagina
			}else{
				$page = strtolower($_GET["p"]); //Voor in de code
			}
		}
		return $page;
	}
	
	function getUserFromURL($url){
		global $dbc;
	
		$portfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$url.'" LIMIT 1');
		$portfolio->execute();
		$portfolio = $portfolio->fetchAll(PDO::FETCH_ASSOC)[0];
	
		return $portfolio['userid'];
	}
	
	function getPortfolioURL($userid){
		global $dbc;
	
		$portfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `userid` = "'.$userid.'" LIMIT 1');
		$portfolio->execute();
		$portfolio = $portfolio->fetchAll(PDO::FETCH_ASSOC);

		if(!empty($portfolio)){
			$portfolio = $portfolio[0];
		
			return $portfolio['url'];
		}
		return false;
	}
	
	//Functie voor het maken van een portfolio layout scema.
	function portfoliolayout($userid, $active = ''){
		global $dbc;
		global $portfolio;
		global $user;
		
		$requestedPortfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `userid` = "'.$userid.'" LIMIT 1');
		$requestedPortfolio->execute();
		$requestedPortfolio = $requestedPortfolio->fetchAll(PDO::FETCH_ASSOC)[0];
		
		$modules = $dbc->prepare('SELECT * FROM `module` WHERE `portfolioid` = "'.$requestedPortfolio['userid'].'" ORDER BY `position`');
		$modules->execute();
		$modules = $modules->fetchAll(PDO::FETCH_ASSOC);
		
		foreach ($modules as $module) {
			$moduletemplate = $dbc->prepare('SELECT * FROM `moduletemplate` WHERE `id` = "'.$module['moduleid'].'" LIMIT 1');
			$moduletemplate->execute();
			$moduletemplate = $moduletemplate->fetchAll(PDO::FETCH_ASSOC)[0];
			
			if (method_exists($portfolio,$moduletemplate['function'])){

				$input = explode(",", $module['input']);
				$fields = explode(",", $moduletemplate['field']);

			
				
				echo '<a href="?p=editmodule&m='.$module['id'].'">';
					if($module['id'] == $active){
						echo '<div class="coll-'.$module['size'].'">'; 
							echo '<div class="active portfoliolayoutscema">';
								echo $moduletemplate['name'];
							echo '</div>';
						echo '</div>';
					}else{
						echo '<div class="coll-'.$module['size'].'">'; 
							echo '<div class="portfoliolayoutscema">';
								echo $moduletemplate['name'];
							echo '</div>';
						echo '</div>';
					}
				echo '</a>';
			}
		}
	
	}
	
		
	function mysqlcon(){
	
		$servername = "db.veldin.com"; 
		$username = "md253219db370063"; 
		$password = "NiFQYCvz"; 
		$DBName = "md253219db370063";  

		$DBConnect = mysqli_connect($servername,$username,$password);
		
		if($DBConnect === FALSE){
			echo "<p>Unable to connect to the database server!</p>" 
			. "<p>Error code" . mysqli_errno() . ": "
			. mysqli_error() . "</p>";
			
			return mysqli_error();
		}else{
			$SelectDB = mysqli_select_db($DBConnect, $DBName);
		}
		
		if($SelectDB === FALSE){
			echo "<p>Unable to connect to the database server.</p>"
			. "<p>Error code " . mysqli_errno() . ": "
			. mysqli_error() . "</p>";
			
			return mysqli_error();
		}else{
			return $DBConnect;
		}
		
	}
	
	function dbc() { 
			$servername = "db.veldin.com"; 
			$username = "md253219db370063"; 
			$password = "NiFQYCvz"; 
			$dbname = "md253219db370063";  
		
		try {
			$dbc = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return $dbc;
		}
		catch(PDOException $e)
		{
			return "Connection failed: " . $e->getMessage();
		}
    } 
	
	//genereren van de inputvelden voor het aanpassen van de modules
	function input($type = 'text', $title = 'naam', $name = 'naam', $value = ' '){
		echo '<div class="form-group">';
			echo '<label>'.$title.':</label>';
			
			if ($type == 'textarea'){
				echo '<textarea id="tinymce" rows="25" name="'.$name.'">'.$value.'</textarea>';
			}else if ($type == 'youtube'){
				
				echo '<div class="coll-100">';
					echo '<div class="coll-75 youtube-form">';
					echo '<span>https://www.youtube.com/watch?v=</span><input  type="text" name="'.$name.'" value="'.$value.'" >';
					echo '</div><br><br>';
				echo '</div>';
				
			}else{
				echo '<input class="form-control"  type="'.$type.'" name="'.$name.'" value="'.$value.'" >';
			}
		echo '</div>';
	}
	
	
	
	
	
	
	
	
}
?>