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
	
	function comments($ammount){
		global $core;
		global $user;
		global $dbc;
	
		$page = $_GET["p"];
	
		//Verkrijg het userID (voor op de edit pagina en op portfolio zelf)
		if($page == 'editmodule'){
			$userId = $user->get()['id'];
		}elseif($page == 'portfolio'){
			$userId = $core->getUserFromURL($_GET["u"]);
		}
		
		//initialiseer argument die functie gaat returnen.
		$return = '';

		//Haal alle reacties op!
		$comments = $dbc->prepare('SELECT * FROM `chat` WHERE `targetid` = "'.$userId.'" ORDER BY `timestamp` DESC LIMIT '.$ammount.'');
		$comments->execute();
		$comments = $comments->fetchAll(PDO::FETCH_ASSOC);
	
		echo '<div class="comments coll-100">';
	
		if(!empty($comments)){
			foreach ($comments as $comment) {
				//print_r($comment);
				
				$CommentUser = $dbc->prepare('SELECT * FROM `user` WHERE `id` = "'.$comment['userid'].' LIMIT 0"');
				$CommentUser->execute();
				$CommentUser = $CommentUser->fetchAll(PDO::FETCH_ASSOC)[0];
				
				//print_r($CommentUser);
				if($CommentUser['id'] == $userId){
				echo '<div class="comment own coll-100">';
				}else{
				echo '<div class="comment coll-100">';
				}
					echo '<div class="coll-25">';
						echo $CommentUser['firstname']." ".$CommentUser['lastname'];
					echo '</div>';
					
					echo '<div class="coll-75">';
						echo $comment['message'];
					echo '</div>';
				echo '</div>';
			}
		}else{
			$return .= 'Er zijn nog geen reacties geplaatst!';
		}
		
		echo '</div>';
	
		return ( $return );
	}
	
	
}
?>