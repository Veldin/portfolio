<!DOCTYPE html>
<?php
	session_start();
	require ('classes/core.php');
	require ('classes/pages.php');
	require ('classes/portfolio.php');
	require ('classes/user.php');
	require ('classes/uploads.php');

	$core = new Core;
	$pages = new Pages;
	$portfolio = new Portfolio;
	$uploads = new Uploads;
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
			echo '<div id="headerOuter">';
				echo '<div id="headerInner">';
					$pages->header();
				echo '</div>';
			echo '</div>';

			//$user = new User("amr.jonkman@gmail.com", "pass", $dbc);
			$user = new User("amr.jonkman@gmail.com", "pass", $dbc);

			if($user->login()){
				echo "User is logged in";
			}

			if($user->isLoggedIn()){
				echo "User is logged in";
			}

			$uploads->getUserUploads(1);

			$core->load();

			echo '<div id="footerOuter">';
				echo '<div id="footerInner">';
					$pages->footer();
				echo '</div>';
			echo '</div>';
		?>
		<form action="classes/uploads.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
		<input type="hidden" name="previous_page" value='<?php echo $_SERVER['REQUEST_URI'];?>'>
    <input type="submit" value="Upload Image" name="upload">
</form>

	</body>
</html>
