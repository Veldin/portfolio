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


								/* echo '<br>';
								echo $input;
								echo '<br>';
								echo $size;
								echo '<br>';
								echo $moduleId; */

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

	function showUploads(){
			global $dbc;

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
									}
							}
					}
			}
			if(isset($_POST['saveChanges'])){
					foreach($uploads->getUserUploads(1) as $id){
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

			echo
			"
			<div class='table-responsive'>
				<form action='#' method='post'>
					<table class='table table-bordered'>
						<tr>
							<th>Bestandsnaam</th>
							<th>Beschrijving</th>
							<th>Publiekelijk</th>
							<th>Download</th>
							<th>Verwijder</th>
						</tr>";
						if($uploads->getUserUploads(1)){
								$arrayLength = count($uploads->getUserUploads(1));

								foreach($uploads->getUserUploads(1) as $upload){
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
					"</table>
					<button type='submit' name='saveChanges' class='btn btn-info'>Wijzigingen opslaan</button>
				</form>
			</div>";
	}

	// Functie voor het uploaden van files
	function uploadFile(){
			global $dbc;

			if(isset($_POST["upload"])){
					if(!empty($_POST['fileName']) && !empty($_POST['fileDescription']) && !empty($_FILES['fileToUpload']['name'])){
							$name = stripslashes($_POST['fileName']);
							$description = stripslashes($_POST['fileDescription']);
							$name = htmlentities($name);
							$description = htmlentities($description);

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
	}

	//404
	function notfound(){

		echo '404';
	}
}
?>
