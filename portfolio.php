<?php
//Een class met alle portfolio functies.
class Portfolio {  
	//Hoofd pagina
	function paragraph($text){
		return('<p>'.$text.'</p>');
	}
	
	function imageFromLink($link,$title){
		return ('<img src="'.$link.'" alt="'.$title.'">');
	}
}
?>