<!DOCTYPE html>
<?php
	session_start();
	require ('core.php');
	require ('pages.php');
	require ('classes/user.php');

	$core = new Core;
	$pages = new Pages;
	$dbc = $core->dbc();
?>
<html>
	<head>
		<?php
			echo '<title>Hallo - '.$core->paginaTitel().'</title>';
		?>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="style.css" rel="stylesheet" type="text/css">

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	</head>
	<body>
		<?php
			echo '<div id="container">';
				$core->load();

				$user = new User($dbc);
				echo $user->register("peter2@stest.com", "aB3@", "aB3@", "Peter", "Pad", "1234567890", "1234CC", "123");
				echo '</div>';
		?>
	</body>
</html>
