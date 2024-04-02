
<?php
include('base.php');
include('tete.php');

	?>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php
	// On regarde à quel page on se trouve
	if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
	{
		$page = intval($_GET['page']);
	}
	else
	{
		$page = 1;
	}
	
if(isset($_GET['recherche']))
{
	$recherche = mysql_real_escape_string(htmlspecialchars($_GET['recherche'])); //sécurisation des variables
	$mode = mysql_real_escape_string(htmlspecialchars($_GET['listRecherche']));
		$reponse = mysql_unbuffered_query('SELECT titre FROM forum WHERE id=\'' . intval($_GET['id']) . '\'');
	$donnees = mysql_fetch_assoc($reponse);
	$GLOBALS['tf'] = $donnees['titre'];
	
	if ($mode == 'IP')
	  {
	  
	  
		$requete_liste_smileys  = mysql_query('SELECT * FROM membres WHERE ip = "'.$recherche.'" GROUP BY pseudo ')or die (mysql_error());
		
		echo '
		<div class="bloc2">
		<h3>Recherche de pseudo pour l\'Ip '.$recherche.'</h3>
			<div class="texte">
		
		<table>
		<tr><th>Pseudo</th></tr>
		';
			
			while ($donnees_liste_smileys = mysql_fetch_array($requete_liste_smileys))
			{
				echo '<tr>
						<td>'.$donnees_liste_smileys['pseudo'].'</td>
					</tr>';
			}
				
		echo '</table></div></div>';
	  }
	elseif ($mode == 'message')
	{
	$mots = explode(' ', $recherche); // Séparation des mots
		$nombre_mots = count ($mots); // On compte le nombre de mots
		$valeur_requete = '';
		
		for($nombre_mots_boucle = 0; $nombre_mots_boucle < $nombre_mots; $nombre_mots_boucle++)
		{
		if ($mots[$nombre_mots_boucle] =="" OR $mots[$nombre_mots_boucle] == " " OR $mots[$nombre_mots_boucle] == "  ")
		{
		}
		else{
			$valeur_requete .= ' AND message REGEXP ("' . $mots[$nombre_mots_boucle] . '") '; // Modification de la variable $valeur_requete
			}
		}
		
	$reponse = mysql_query('SELECT  *  FROM message
	WHERE 	statut < 2 '.$valeur_requete.' ORDER BY 	timestamp DESC')or die (mysql_error());

					echo' 
<center><font color="grey"><h3><b><u>Forum :</u></font>  ' . stripslashes(htmlspecialchars($GLOBALS['tf'])) . '</b></h3></center><br /><br />
<div class="bloc2"><h3 onclick="cacher();" onmouseover="document.body.style.cursor=\'hand\';" ><img src="images/search.png" title="Rechercher"/> Recherche Topics / Messages <span style="float: right"><a href="stats.php?forum=' . intval($_GET['id']). '"><img src="images/stats.png" title="Stats de ce forum"/></a>
	<a href="#" onclick="add_favoris(' . intval($_GET['id']) . ');"> <img src="images/favoris.png" title="Ajouter aux favoris"/></a></span></h3>
	<div class="texte" id="texterecherche1">
			      <form method="get" action="recherche.php">
				    <input type="hidden" name="id" value="' . intval($_GET['id']) . '" />
				    <input type="text" name="recherche" value="Rechercher" onfocus="if(this.value == \'Rechercher\')this.value=\'\'"/>
				    <select name="listRecherche">
				    	<option value="sujet">Sujet</option>
					    <option value="auteur">Auteur</option>
                        <option value="message">Message</option>
				    </select>
				    <input type="image" value="Rechercher" type="button" class="bouton2"/> <input style="float: right;" type="button" class="bouton2" onClick="window.location.reload();" value="Rafraichir"/>
			      </form>
				 ';
				 $reponse = mysql_query('SELECT moderateurs.idPseudo, moderateurs.idForum, membres.id, membres.pseudo AS pseudo FROM moderateurs WHERE idForum = ' . intval($_GET['id']) . ' INNER JOIN membres ON moderateurs.idPseudo = membres.id ORDER BY membres.pseudo ASC'); while($donnees = mysql_fetch_assoc($reponse)) { echo 'Moderateur' . s('moderateurs') . 'du forum : ' . $donnees['pseudo'] . ''; }
			     echo'
			</div>
			</div><br />
			';
	?>
				<script type="text/javascript">

document.getElementById('<?php echo $mode;?>').selected = 'selected';


</script>
				<?php
				
	 echo ' <center><b><font color="#dd0000">R&eacute;sultat de la recherche pour "</font> ' . $recherche . ' <font color="#dd0000">"</font></b></center><br /> ';
	
	
		
		$i = 0;
		while($donnees = mysql_fetch_assoc($reponse))
		{
				
		// Debut - Alternance de couleur
			if($donnees['statut'] == 2)
			{
				$msg_msg = 'msg msg3';
			}
			else
			{
				if($i % 2 == 0)
				{
					$msg_msg = 'msg msg2';
					$a = 2;
				}
				else
				{
					$msg_msg = 'msg msg1';
					$a = '';
				}
			}
		// Fin - Alternance de couleur
		
			?>
			<script type="text/javascript"><!--
					function resize(imageposte)
					{
						var resizeLargeur = imageposte.offsetWidth ;
						
						
						// { Début - Redimensionnement si trop grand
							if(resizeLargeur > 400)
							{
								imageposte.width = 400 ;
								resizeLargeur -= 400 ;
							}
						// } Fin - Redimensionnement si trop grand
					}
					// --></script>
			<?php
		
		// Debut - Couleur du Pseudo
			if($donnees['acces'] == 100) // Admin
			{
				$pseudo = ' <strong>' . ($COLP->afficher(100, $donnees['pseudo'])) . '</strong>';
			}
			elseif($donnees['acces'] == 95) // Directeur
			{
				$pseudo = ' <strong>' . ($COLP->afficher(95, $donnees['pseudo'])) . '</strong>';
			}
			elseif($donnees['acces'] == 90) // Super modo
			{
				$pseudo = ' <strong>' . ($COLP->afficher(90, $donnees['pseudo'])) . '</strong>';
			}
			elseif($donnees['acces'] == 50) // Modérateur
			{
				$pseudo = ' <strong>' . ($COLP->afficher(50, $donnees['pseudo'])) . '</strong>';
			}
			elseif($donnees['acces'] == 45) // Pubbeur
			{
				$pseudo = ' <strong>' . ($COLP->afficher(45, $donnees['pseudo'])) . '</strong>';
			}
			elseif($donnees['acces'] == 98) // Codeur
			{
				$pseudo = ' <strong>' . ($COLP->afficher(98, $donnees['pseudo'])) . '</strong>';
			}
			else // Membre
			{
				$pseudo = ' <strong>' . ($COLP->afficher(10, $donnees['pseudo'])) . '</strong>';
			}
		// Fin - Couleur du Pseudo
		
		// Debut - Fonction simple pour les membres
			if(isset($_SESSION['ignore'][$donnees['idPseudo']]) AND $_SESSION['ignore'][$donnees['idPseudo']] == 1)
			{
				$imgI = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;designorerMembre=' . $donnees['idPseudo'] . '" title="Voir le membre"><img src="images/message/voir.gif" alt="Voir le membre" /></a>';
				$message = '<i>Vous avez décidé d\'ignorer ce membre.</i><br />';
			}
			else
			{
				$imgI = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;ignorerMembre=' . $donnees['idPseudo'] . '" title="Ignorer ce membre"><img src="images/message/ignorer.gif" alt="Ignorer le membre" /></a>';
				$message = nl2br(htmlspecialchars(stripslashes($donnees['message'])));
			}
			
			$avertir = '<a target="avertir" rel="nofollow" href="avertir.php?idMessage=' . $donnees['id'] . '&amp;idTopic=' . $donnees['idTopic'] . '&amp;page=' . $page . '&amp;pseudo=' . $donnees['pseudo'] . '&amp;acces=' . $_SESSION['m']['acces'] . '&amp;pseudoA=' . $_SESSION['m']['pseudo'] . '" onclick="window.open(\'\',\'avertir\',\'width=580,height=330,scrollbars=no,status=no\')"><img src="http://image.jeuxvideo.com/pics/forums/bt_forum_avertirmod.gif" alt="Avertir un administrateur" width="11" height="12" /></a>';
			
			// Citer
			if(isset($_SESSION['m']['id']))
			{
				$citer = '<a href="topic.php?id=' . $donnees['idTopic'] . '&amp;page=' . $page . '&amp;citer=' . $donnees['id'] . '#form_post"><img src="images/message/citer.gif" alt="Citer le message" /></a>';
			}
			else
			{
				$citer = '';
			}
			
	
			// Bouton éditer qui peut apparaitre que 3min après le post (sauf admin)
			if(((time() - 5 * 60) < $donnees['timestamp'] AND isset($_SESSION['m']['id']) AND $_SESSION['m']['id'] == $donnees['idPseudo']) OR $_SESSION['m']['acces'] == 100 OR $_SESSION['m']['acces'] == 50)
			{
				$editer = '<a href="topic.php?id=' . $donnees['idTopic'] . '&amp;page=' . $page . '&amp;editer=' . $donnees['id'] . '"><img src="images/message/editer.gif" alt="Editer un message" /></a>';
			}
			else
			{
				$editer = '';
			}
		// Fin - Fonctions simple pour un membre
		
		// Debut - Affichage date du message de modification
			if($donnees['timestampModif'] > 1000)
			{
				$modif = '<li class="modification">Dernière modification le ' . date('d/m/Y\ \à\ H\:i\:s', $donnees['timestampModif']) . ' par ' . $donnees['auteurModif'] . '</li>';
			}
			else
			{
				$modif = '';
			}
		// Fin - Affichage date du message de modification
		
		if(!empty($donnees['signature']))
		{
			$signature = '<br /><br /><hr />' . bbCode(stripslashes(htmlspecialchars($donnees['signature']))) . '';
		}
		else
		{
			$signature = '';
		}
		$aysql = mysql_query('SELECT COUNT(*) FROM message WHERE idPseudo = ' . $donnees['idPseudo']);
		$posts = mysql_result($aysql, 0);
		$points = $posts * 3;
		
			$nb = 46;
			while($posts / $nb > 7)
			{
				$nb *= 10;
			}
			$lenght =  ceil($posts / $nb);
			
			if($lenght <  1)
			{
				$lenght = 1;
			}
			
			
			$avatar = $donnees['avatar'];
			
				if(!preg_match("#^http://[^'\" ]+\.(jpg|jpeg|gif|png)$#i", $avatar))
				{
					$avatar = 'http://image.jeuxvideo.com/avatars/default.jpg';
				}
				
			$affichAva = '<img src="' . $avatar. '" />
			';

			$ss = '';
			if($posts > 1)
			{
				$ss = 's';
			}
			
			$affichRang = rang($posts,10) . '<br />';
			$affichPost = '' . $posts . ' message' . $ss . '';
			$affichPoint = '' . $points . ' point' . $ss . '';
			
			$membredepuis = ceil((time() - $donnees['timestamp']) / 60 / 60 / 24);
			$membredepuis = number_format($membredepuis, 0, '', '.');
				
				
			$affichJour =  '<b>' . $membredepuis . ' jours</b><br />';


			$mobile = '';
			if($donnees['mb'] == 1 AND $OPTIONS['avatar_p'] == 0)
			{
				$mobile = ' <span style="color: orange;"><b>via mobile</b></span>';
			}
			
			if($donnees['acces'] == 100) // Admin
			{
				$grade = ' <strong>Administrateur</strong>';
			}
			elseif($donnees['acces'] == 95) // Directeur
			{
				$grade = ' <strong>Directeur</strong>';
			}
			elseif($donnees['acces'] == 90) // Super modo
			{
				$grade = ' <strong>Super modo</strong>';
			}
			elseif($donnees['acces'] == 50) // Modérateur
			{
				$grade = ' <strong>Mod&eacute;rateur</strong>';
			}
			elseif($donnees['acces'] == 98) // Codeur
			{
				$grade = ' <strong>Codeur</strong>';
			}
			else // Membre
			{
				$grade = ' <strong>Membre</strong>';
			}
			
		// Debut - Affichage du message
		$selection_recherche = mysql_query('
		SELECT topic.*, MAX(message.timestamp) AS `timestampDernierMessage` 
		FROM topic
		INNER JOIN message ON message.idTopic = topic.id
		WHERE ' . $sql . 'topic.idForum=\'' . intval($_GET['id']) . '\' GROUP BY topic.id
		ORDER BY ROUND(topic.statut) DESC, timestampDernierMessage DESC		
		') or die (mysql_error()); //requête avec le résultat de la boucle dedans
		$resultats = mysql_fetch_assoc($selection_recherche);
		$titre = stripslashes(htmlspecialchars($donnees['titre']));
			echo '
	<div class="message" id="message_' . $donnees['id'] . '">
		<div class="table">
			<div class="tr">
				<div class="msg2">
					<div class="td gauche">
						<div class="pseudo">
						    ' . $pseudo  . ' <a href="cdv-' . $donnees['idPseudo'] . '.html" title="Voir le profil" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/message/bt_forum_profil.gif" width="11" height="12"></a>
						</div>
						<div class="img">
							' . $affichAva . '
						</div>
						<div class="nbPost">
							' . ($COLP->afficher($donnees['acces'], $grade)) . '<br />
							' . $affichJour . '
							' . $affichPost . '<br />
							' . $Sip .'
						</div>
					</div>
					<div class="td droite">
						<div class="date">
						' . $imgI . ' ' . $citer . ' ' . $editer . ' <a href="mp.php?action=ecrire&amp;pseudo=' . $donnees['idPseudo'] . '"><img src="images/mp.png"></a>
						</div>
						<div class="fonctions">
						<b>' . $armev5 . ' ' . $supprimer_message  . ' ' . $avertir . ' Post&eacute; ' . $mobile .  ' le ';
						
						$timestamp = $donnees['timestamp'];
						
						// Tableau des mois en français
						$mois_fr = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
						
						// Tableau de la date
						// $jour_modif = array('', '1er', 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
						
						// On extrait la date du jour
						list($jour, $mois, $annee) = explode('/', date('d/n/Y', $timestamp));
						echo $jour . ' ' . $mois_fr[$mois] . ' ' . $annee; 
						echo ' à ';
						echo date('H\:i\:s', $timestamp);
							$recherche = mysql_real_escape_string(htmlspecialchars($_GET['recherche'])); //sécurisation des variables
	$mots = explode(' ', $recherche); // Séparation des mots
		$nombre_mots = count ($mots); // On compte le nombre de mots
		$valeur_requete = '';
		
		for($nombre_mots_boucle = 0; $nombre_mots_boucle < $nombre_mots; $nombre_mots_boucle++)
		{
		$message = str_replace(STRTOLOWER(''.$mots[$nombre_mots_boucle].'') , '<strong style="color:red">'.$mots[$nombre_mots_boucle].'</strong>', STRTOLOWER($message)); 
		}
						echo '
						' . $kick . '' .  $bann . '</b>
						<div class="post">
                      	' . bbCode($message) . '
						' . bbCode($signature) . '
						' . $modif . '
						<div class="lienPermanent">
						<a href="topic.php?id=' . $donnees['idTopic'] . '&page=' . $page . '#message_' . $donnees['id'] . '">Lien permanent</a>
					    </div>
					  </div>
				    </div>
			    </div>
		  	 </div>
	     </div>
	   </div>
    </div>
			';
		// Fin - Affichage du message
		
		$i++; // On incrémente pour l'alternance de couleur des messages
	
		}
		echo '<style=""> <a href=""></a>
		</table>
		</div>
		<br />
		';
					
	}
	
	elseif ($mode == 'posteur')
	{
	$mots = explode(' ', $recherche); // Séparation des mots
		$nombre_mots = count ($mots); // On compte le nombre de mots
		$valeur_requete = '';
		
		for($nombre_mots_boucle = 0; $nombre_mots_boucle < $nombre_mots; $nombre_mots_boucle++)
		{
		if ($mots[$nombre_mots_boucle] =="" OR $mots[$nombre_mots_boucle] == " " OR $mots[$nombre_mots_boucle] == "  ")
		{
		}
		else{
			$valeur_requete .= ' AND pseudo REGEXP ("' . $mots[$nombre_mots_boucle] . '") '; // Modification de la variable $valeur_requete
			}
		}
		
	$reponse = mysql_unbuffered_query('SELECT * FROM message WHERE 	statut < 2 '.$valeur_requete)or die (mysql_error());

			echo' 
<center><font color="grey"><h3><b><u>Forum :</u></font>  ' . stripslashes(htmlspecialchars($GLOBALS['tf'])) . '</b></h3></center><br /><br />
<div class="bloc2"><h3 onclick="cacher();" onmouseover="document.body.style.cursor=\'hand\';" ><img src="images/search.png" title="Rechercher"/> Recherche Topics / Messages <span style="float: right"><a href="stats.php?forum=' . intval($_GET['id']). '"><img src="images/stats.png" title="Stats de ce forum"/></a>
	<a href="#" onclick="add_favoris(' . intval($_GET['id']) . ');"> <img src="images/favoris.png" title="Ajouter aux favoris"/></a></span></h3>
	<div class="texte" id="texterecherche1">
			      <form method="get" action="recherche.php">
				    <input type="hidden" name="id" value="' . intval($_GET['id']) . '" />
				    <input type="text" name="recherche" value="Rechercher" onfocus="if(this.value == \'Rechercher\')this.value=\'\'"/>
				    <select name="listRecherche">
				    	<option value="sujet">Sujet</option>
					    <option value="auteur">Auteur</option>
                        <option value="message">Message</option>
				    </select>
				    <input type="image" value="Rechercher" type="button" class="bouton2"/> <input style="float: right;" type="button" class="bouton2" onClick="window.location.reload();" value="Rafraichir"/>
			      </form>
				 ';
				 $reponse = mysql_query('SELECT moderateurs.idPseudo, moderateurs.idForum, membres.id, membres.pseudo AS pseudo FROM moderateurs WHERE idForum = ' . intval($_GET['id']) . ' INNER JOIN membres ON moderateurs.idPseudo = membres.id ORDER BY membres.pseudo ASC'); while($donnees = mysql_fetch_assoc($reponse)) { echo 'Moderateur' . s('moderateurs') . 'du forum : ' . $donnees['pseudo'] . ''; }
			     echo'
			</div>
			</div><br />
			';
	?>
				<script type="text/javascript">

document.getElementById('<?php echo $mode;?>').selected = 'selected';


</script>
				<?php
				
	 echo ' <center><b><font color="#dd0000">R&eacute;sultat de la recherche pour "</font> ' . $recherche . ' <font color="#dd0000">"</font></b></center><br /> ';
	
	
		
		$i = 0;
		while($donnees = mysql_fetch_assoc($reponse))
		{
				
		// Debut - Alternance de couleur
			if($donnees['statut'] == 2)
			{
				$msg_msg = 'msg msg3';
			}
			else
			{
				if($i % 2 == 0)
				{
					$msg_msg = 'msg msg2';
					$a = 2;
				}
				else
				{
					$msg_msg = 'msg msg1';
					$a = '';
				}
			}
		// Fin - Alternance de couleur
		
		// Debut - Fonction que Admin
			if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] >= 90)
			{
				$bann = ' <a title="Bannir ce membre" href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;bannirMembre=' . $donnees['idPseudo'] . '&amp;idMessage=' . $donnees['id'] . '" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir BANNIR ce membre ?\'); return reponse;"><img src="images/moderation/bann.gif" alt="bannir" /></a>';
				$bannIp = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;bannIp=' . $donnees['ip'] . '" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir BANNIR IP ce membre ?\'); return reponse;"><img src="images/moderation/bannIp.png" alt="Bannir Ip" /></a>';
			}
			else
			{
				$bann = '';
				$bannIp = '';
			}
		// Fin - Fonction que Admin
			?>
			<script type="text/javascript"><!--
					function resize(imageposte)
					{
						var resizeLargeur = imageposte.offsetWidth ;
						
						
						// { Début - Redimensionnement si trop grand
							if(resizeLargeur > 400)
							{
								imageposte.width = 400 ;
								resizeLargeur -= 400 ;
							}
						// } Fin - Redimensionnement si trop grand
					}
					// --></script>
			<?php
		// Debut - Fonction que modo ou admin
			if($donnees['statut'] != 2) // Message non-supprimé
			{
				if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] >= 50) // Modérateur ou +
				{	
					$supprimer_message = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;supprimerMessage=' . $donnees['id'] . '&amp;statut=' . $donnees['statut'] . '" title="Supprimer ce message" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir SUPPRIMER ce message ?\'); return reponse;"><img src="images/moderation/supprimer.gif" alt="Supprimer ce message" width="11" height="12" /></a> ';
					$kick = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;kicker=' . $donnees['idPseudo'] . '" title="Kicker ce membre" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir KICKER ce membre ?\'); return reponse;"><img src="images/moderation/kick.gif" alt="Kicker le menbre" /></a>';
					$armev5 = '<input type="checkbox" name="supprimer[]" value="' . $donnees['id'] . '" style="margin: 0px" />';
					
					if($_SESSION['m']['acces'] == 100) // Admin
					{
						$Sip = '<b>Ip : </b>' . $donnees['ip'] . ''; 
					}
				}
			}
			elseif($donnees['statut'] == 2) // Message supprimé, visible que par les admins
			{
				if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] == 100)
				{
					$supprimer_message = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;voirMessage=' . $donnees['id'] . '&amp;statut=' . $donnees['statut'] . '" title="Supprimer ce message" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir REMETTRE ce message ?\'); return reponse;"><img src="images/moderation/voir.gif" alt="Supprimer ce message" width="11" height="12" /></a> ';
					$Sip = '<b>Ip : </b>' . $donnees['ip'] . ''; 
					$kick = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;kicker=' . $donnees['idPseudo'] . '" title="Kicker ce membre"><img src="images/moderation/kick.gif" alt="Kicker le membre" /></a>';
				}
			}
			else // On est simple membre, on voit rien de tout ça
			{
				$supprimer_message = '';
				$kick = '';
				$Sip = '';
				$armev5 = '';
			}
		// Fin - Fonction que modo ou admin
		
		// Debut - Couleur du Pseudo
			if($donnees['acces'] == 100) // Admin
			{
				$pseudo = ' <strong class="admin">' . $donnees['pseudo'] . '</strong>';
			}
			elseif($donnees['acces'] == 90) // Super modo
			{
				$pseudo = ' <strong class="moderateurG">' . $donnees['pseudo'] . '</strong>';
			}

			elseif($donnees['acces'] == 50) // Modérateur
			{
				$pseudo = ' <strong class="moderateur">' . $donnees['pseudo'] . '</strong>';
			}
			elseif($donnees['acces'] != 10) // Super membre
			{
				$pseudo = ' <strong style="color:'.$donnees['acces'].'">' . $donnees['pseudo'] . '</strong>';
			}
			else // Membre
			{
				$pseudo = ' <strong>' . $donnees['pseudo'] . '</strong>';
			}
		// Fin - Couleur du Pseudo
		
		// Debut - Fonction simple pour les membres
			if(isset($_SESSION['ignore'][$donnees['idPseudo']]) AND $_SESSION['ignore'][$donnees['idPseudo']] == 1)
			{
				$imgI = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;designorerMembre=' . $donnees['idPseudo'] . '" title="Voir le membre"><img src="images/message/voir.gif" alt="Voir le membre" /></a>';
				$message = '<i>Vous avez décidé d\'ignorer ce membre.</i><br />';
			}
			else
			{
				$imgI = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;ignorerMembre=' . $donnees['idPseudo'] . '" title="Ignorer ce membre"><img src="images/message/ignorer.gif" alt="Ignorer le membre" /></a>';
				$message = nl2br(bbCode(htmlspecialchars(stripslashes($donnees['message']))));
			}
			
			$avertir = '<a target="avertir" rel="nofollow" href="avertir.php?idMessage=' . $donnees['id'] . '&amp;idTopic=' . $donnees['idTopic'] . '&amp;page=' . $page . '&amp;pseudo=' . $donnees['pseudo'] . '&amp;acces=' . $_SESSION['m']['acces'] . '&amp;pseudoA=' . $_SESSION['m']['pseudo'] . '" onclick="window.open(\'\',\'avertir\',\'width=580,height=330,scrollbars=no,status=no\')"><img src="http://image.jeuxvideo.com/pics/forums/bt_forum_avertirmod.gif" alt="Avertir un administrateur" width="11" height="12" /></a>';
			
			// Citer
			if(isset($_SESSION['m']['id']))
			{
				$citer = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;citer=' . $donnees['id'] . '#form_post"><img src="images/message/citer.gif" alt="Citer le message" /></a>';
			}
			else
			{
				$citer = '';
			}
			
	
			// Bouton éditer qui peut apparaitre que 3min après le post (sauf admin)
			if(((time() - 5 * 60) < $donnees['timestamp'] AND isset($_SESSION['m']['id']) AND $_SESSION['m']['id'] == $donnees['idPseudo']) OR $_SESSION['m']['acces'] == 100 OR $_SESSION['m']['acces'] == 50)
			{
				$editer = '<a href="topic.php?id=' . htmlspecialchars(stripslashes($_GET['id'])) . '&amp;page=' . $page . '&amp;editer=' . $donnees['id'] . '"><img src="images/message/editer.gif" alt="Editer un message" /></a>';
			}
			else
			{
				$editer = '';
			}
		// Fin - Fonctions simple pour un membre
		
		// Debut - Affichage date du message de modification
			if($donnees['timestampModif'] > 1000)
			{
				$modif = '<li class="modification">Dernière modification le ' . date('d/m/Y\ \à\ H\:i\:s', $donnees['timestampModif']) . ' par ' . $donnees['auteurModif'] . '</li>';
			}
			else
			{
				$modif = '';
			}
		// Fin - Affichage date du message de modification
		
		if(!empty($donnees['signature']))
		{
			$signature = '<br /><br /><hr />' . bbCode(stripslashes(htmlspecialchars($donnees['signature']))) . '';
		}
		else
		{
			$signature = '';
		}
		
		// Debut - Affichage du message
		$recherche = mysql_real_escape_string(htmlspecialchars($_GET['recherche'])); //sécurisation des variables
	$mots = explode(' ', $recherche); // Séparation des mots
		$nombre_mots = count ($mots); // On compte le nombre de mots
		$valeur_requete = '';
		
		for($nombre_mots_boucle = 0; $nombre_mots_boucle < $nombre_mots; $nombre_mots_boucle++)
		{
		$donnees['idPseudo'] = str_replace(''.$mots[$nombre_mots_boucle].'' , '<strong style="color:red">'.$mots[$nombre_mots_boucle].'</strong>', $donnees['idPseudo']); 
		}
			
		$aysql = mysql_query('SELECT COUNT(*) FROM message WHERE idPseudo = ' . $donnees['idPseudo']);
		$posts = mysql_result($aysql, 0);
		$points = $posts * 3;
		
			$nb = 46;
			while($posts / $nb > 7)
			{
				$nb *= 10;
			}
			$lenght =  ceil($posts / $nb);
			
			if($lenght <  1)
			{
				$lenght = 1;
			}
			
			
			$avatar = $donnees['avatar'];
			
				if(!preg_match("#^http://[^'\" ]+\.(jpg|jpeg|gif|png)$#i", $avatar))
				{
					$avatar = 'http://image.jeuxvideo.com/avatars/default.jpg';
				}
				
			$affichAva = '<img src="' . $avatar. '" />
			';

			$ss = '';
			if($posts > 1)
			{
				$ss = 's';
			}
			
			$affichRang = rang($posts,10) . '<br />';
			$affichPost = '' . $posts . ' message' . $ss . '';
			$affichPoint = '' . $points . ' point' . $ss . '';
			
			$membredepuis = ceil((time() - $donnees['timeM']) / 60 / 60 / 24);
			$membredepuis = number_format($membredepuis, 0, '', '.');
				
				
			$affichJour =  '<b>' . $membredepuis . ' jours</b><br />';


			$mobile = '';
			if($donnees['mb'] == 1 AND $OPTIONS['avatar_p'] == 0)
			{
				$mobile = ' <span style="color: orange;"><b>via mobile</b></span>';
			}
			
			if($donnees['acces'] == 100) // Admin
			{
				$grade = ' <strong>Administrateur</strong>';
			}
			elseif($donnees['acces'] == 95) // Directeur
			{
				$grade = ' <strong>Directeur</strong>';
			}
			elseif($donnees['acces'] == 90) // Super modo
			{
				$grade = ' <strong>Super modo</strong>';
			}
			elseif($donnees['acces'] == 50) // Modérateur
			{
				$grade = ' <strong>Mod&eacute;rateur</strong>';
			}
			elseif($donnees['acces'] == 98) // Codeur
			{
				$grade = ' <strong>Codeur</strong>';
			}
			else // Membre
			{
				$grade = ' <strong>Membre</strong>';
			}

			echo '
	<div class="message" id="message_' . $donnees['id'] . '">
		<div class="table">
			<div class="tr">
				<div class="msg2">
					<div class="td gauche">
						<div class="pseudo">
						    ' . $pseudo  . ' <a href="cdv-' . $donnees['idPseudo'] . '.html" title="Voir le profil" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/message/bt_forum_profil.gif" width="11" height="12"></a>
						</div>
						<div class="img">
							' . $affichAva . '
						</div>
						<div class="nbPost">
							Rang : ' . $affichRang . '
							' . $affichJour . '
							' . $Sip .'
						</div>
					</div>
					<div class="td droite">
						<div class="date">
						' . $imgI . ' ' . $citer . ' ' . $editer . ' <a href="mp.php?action=ecrire&amp;pseudo=' . $donnees['idPseudo'] . '"><img src="images/mp.png"></a>
						</div>
						<div class="fonctions">
						<b>' . $armev5 . ' ' . $supprimer_message  . ' ' . $avertir . ' Post&eacute; ' . $mobile .  ' le ';
						
						$timestamp = $donnees['timestamp'];
						
						// Tableau des mois en français
						$mois_fr = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
						
						// Tableau de la date
						// $jour_modif = array('', '1er', 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
						
						// On extrait la date du jour
						list($jour, $mois, $annee) = explode('/', date('d/n/Y', $timestamp));
						echo $jour . ' ' . $mois_fr[$mois] . ' ' . $annee; 
						echo ' à ';
						echo date('H\:i\:s', $timestamp);
							
						echo '
						' . $kick . '' .  $bann . '</b>
						<div class="post">
                      	' . $message . '
						' . bbCode($signature) . '
						' . $modif . '
						<div class="lienPermanent">
						<a href="topic-' . htmlspecialchars(stripslashes($_GET['id'])) . '-' . $page . '.html#message_' . $donnees['id'] . '">Message n. ' . $donnees['id'] . '</a>
					    </div>
					  </div>
				    </div>
			    </div>
		  	 </div>
	     </div>
	   </div>
    </div>
			';
		// Fin - Affichage du message
		
		$i++; // On incrémente pour l'alternance de couleur des messages
	
		}
		echo '<style=""> <a href=""></a>
		</table>
		</div>
		<br />
		';
					
	}
	else
	{
	// Définition du champ
	if($mode == 'sujet' OR $mode == 'sujetExact')
	{
		$champ = 'topic.titre';
	}
	elseif($mode == 'auteur' OR $mode == 'auteurExact')
	{
		$champ = 'topic.pseudo';
	}
	
	else
	{
		echo '<b>Vous n\'avez pas choisi de mode de recherche.</b>';
		include('pied.php');
		exit;
	}
	
	if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] == 100)
	{
		$sql = '';
	}
	else
	{
		$sql = 'ROUND(topic.statut) > 0 AND ';
	}
	// Définition du mode de recherche
	if ($mode == 'auteur' OR $mode == 'sujet')
	{
		$and_ou_or = 'AND'; // On utilisera AND dans la boucle
	}
	
	if ($mode == 'auteurExact' OR $mode == 'sujetExact') // Si le mode de recherche est par expression exacte
	{
		$selection_recherche = mysql_query('
		SELECT topic.*, MAX(message.timestamp) AS `timestampDernierMessage` 
		FROM topic 
		INNER JOIN message ON message.idTopic = topic.id
		WHERE ' . $sql . 'topic.idForum=\'' . intval($_GET['id']) . '\' AND ' . $champ . ' 
		LIKE \'%' . $recherche . '%\' GROUP BY topic.id 
		ORDER BY ROUND(topic.statut) DESC, timestampDernierMessage DESC
		') or die (mysql_error());
	}
	else // Si le mode de recherche n'est pas par expression exacte
	{
		$mots = explode(' ', $recherche); // Séparation des mots
		$nombre_mots = count ($mots); // On compte le nombre de mots
		$valeur_requete = '';
		
		for($nombre_mots_boucle = 0; $nombre_mots_boucle < $nombre_mots; $nombre_mots_boucle++)
		{
			$valeur_requete .= '' . $and_ou_or . ' ' . $champ . ' LIKE \'%' . $mots[$nombre_mots_boucle] . '%\''; // Modification de la variable $valeur_requete
		}
		
		$valeur_requete = ltrim($valeur_requete,$and_ou_or); // Suppression de AND ou de OR au début de la boucle
		$selection_recherche = mysql_query('
		SELECT topic.*, MAX(message.timestamp) AS `timestampDernierMessage` 
		FROM topic
		INNER JOIN message ON message.idTopic = topic.id
		WHERE ' . $sql . 'topic.idForum=\'' . htmlspecialchars(stripslashes($_GET['id'])) . '\' AND ' . $valeur_requete . ' GROUP BY topic.id
		ORDER BY ROUND(topic.statut) DESC, timestampDernierMessage DESC		
		') or die (mysql_error()); //requête avec le résultat de la boucle dedans
	}
	
	$nombre_resultats = mysql_num_rows($selection_recherche); // Compte le nombre d'entrées sélectionnées par la recherche*/
	if ($nombre_resultats == 0) // S'il n'y a pas de résultat
	{
	/* Nom du forum selon la variable $titreforum */
$reponse = mysql_unbuffered_query('SELECT titre FROM forum WHERE id=\'' . htmlspecialchars(stripslashes($_GET['id'])) . '\'');
$donnees = mysql_fetch_assoc($reponse);
$titreForum = $donnees['titre'];

$titre_page = $titreForum;
					echo' 
<center><font color="grey"><h3><b><u>Forum :</u></font>  ' . stripslashes(htmlspecialchars($GLOBALS['tf'])) . '</b></h3></center><br /><br />
<div class="bloc2"><h3 onclick="cacher();" onmouseover="document.body.style.cursor=\'hand\';" ><img src="images/search.png" title="Rechercher"/> Recherche Topics / Messages <span style="float: right"><a href="stats.php?forum=' . intval($_GET['id']). '"><img src="images/stats.png" title="Stats de ce forum"/></a>
	<a href="#" onclick="add_favoris(' . intval($_GET['id']) . ');"> <img src="images/favoris.png" title="Ajouter aux favoris"/></a></span></h3>
	<div class="texte" id="texterecherche1">
			      <form method="get" action="recherche.php">
				    <input type="hidden" name="id" value="' . intval($_GET['id']) . '" />
				    <input type="text" name="recherche" value="Rechercher" onfocus="if(this.value == \'Rechercher\')this.value=\'\'"/>
				    <select name="listRecherche">
				    	<option value="sujet">Sujet</option>
					    <option value="auteur">Auteur</option>
                        <option value="message">Message</option>
				    </select>
				    <input type="image" value="Rechercher" type="button" class="bouton2"/> <input style="float: right;" type="button" class="bouton2" onClick="window.location.reload();" value="Rafraichir"/>
			      </form>
				 ';
				 $reponse = mysql_query('SELECT moderateurs.idPseudo, moderateurs.idForum, membres.id, membres.pseudo AS pseudo FROM moderateurs WHERE idForum = ' . intval($_GET['id']) . ' INNER JOIN membres ON moderateurs.idPseudo = membres.id ORDER BY membres.pseudo ASC'); while($donnees = mysql_fetch_assoc($reponse)) { echo 'Moderateur' . s('moderateurs') . 'du forum : ' . $donnees['pseudo'] . ''; }
			     echo'
			</div>
			</div><br />
			';
	?>
				<script type="text/javascript">

document.getElementById('<?php echo $mode;?>').selected = 'selected';


</script>
				<?php
		 echo ' <center><b><font color="#dd0000">R&eacute;sultat de la recherche pour "</font> ' . $recherche . ' <font color="#dd0000">"</font></b></center><br /> ';
		avert('<b><img src="http://image.jeuxvideo.com/css_img/defaut/danger.gif"> Pas de r&eacute;ponses pour " <i>' . $recherche . ' </i>"</b>');
	}
	else // Il y a au moins un résultat	
	{
		/* Nom du forum selon la variable $titreforum */
$reponse = mysql_unbuffered_query('SELECT titre FROM forum WHERE id=\'' . htmlspecialchars(stripslashes($_GET['id'])) . '\'');
$donnees = mysql_fetch_assoc($reponse);
$titreForum = $donnees['titre'];

$titre_page = $titreForum;
	
					echo' 
<center><font color="grey"><h3><b><u>Forum :</u></font>  ' . stripslashes(htmlspecialchars($GLOBALS['tf'])) . '</b></h3></center><br /><br />
<div class="bloc2"><h3 onclick="cacher();" onmouseover="document.body.style.cursor=\'hand\';" ><img src="images/search.png" title="Rechercher"/> Recherche Topics / Messages <span style="float: right"><a href="stats.php?forum=' . intval($_GET['id']). '"><img src="images/stats.png" title="Stats de ce forum"/></a>
	<a href="#" onclick="add_favoris(' . intval($_GET['id']) . ');"> <img src="images/favoris.png" title="Ajouter aux favoris"/></a></span></h3>
	<div class="texte" id="texterecherche1">
			      <form method="get" action="recherche.php">
				    <input type="hidden" name="id" value="' . intval($_GET['id']) . '" />
				    <input type="text" name="recherche" value="Rechercher" onfocus="if(this.value == \'Rechercher\')this.value=\'\'"/>
				    <select name="listRecherche">
				    	<option value="sujet">Sujet</option>
					    <option value="auteur">Auteur</option>
                        <option value="message">Message</option>
				    </select>
				    <input type="image" value="Rechercher" type="button" class="bouton2"/> <input style="float: right;" type="button" class="bouton2" onClick="window.location.reload();" value="Rafraichir"/>
			      </form>
				 ';
				 $reponse = mysql_query('SELECT moderateurs.idPseudo, moderateurs.idForum, membres.id, membres.pseudo AS pseudo FROM moderateurs WHERE idForum = ' . intval($_GET['id']) . ' INNER JOIN membres ON moderateurs.idPseudo = membres.id ORDER BY membres.pseudo ASC'); while($donnees = mysql_fetch_assoc($reponse)) { echo 'Moderateur' . s('moderateurs') . 'du forum : ' . $donnees['pseudo'] . ''; }
			     echo'
			</div>
			</div><br />
			';
	?>
				<script type="text/javascript">

document.getElementById('<?php echo $mode;?>').selected = 'selected';


</script>
				<?php
	 echo ' <center><b><font color="#dd0000">R&eacute;sultat de la recherche pour "</font> ' . $recherche . ' <font color="#dd0000">"</font></b></center><br /> ';
	
		echo '
		<div class="listageTopic">
			<table>
				<tr>
					<th class="col1">&nbsp;</th>
					<th class="col2">Sujet</th>
					<th class="col3">Auteur</th>
					<th class="col4">Nb</th>
					<th class="col5">Dernier Msg</th>
				</tr>
				';
		
		$i = 0;
		while($resultats = mysql_fetch_array($selection_recherche)) //boucle affichant les résultats
		{
			// Nombre de Message par topic
			$retour = mysql_query('SELECT COUNT(*) AS nbMessage FROM message WHERE idTopic=\'' . $resultats['id'] . '\'');
			$infos = mysql_fetch_assoc($retour);
			$nbMessage = $infos['nbMessage'] - 1;
			
						// Déclaration de la couleur du dossier
						if($nbMessage < 20)
						{
							$dossier = 1;
						}
						elseif($nbMessage < 100)
						{
							$dossier = 2;
						}
						elseif($nbMessage < 1000)
						{
							$dossier = 3;
						}
						elseif($nbMessage < 5000)
						{
							$dossier = 4;
						}
						else
						{
							$dossier = 5;
						}
			
			// Déclaration de l'image du topic
			$arrayImages = array(
			'1.0' => 'lock',
			'1.1' => 'dossier' . $dossier . '',
			'2.1' => 'eplock',
			'2.0' => 'epingle',
			);
			
			// Pour l'alternance de couleur
			if($i % 2 == 0)
			{
				$bloc = 'tr1';
			}	
			else
			{
				$bloc = 'tr2';	
			}
			
			
			// Couleur du pseudo
			if($resultats['acces'] == 100)
			{	
				$pseudo = '<div class="admin">' . $resultats['pseudo'] . '</div>';
			}
			elseif($resultats['acces'] == 50)
			{	
				$pseudo = '<font color="#0000dd">' . $resultats['pseudo'] . '</font>';
			}
			else
			{
				$pseudo = $resultats['pseudo'];
			}
			
			echo '
			<tr class="' . $bloc . '">
				<td class="col11"><a href="topic.php?id='.$resultats['id'].'&page=' . $page . '"><img src="images/pic/' . $arrayImages[$resultats['statut']] . '.gif" /></a></td>
				';
				
				$titre = stripslashes(htmlspecialchars($resultats['titre']));
				
				echo '
				<td class="col12"><b><a href="topic.php?id=' . $resultats['id'] . '&amp;page=' . $page . '">' . wordwrap($titre, 400, "\n", true) . '</a></b></td>
				<td class="col13">' . $pseudo . '</td>
				<td class="col14">' . $nbMessage . '</td>
				<td class="col15">' . date('d/m/Y\ H\hi', $resultats['timestampDernierMessage']) . '</td>
			</tr>
			';
			$i++; // On incrémente pour changer la couleur
		}
		echo '<style=""> <a href=""></a>
		</table>
		</div>
		<br />
		';
	}
	}
}
include('pied.php');
?>