<!DOCTYPE html>
<?php
	session_start();
	
	//Alle benodigde bestanden.
	require ('classes/core.php');
	require ('classes/pages.php');
	require ('classes/portfolio.php');
	require ('classes/user.php');
	
	//Initialiseren van benodigde classen.
	$core = new Core;
	$pages = new Pages;
	$portfolio = new Portfolio;
	$dbc = $core->dbc();
	//$user = new User("amr.jonkman@gmail.com", "pass", $dbc);
	$user = new User($dbc);
?>
<html>
	<head>
		<?php
			echo '<title>Portfolio - '.$core->paginaTitel().'</title>';
		?>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<link href="coll.css" rel="stylesheet" type="text/css">
		<link href="style.css" rel="stylesheet" type="text/css">
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	</head>
	<body>
		<?php 
			echo '<div id="headerOuter">';
				echo '<div id="headerInner">';
					$pages->header();
				echo '</div>';
			echo '</div>';

			
			if($user->login("amr.jonkman@gmail.com", "pass")){
				//echo "Gebruiker logged in.";
			}
			
			if($user->isLoggedIn()){
				//echo "Gebruiker is ingelogd";
			}
		
			$core->load();
			
			echo '<div id="footerOuter">';
				echo '<div id="footerInner">';
					$pages->footer();
				echo '</div>';
			echo '</div>';
		?>
	</body>
</html>





