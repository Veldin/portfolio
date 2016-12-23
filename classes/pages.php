<?php
//Een class met al mijn paginas als functies
class Pages {  
	//Hoofd pagina
	function home(){
		global $core;

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
		
		echo '<div id="containerOuter">';
			echo '<div id="containerInner">';
				echo 'Comentaar';
			echo '</div>';
		echo '</div>';
	}
	
	
	//functie voor het editen van een module
	function editmodule(){
		global $dbc;
		global $core;
		global $portfolio;
		
		if(isset($_GET["m"])){
				$moduleId = htmlspecialchars($_GET["m"]);
				$moduleId = preg_replace("/[^0-9,.]/", "", $moduleId);
				
				$user = 1; //loged in user

				$module = $dbc->prepare('SELECT * FROM `module` WHERE `id` = "'.$moduleId.'" AND `portfolioid` = '.$user.' LIMIT 1');
				$module->execute();
				$module = $module->fetchAll(PDO::FETCH_ASSOC);

				//print_r($moduletemplate );
				if(!empty($module)){
					$module = $module[0];
					echo '<div id="containerOuter">';
						echo '<div id="containerInner">';
							//verwerken
						
							if(isset($_POST['Submit'])){
								$input = ''; 
								
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
								
								$size = 100;
								if(isset($_POST['size'])){		
									$size = htmlspecialchars($_POST['size']);
									$size = preg_replace("/[^0-9,.]/", "", $size);
								}
								
								$sql = "UPDATE `module` SET `input`='".$input."',`size`='".$size."'  WHERE id=".$moduleId;
								
								$update = $dbc->prepare($sql);
								$update->execute();
								
								if($update == true){
									echo '<p>Module is bijgewerkt.</p>';
								}else{
									echo '<p>Er is een fout voorgekomen. Probeer het opnieuw.</p>';
								}
							}
						
							//ophalen module
							$module = $dbc->prepare('SELECT * FROM `module` WHERE `id` = "'.$moduleId.'" AND `portfolioid` = '.$user.' LIMIT 1');
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
							
							$url = $core->getPortfolioURL($module['portfolioid']);
							echo $url;
							//Links
							echo '<div></div>';
							
							echo '<div class="coll-50">';
								echo '<h2>'.$moduletemplate['name'].'</h2>';
								echo '<p>'.$moduletemplate['description'].'</p>';
								
								$inputs = explode(",", $module['input']);
								$fields = explode(",", $moduletemplate['field']);
								$titles = explode(",", $moduletemplate['fieldTitle']);
								
								echo '<form action="#" method="post">';
									for ($x = 0; $x < count($fields); $x++) {
										 
										echo $core->input($fields[$x],$titles[$x],$x,$inputs[$x]);
									} 
									
									echo 'Groote: <input min="0" min="100" type="number" name="size" value="'.$module['size'].'" ><br>';
									
									echo '<input type="submit" name="Submit" value="Submit">';
								echo '</form>';
							echo '</div>';
							
							echo '<div class="coll-50">';
								echo '<h2>Portfolio Layout</h2>';

									echo '<div class="coll-33">';
										$core->portfoliolayout($module['portfolioid'], $moduleId);
									echo '</div>';
									
									echo '<div class="clear"></div>';

								echo '<div class="clear"></div>';
							echo '</div>';
							
							echo '<div class="clear"></div>';
							
							//Tonen van de uitkomst
							echo '<h2>Dit is hoe hij er uit komt!</h2>';
							echo '<p>Dit is hoe hij er uit komt te zien op de portfolio!</p>';
							
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