<?php
//Een class met alle portfolio functies.
class Portfolio {  
	//Hoofd pagina
	function header($text,$size){
		return('<h'.$size.'>'.$text.'</h'.$size.'>');
	}
	
	function paragraph($text){
		return('<p>'.$text.'</p>');
	}
	
	function imageFromLink($link,$title){
		return ('<img src="'.$link.'" style="width:100%;" alt="'.$title.'">');
	}
}
?>