<?php
header( "refresh:10000;url=chat.php" );
?>

<!DOCTYPE html>
<?php
	session_start();
	
	//Alle benodigde bestanden.
	require ('classes/core.php');
	require ('classes/pages.php');
	require ('classes/portfolio.php');
	require ('classes/user.php');
	require ('classes/uploads.php');
	
	//Initialiseren van benodigde classen.
	$core = new Core;
	$pages = new Pages;
	$portfolio = new Portfolio;
	$dbc = $core->dbc();
	$mysqlconn = $core->mysqlcon();
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
		<link href="style.css?v=<?php echo TIME()?>" rel="stylesheet" type="text/css">
		
		<!-- Editor -->
		<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({ 
  
  
  selector:'#tinymce' ,
        style_formats: [
            {title: 'Open Sans', inline: 'span', styles: { 'font-family':'Open Sans'}},
            {title: 'Arial', inline: 'span', styles: { 'font-family':'arial'}},
            {title: 'Book Antiqua', inline: 'span', styles: { 'font-family':'book antiqua'}},
            {title: 'Comic Sans MS', inline: 'span', styles: { 'font-family':'comic sans ms,sans-serif'}},
            {title: 'Courier New', inline: 'span', styles: { 'font-family':'courier new,courier'}},
            {title: 'Georgia', inline: 'span', styles: { 'font-family':'georgia,palatino'}},
            {title: 'Helvetica', inline: 'span', styles: { 'font-family':'helvetica'}},
            {title: 'Impact', inline: 'span', styles: { 'font-family':'impact,chicago'}},
            {title: 'Symbol', inline: 'span', styles: { 'font-family':'symbol'}},
            {title: 'Tahoma', inline: 'span', styles: { 'font-family':'tahoma'}},
            {title: 'Terminal', inline: 'span', styles: { 'font-family':'terminal,monaco'}},
            {title: 'Times New Roman', inline: 'span', styles: { 'font-family':'times new roman,times'}},
            {title: 'Verdana', inline: 'span', styles: { 'font-family':'Verdana'}}
        ],
		plugins: "link",
		

  
  
  });</script>
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://bootswatch.com/cosmo/bootstrap.min.css">
		
		
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	

	
		<?php $pages->portfolioCSS(); ?>
	</head>
	<body style="
    background: initial;
	">
		<?php	
		$pages->chat(); 
		
		?>
		

	</body>
</html>





