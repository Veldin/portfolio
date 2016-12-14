<?php
//Een class met Core functies
class Core { 
	//Functie om de pagina te laden.
	function load(){
		global $pages;

		//Als pagina is aangegeven set de Pagina variabele.
		//Als de pagina niet is aangegeven - ga naar de Homepage.
		if (empty($_GET["p"])){ 
			$page = 'Home';
		}else{
			$page = $_GET["p"];
		}
		
		//Laad of de aangegeven pagina bestaat. 
		//Als deze niet bestaat - ga naar de notfound pagina.
		if (method_exists($pages,$page)){
			echo $pages->$page();
		}else{
			echo $pages->notfound();
		}	
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
	function lerarennav() {
		$DBconnect = mysqli_connect("127.0.0.1", "root", "");
		if($DBconnect === FALSE){
			echo "<p>Unable to connect to the database server.</p>"
			. "<p>Error code " . mysqli_errno() . ": " . mysqli_error()
			. "</p>";
		}else{
			$DBname = "portfolio";
			if(!mysqli_select_db ($DBConnect, $DBName)){
					echo "<p>There have not been found any portfolio's!</p>";
			}else{
				$TableName = "portfolio";
				$SQLstring = "SELECT * FROM $TableName";
				$QueryResult = mysqli_query($DBConnect, $SQLstring);
				if (mysqli_num_rows($QueryResult) == 0){
					echo "<p>There are no entries in the portfolio database!</p>";
				}else{
					echo "<p>Portfolio list:</p>";
					echo "<table>";
					echo "<tr><th>First Name</th>
					<th>Last Name </th></tr>";
					while($Row = mysqli_fetch_assoc($QueryResult)){
						echo "<tr><td>{$Row['first_name']}</td>";
						echo "<td>{$Row['last_name']}</td></tr>";
					}
				}
				mysqli_free_result($QueryResult);
			}
		}
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
		echo "<pre>";
		var_export($menu);
		echo "</pre>";
		//je krijgt een lijst doormiddel van deze foreach:
		//echo "<ul>";
		//foreach($menu as $optionDesc => $option){
		//	echo "<li><a href=$option>$optionDesc</a></li>";
		//}
		//echo "</ul>";
	}
	
}
?>