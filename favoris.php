<?php
	
	include('base.php');
	
	if(isset($_SESSION['m']['id']) AND ctype_digit($_SESSION['m']['id']))
	{
	 
		function recupererFavoris()
		{
			$TEST = 'SELECT favoris FROM infomembre WHERE membre = '. $_SESSION['m']['id'] . ' LIMIT 1';
		
			$mys = mysql_query($TEST) or die(mysql_error());
			
			
			$donnees = mysql_result($mys, 0);
			
		
			
			if(empty($donnees))
			{
				return array();
			}
			
			if(!preg_match('#,#', $donnees))
			{
				$favoris = array($donnees);
			}
			else
			{
				$favoris = explode(',', $donnees);
			}
			return $favoris;
		}
		
		function ajouterFavoris($id)
		{
			
			$favoris = recupererFavoris();
			
			if(!in_array($id, $favoris))
			{
				$favoris[] = $id;
			}
			
			$mysql = implode(',', $favoris);
			
			$count = mysql_query('SELECT COUNT(*) FROM infomembre WHERE membre = ' . $_SESSION['m']['id']) or die(mysql_error());
			$nb = mysql_result($count, 0);
			
			if($nb == 0)
			{
				mysql_query('INSERT INTO infomembre(id, membre, favoris) VALUES("", "' . $_SESSION['m']['id'] . '", "' . $mysql  . '")') or die(mysql_error());
			}
			
			mysql_query('UPDATE infomembre SET favoris = "' . $mysql  . '"  WHERE membre = ' . $_SESSION['m']['id']) or die(mysql_error());
			
		}
		
		
		function supprimerFavoris($id)
		{
			$favoris = recupererFavoris();
			
			$risofav = array_flip($favoris);
			
			unset($risofav[$id]);
		
			$favoris = array_flip($risofav);
			
			$mysql = implode(',', $favoris);
			$query = 'UPDATE infomembre SET favoris = "' . $mysql . '" WHERE membre = ' . $_SESSION['m']['id'];
			mysql_query($query) or die(mysql_error());
		}
		
		
		if(isset($_GET['ajouter']))
		{
		
			if(ctype_digit($_GET['ajouter']))
			{
				
				$forums = mysql_query('SELECT COUNT(*) FROM forum WHERE id = ' . $_GET['ajouter']) or die(mysql_error());
				$result = mysql_result($forums, 0);
				
				if($result == 1)
				{
				
					ajouterFavoris($_GET['ajouter']);
				
				}
			}
			
		}
		
		if(isset($_GET['supprimer']))
		{
		
			if(ctype_digit($_GET['supprimer']))
			{
				
				$forums = mysql_query('SELECT COUNT(*) FROM forum WHERE id = ' . $_GET['supprimer']);
				$result = mysql_result($forums, 0);
				
				if($result == 1)
				{
					supprimerFavoris($_GET['supprimer']);
					
				}
				
			}	
			
		}
		
	}
	
	if(isset($_SESSION['m']['id']))
	{
		$requete = mysql_query('SELECT favoris FROM infomembre WHERE membre = ' . $_SESSION['m']['id']) or die(mysql_error());
		$forms = mysql_result($requete, 0);
		
		$supprimer = 0;
		
		if(!empty($forms))
		{
			$supprimer = 1;
			$favorisM = 1;
		}
	
		
	}
		
		$req = '';
		
		if($favorisM == 1)
		{
			$req = 'AND id IN(' . $forms . ')';
		}
		
		
						$reponse = mysql_unbuffered_query('SELECT id, titre FROM forum WHERE statut=\'0\' ' . $req .  ' ORDER BY titre ASC');
						
						while($donnees = mysql_fetch_assoc($reponse))
						{
							$supprimerr = '';
							if($supprimer == 1)
							{
								$supprimerr = '<img src="message/supprimer.gif" onclick="delete_favoris(\'' . $donnees['id'] . '\'); return false;" alt=""/> ';
							}
							
							echo'<img src="images/puce_liste_bleue.gif" alt=""> <a href="' . have_url($donnees['id'], 1 , FORUM_URL, $donnees['titre']) .'">' . $donnees['titre'] . '</a> ' . $supprimerr . '<br />';
							
						}
		
	
	
?>
