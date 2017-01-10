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
	
		echo '<h2>Reacties</h2>';
	
		if($page == 'portfolio' && $user->isLoggedIn()){
		
			if(isset($_POST["reactie"])){
				$comment = htmlspecialchars($_POST["reactie"]);
			
				if(!empty($comment)){
					//$toevoegen = $dbc->prepare('INSERT INTO `chat` VALUES ('..',timestamp,targetid,message)');
					//$toevoegen->execute();

					$toevoegen = $dbc->prepare('INSERT INTO `chat` VALUES ('.$user->get()['id'].','.time().','.$userId.',"'.$comment.'")');
					$toevoegen->execute();
					
					if($toevoegen){
						echo '<div class="alert alert-success">';
							echo '<strong>Succces!</strong> Uw bericht is toegevoegd.';
						echo '</div>';
						
						//haal alle reacties opnieuw op.
						$comments = $dbc->prepare('SELECT * FROM `chat` WHERE `targetid` = "'.$userId.'" ORDER BY `timestamp` DESC LIMIT '.$ammount.'');
						$comments->execute();
						$comments = $comments->fetchAll(PDO::FETCH_ASSOC);
					}else{
						echo '<div class="alert alert-danger">';
							echo '<strong>:(</strong> Er is iets fout gegaan, probeer het later opnieuw.';
						echo '</div>';
					}
					
				}else{
					echo '<div class="alert alert-danger">';
					  echo '<strong>:(</strong> U heeft geen bericht ingevuld.';
					echo '</div>';
				}
			}
		
			
		
			echo '<form action="#" method="post">';
				echo '<div class="form-group">';
					echo '<textarea class="form-control" rows="5" name="reactie"></textarea>';
				echo '</div>';
				echo '<input  type="submit" class="btn btn-default" type="submit" name="Submit" value="Verstuur">';
			echo '</form>';
		}
	
	
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
						echo htmlspecialchars($CommentUser['firstname'])." ".htmlspecialchars($CommentUser['lastname']);
					echo '</div>';
					
					echo '<div class="coll-75">';
						echo htmlspecialchars($comment['message']);
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