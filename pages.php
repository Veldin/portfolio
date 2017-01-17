<?php
//Een class met al mijn paginas als functies
class Pages {
	//Hoofd pagina
	function home(){
		global $core;

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
			$display = $display->fetchAll(PDO::FETCH_OBJ);
			foreach ($display as $value) {
				echo '<br>';
				print_r($value);
				echo '<br>';
			}

		}
	}

	//404
	function notfound(){

		echo '404';
	}
}
?>
