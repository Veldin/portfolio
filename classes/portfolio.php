<?php
//Een class met alle portfolio functies.
class Portfolio {  
	//Hoofd pagina
	
	function header($text,$size){

		$id = str_replace(' ', '_', $text);
		return('<h'.$size.' id="header'.$id.'">'.$text.'</h'.$size.'>');
	}
	
	function tableOfContents(){
		global $core;
		global $dbc;
		global $user;
		
		$page = $_GET["p"];
	
		//Verkrijg het userID (voor op de edit pagina en op portfolio zelf)
		if($page == 'editmodule'){
			$userId = $user->get()['id'];
		}elseif($page == 'portfolio'){
			$userId = $core->getUserFromURL($_GET["u"]);
		}
		
		$headers = $dbc->prepare('SELECT * FROM `module` WHERE `portfolioid` = "'.$userId.'" AND `moduleid` = 3 ORDER BY `position`');
		$headers->execute();
		$headers = $headers->fetchAll(PDO::FETCH_ASSOC);
		
		echo '<ul class="inhoudsopgave">';
		foreach ($headers as &$header) {
			$header = explode(",", $header['input']);
			$header[2] = str_replace(' ', '_', $header[0]);
			
			if(isset($header[1])){
			
			}
			
			echo '<li class="size'.$header[1].'">';
				echo '<a href="#header'.$header[2].'">'.$header[0].'</a>';
			echo '</li>';
		}
		echo '</ul>';
		
	}
	
	function paragraph($text){
		return(html_entity_decode ( $text));
	}
	
	function imageFromLink($link,$title){
		return ('<img src="'.$link.'" style="width:100%;" alt="'.$title.'">');
	}
	
	function youtube($watch,$beschrijving){
		
		$return = '';
		
		$return .= '<div class="coll-100">';
			$return .= '<iframe class="Iframe" src="https://www.youtube.com/embed/'.$watch.'"></iframe>';
			$return .= '<div class="center coll-95"> '.$beschrijving.' </div>';
		$return .= '</div>';
		//qzQO5a9F328
		return($return);
	}
	
	function files(){
		global $core;
		global $user;
	
		$uploads = new Uploads;
		
		$page = $_GET["p"];
		
		//Verkrijg het userID (voor op de edit pagina en op portfolio zelf)
		if($page == 'editmodule'){
			$userId = $user->get()['id'];
		}elseif($page == 'portfolio'){
			$userId = $core->getUserFromURL($_GET["u"]);
		}

		//est wainting for icon xD
		$count = 0;
		foreach($uploads->getUserUploads($userId,true) as $upload){
		
			if($count == 3){
				echo '<div class="clear"></div>';
				$count = 0;
			}
			
			echo '<div class="coll-33 selectModule">';

				echo '<h3 class=>'.$upload['name'].'</h2>';
				echo '<div class="iconHuge">'.$upload['fileicon'].'</div>';
				echo '<p>'.$upload['description'].'<br></p>';
				
				echo '<a href="'.$upload['url'].'" class="btn btn-default" role="button" download>Download<br><span class="extension">als: .'.$upload['extension'].'</span></a>';
			echo '</div>';
			
			$count++;
		}
		
	
		
	
		return '';
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