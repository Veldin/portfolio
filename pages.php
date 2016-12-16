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
			
			$requestedPortfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$user.'"');
			$requestedPortfolio->execute();
			$requestedPortfolio = $requestedPortfolio->fetchAll(PDO::FETCH_ASSOC);
			
			if(!empty($requestedPortfolio)){
				//user bestaat
			
				$requestedPortfolio = $dbc->prepare('SELECT * FROM `portfolio` WHERE `url` = "'.$user.'" LIMIT 1');
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
					echo '<div id="containerInner">';
				
					foreach ($modules as $module) {
						$moduletemplate = $dbc->prepare('SELECT * FROM `moduletemplate` WHERE `id` = "'.$module['moduleid'].'" LIMIT 1');
						$moduletemplate->execute();
						$moduletemplate = $moduletemplate->fetchAll(PDO::FETCH_ASSOC)[0];

						if (method_exists($portfolio,$moduletemplate['function'])){
							//echo 'Function Found';
							$input = explode(",", $module['input']);
							$fields = explode(",", $moduletemplate['field']);
							
							/* print_r($moduletemplate);*/
							
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
						//echo $portfolio->$moduletemplate['function']('sdfdsf');
						}else{
							echo 'Methode niet gevonden!';
						}
					}
					
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
	
	//404
	function notfound(){
	
		echo '404';
	}
}
?>