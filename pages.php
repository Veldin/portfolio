<?php
//Een class met al mijn paginas als functies
class Pages {  
	//Hoofd pagina
	function home(){
		global $core;

	}
	
	function header(){
		echo 'HEADER';
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
	
		
		if(isset($_GET["u"])){
			$user = htmlspecialchars($_GET["u"]);
			
			$portfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$user.'"');
			$portfolio->execute();
			$portfolio = $portfolio->fetchAll(PDO::FETCH_ASSOC);
			
			if(!empty($portfolio)){
				//user bestaat
			
				$portfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$user.'"');
				$portfolio->execute();
				$portfolio = $portfolio->fetchAll(PDO::FETCH_ASSOC);
			
				/* echo '<div id="containerOuter">';
					echo '<div id="containerInner">';
						echo 'User exists.';
					echo '</div>';
				echo '</div>'; */
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
	
	//404
	function notfound(){
	
		echo '404';
	}
}
?>