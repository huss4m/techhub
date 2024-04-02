<?php
include('base.php');

// DÈbut - SÈcurisation du script
	if(!isset($_GET['id']))
	{
		avert(bbcode("Votre ip a bien &eacute;t&eacute; enregistr&eacute;."));
		exit;
	}
	
	if(preg_match("#\D#", $_GET['id']))
	{
		avert(bbcode("Votre ip a bien &eacute;t&eacute; enregistr&eacute;."));
		exit;
	}
	$_GET['id'] = secure($_GET['id']);
	
	if(preg_match("#\D#", $_GET['lapage']))
	{
		avert(bbcode("Votre ip a bien &eacute;t&eacute; enregistr&eacute;."));
		exit;
	}
	$_GET['lapage']= secure($_GET['lapage']);
// Fin - SÈcurisation du script


if(stristr($server["request_uri"], "?d=1")) {
$_SESSION['nolimit'] = "undefined";}
if($_GET['id']==197 && !isset($_SESSION['nolimit'])) {
require "tete.php";
avert(utf8_encode("<center>Le contenu de ce forum peut-Ítre choquant voire dÈconseillÈ aux mineurs<br />
Voulez vous y aller pour autant ?<br />
<input type='button' value='Oui' onclick='window.location=document.location.href + \"?d=1\"' /> <input type='button' value='Non' onclick='window.location=\"accueil.htm\"' /></center>
"));
require "pied.php";
die;
}
	if(isset($_SESSION['m']['acces'], $_SESSION['m']['id'], $_SESSION['admin']['token']) AND $_SESSION['m']['acces'] > 92)
	{
		$tokenModerationForum = $_SESSION['moderation']['token'];
	}
	elseif(isset($_SESSION['m']['moderateur'][$_GET['id']], $_SESSION['m']['id'], $_SESSION['moderation']['token']) AND $_SESSION['m']['moderateur'][$_GET['id']] == 1)
	{
		$_SESSION['m']['acces'] = 50;
		$tokenModerationForum = $_SESSION['moderation']['token'];
	}
	else
	{
		$reponse = mysql_unbuffered_query('SELECT statut FROM forum WHERE id=' . intval($_GET['id']) . '');
		$donnees = mysql_fetch_assoc($reponse);
		
		if($donnees['statut'] == 1)
		{
			if(isset($_SESSION['m']['id']))
			{
				$reponse2 = mysql_query('SELECT * FROM moderateurs WHERE idPseudo=' . $_SESSION['m']['id'] . '') or die(mysql_error());
				$result = mysql_num_rows($reponse2);
				
					if(isset($_SESSION['m']['acces'], $_SESSION['m']['id']) AND $_SESSION['m']['acces'] != 100 AND $_SESSION['m']['acces'] != 90)
					{	
						if((int) $result == 0)
						{
						include('tete.php');
						echo '
						<div class="bloc2">
							<h3><span>ERREUR !</span></h3>
							<div class="texte">
								<b><center><FONT COLOR="red" >DÈsolÈ, ce forum n\'existe pas !</font></b></center><br />
								<center><img src="images/defaut/puce_base.gif"> <a href="liste-forums.html">Revenir ‡ la page d\'accueil des forums</a><br /></center>
							</div>
						</div>';
						include('pied.php');
						exit;
						}
					}
			}
			else
			{
			include('tete.php');
			echo '
			<div class="bloc2">
				<h3><span>ERREUR !</span></h3>
				<div class="texte">
					<b><center><FONT COLOR="red" >DÈsolÈ, ce forum n\'existe pas !</font></b></center><br />
					<center><img src="images/defaut/puce_base.gif"> <a href="liste-forums.html">Revenir ‡ la page d\'accueil des forums</a><br /></center>
				</div>
			</div>';
			include('pied.php');
			exit;
			}
		}
	}

	// L'url n'a pas ÈtÈ modifiÈ par le visiteur
	if(!isset($_GET['id']) OR !ctype_digit($_GET['id']))
	{
	include('tete.php');
	echo '
	<div class="bloc2">
		<h3><span>ERREUR !</span></h3>
		<div class="texte">
			<b><center><FONT COLOR="red" >DÈsolÈ, ce forum n\'existe pas !</font></b></center><br />
			<center><img src="images/defaut/puce_base.gif"> <a href="liste-forums.html">Revenir ‡ la page d\'accueil des forums</a><br /></center>
		</div>
	</div>';
	include('pied.php');
	exit;
	}

	// Si le forum existe bien
	$reponse = mysql_query('SELECT * FROM forum WHERE id=' . intval($_GET['id']));
	$result = mysql_num_rows($reponse);

	if((int) $result == 0) // ( pour php, 0 = false, donc il faut prÈciser qu'on attend un chiffre )
	{
	include('tete.php');
	echo '
	<div class="bloc2">
		<h3><span>ERREUR !</span></h3>
		<div class="texte">
			<b><center><FONT COLOR="red" >DÈsolÈ, ce forum n\'existe pas !</font></b></center><br />
			<center><img src="images/defaut/puce_base.gif"> <a href="liste-forums.html">Revenir ‡ la page d\'accueil des forums</a><br /></center>
		</div>
	</div>';
	include('pied.php');
	exit;
	}
// Fin - AccËs ‡ la page


// Debut - Gestion des Kicks

	// D√©kicker un pseudo par un modo/admin
	if(isset($_SESSION['m']['acces'], $_GET['dekick']) AND $_SESSION['m']['acces'] >= 50) 
	{
		mysql_unbuffered_query('DELETE FROM kick WHERE idPseudo=' . intval($_GET['dekick']) . ' AND idForum=' . intval($_GET['id']) . '');
		tete('Location: forum-' . intval($_GET['id']) . '.html#kick');
		exit;
	}
	
	// D√©kickement auto apr√®s 72h
	mysql_unbuffered_query('DELETE FROM kick WHERE timestamp < ' . (time() - 3600 * 72) . '');
	
// Fin - Gestion des Kicks


// Debut - Marquer message lus
	if(isset($_GET['marquerLu']) AND $_GET['marquerLu'] = 1)
	{
		$_SESSION['tmp_derniere_visite'] = time();
	}
// Fin - Marquer message lus


// Debut - Gestion des Topics
	if(isset($_GET['supprimerTopic'], $_SESSION['m']['acces']) AND ctype_digit($_GET['supprimerTopic']) AND $_SESSION['m']['acces'] >= 50)
	{
		mysql_unbuffered_query("UPDATE topic SET statut=0 WHERE id='" . intval($_GET['supprimerTopic']) . "'");

	}
	// Arme v5
	if(isset($_POST['supprimer'], $_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
	{
		foreach($_POST['supprimer'] as $idTopic)
		{
			if(isset($idTopic) AND ctype_digit($idTopic))
			{
				if(isset($_POST['changeStatut']))
				{
					$statut = secure($_POST['changeStatut']);

					switch ($statut) 
					{
						case 1: // Restaurer un topic
						mysql_unbuffered_query("UPDATE topic SET statut='1.1' WHERE id='" . $idTopic . "'");
						break;
						
						case 10: // Locker un topic
						mysql_unbuffered_query("UPDATE topic SET statut='1.0' WHERE id='" . $idTopic . "'");
						break;
						
						case 11: // Topic normal
						mysql_unbuffered_query("UPDATE topic SET statut='1.1' WHERE id='" . $idTopic . "'");
						break;
						
						case 12: // Supprimer un topic
						mysql_unbuffered_query("UPDATE topic SET statut=0 WHERE id='" . intval($idTopic) . "'");
						break;
						
						case 20: // Epingler un topic
						mysql_unbuffered_query("UPDATE topic SET statut='2.0' WHERE id='" . $idTopic . "'");
						break;
						
						case 21: // Epingler & verrouiller un topic
						mysql_unbuffered_query("UPDATE topic SET statut='2.1' WHERE id='" . $idTopic . "'");
						break;

						default:
						echo 'Une erreur est survenue.';
					}
				}
			}
		}
	}

	// Restaurer un topic
	if(isset($_GET['voirTopic'], $_SESSION['m']['acces']) AND ctype_digit($_GET['voirTopic']) AND $_SESSION['m']['acces'] > 92)
	{
		mysql_unbuffered_query("UPDATE topic SET statut='1.1' WHERE id='" . intval($_GET['voirTopic']) . "'");
	}
// Fin - Gestion des Topics

include('tete.php');

// Debut - Ajout d'un nouveau topic
	$titre2 = '';
	if(isset($_POST['titre'], $_POST['message']) AND trim(!empty($_POST['titre'])))
	{
		$titre = secure($_POST['titre']);
		$titre2 = secure($_POST['titre']);
		$message = nl2br(secure($_POST['message']));
		$message2 = secure($_POST['message']);
		
		// $titre2 et $message2 pour le formulaire
		
		if(!empty($_POST['titre']))
		{
			if(mb_strlen($_POST['titre']) <= 50) // Titre compos√© de 1 √† 50 caract√®res
			{
				
						if(isset($_SESSION['m']['id']))
						{
							// On v√©rifie qu'il n'a post√© 10s avant
							$requete = mysql_unbuffered_query('SELECT MAX(topic.timestamp) AS `time` FROM topic WHERE idPseudo=\'' . $_SESSION['m']['id'] . '\'');
							$infos = mysql_fetch_assoc($requete);
							
							if($infos['time'] < (time() - $config['forum']['tempsEntreChaqueTopic']))
							{
								$requete = mysql_query('SELECT argent FROM membres WHERE id = ' . $_SESSION['m']['id']);
								$shop = mysql_fetch_assoc($requete);
								$argent = $shop['argent'];
								$argentForum = $argent + 5;
								if(dict($_POST['message']))
								{
									$Sacces = $_SESSION['m']['acces'];
									
									// Insertion du topic dans la table topic
									mysql_unbuffered_query('INSERT INTO topic VALUES("", "' . $_SESSION['m']['pseudo'] . '", "' . $titre . '", "' . intval($_GET['id']) . '", "' . secure($Sacces) . '", "1.1", "' . $_SESSION['m']['id'] . '", "' . time() . '", "' . time() . '", "0")');
									$dernierId = mysql_insert_id();
									
									$_SESSION['topic_lu' . $dernierId] = time();
									
							
												
									// Insertion du message dans la table message
									mysql_unbuffered_query('INSERT INTO message VALUES("", "' . $_SESSION['m']['pseudo'] . '", "' . $message . '", "' . $dernierId . '", "' . secure($Sacces) . '", "1", "' . time() . '", "0", "a", "' . $_SESSION['m']['id'] . '", "' . $_SERVER['REMOTE_ADDR'] . '")');
									
									$titre2 = '';
									$message = '';
								}
								
							else
							{
								avert('Vous venez de poster un topic il y a moins de ' . $config['forum']['tempsEntreChaqueTopic'] . ' secondes !');
							}
						}
						
						else
						{
							avert('Vous n\'√™tes pas connect√©.');
						}
					}
					
			else
			{
				avert('Le titre du topic n\'est pas valide.');
			}
		}
		else
		{
			avert('Le titre du topic est vide.');
		}
	}else
								{
									avert('Votre message contient des mots interdis.');
								}
							}
// Fin - Ajout d'un nouveau topic


// Debut - Bloc n¬∞1 dans une variable
	$reponse = mysql_unbuffered_query('SELECT titre FROM forum WHERE id=\'' . intval($_GET['id']) . '\'');
	$donnees = mysql_fetch_assoc($reponse);
	$GLOBALS['tf'] = $donnees['titre'];
	

	$droite = '<span class="favoris"><a class="statsl" href="stats.php?forum=' . intval($_GET['id']). '">Stats de ce forum</a> <a href="#" onclick="add_favoris(' . intval($_GET['id']) . ');"><img src="message/favoris.gif" alt="rien"/></a></span>';

	
	echo '
	<script>
function cacher()
{

	if(document.getElementById(\'texterecherche1\').style.display != \'none\')
	{
		document.getElementById(\'texterecherche1\').style.display = \'none\';
	}
	else
	{
		document.getElementById(\'texterecherche1\').style.display = \'\';
	}
		
}
</script>

	
	';
	
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
// Fin - Bloc n¬∞1 dans une variable


// Debut - Syst√®me de pagination
	$nombreDeMessagesParPage = $config['forum']['nbTopicParPage'];

	if(isset($_SESSION['m']['acces']) && $_SESSION['m']['acces'] > 92)
		$sql = '';
	else
		$sql = ' AND statut < 3';
	
	$retour = mysql_unbuffered_query("SELECT COUNT(*) AS nb_messages FROM topic WHERE idForum='" . intval($_GET['id']) . "'");
	$donnees = mysql_fetch_assoc($retour);
	$totalDesMessages = $donnees['nb_messages'];

	$nb_page = ceil($totalDesMessages / $nombreDeMessagesParPage);
	
	// On regarde √† quel page on se trouve :
	if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
	{
		$page = intval($_GET['page']);
	}
	else
	{
		$page = 1;
	}
	
	function pagination($nombreDeMessagesParPage)
	{
		$retour = mysql_unbuffered_query('SELECT COUNT(*) AS nb_messages FROM topic WHERE idForum=' . intval($_GET['id']) . ' AND ROUND(statut) != 0');
		$donnees = mysql_fetch_assoc($retour);
		$totalDesMessages = $donnees['nb_messages'];

		$nb_page = ceil($totalDesMessages / $nombreDeMessagesParPage);
		
		// On regarde √† quel page on se trouve :
		if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
		{
			$page = intval($_GET['page']);
		}
		else
		{
			$page = 1;
		}
		
		if($nb_page > 1)
		{
			echo '<div class="espaceModeration">';

			if($page == 1 AND $nb_page > 1)
			{
				echo '<span style="float: right;"><a href=" ' . have_url($_GET['id'] , 2 , FORUM_URL, $GLOBALS['tf']). '">Suivant <img src="images/fleched.gif" /></a>';
				
			}
			
			elseif($page > 1 AND $page < $nb_page)
			{
				
				echo '<span style="float: right"><a href="' . have_url(intval($_GET['id']), ($page + 1) , FORUM_URL, $GLOBALS['tf']) .'">Suivant <img src="images/fleched.gif" /></a> <a href="' . have_url($_GET['id'], $nb_page , FORUM_URL, $GLOBALS['tf']) . '">Dernier <img src="images/fleched.gif" /></a></span>
				<img src="images/flecheg.gif" /><a href="' . have_url(intval($_GET['id']), 1 , FORUM_URL, $GLOBALS['tf']) . '"> D√©but</a> <a href="forum-' . intval($_GET['id']) . '-' . ($page - 1) . '.htm"><img src="images/flecheg.gif" /> Pr√©c√©dent</a> 
				';
			}
			
			elseif($page == $nb_page AND $page > 1)
			{
				echo '<img src="images/flecheg.gif" /><a href="' . have_url(intval($_GET['id']), 1 , FORUM_URL, $GLOBALS['tf']) . '"> D√©but</a> <a href="' . have_url(intval($_GET['id']), ($page - 1) , FORUM_URL, $GLOBALS['tf']) . '"><img src="images/flecheg.gif" /> Pr√©c√©dent</a>';
			}
			echo '</div><br />';
		}
	}

	$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;
// Fin - Syst√®me de pagination

echo pagination($nombreDeMessagesParPage);

// Debut - Affichage de la page
	if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92)
	{
		$sql = '';
	}
	else
	{
		$sql = 'AND statut > 0';
	}

	$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS nbTopic FROM topic WHERE idForum=\'' . intval($_GET['id']) . '\' ' . $sql . '');
	$donnees = mysql_fetch_assoc($reponse);
	$nbTopic = $donnees['nbTopic'];
	
	// Bloc de recherche
	echo $bloc1;
	
	
	// Debut - Listage de Topics
		echo '
		
		<center><form method="post" id="checktopic">
		<div class="listageTopic">
			<table>
				<tr>
					<th class="col1">&nbsp;</th>
					';
					
					// Colonne pour supprimer topic
					if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
					{
						echo '<th class="colM"></th>';
					}
					
					echo '
					<th class="col2">Sujet</th>
					<th class="col4">Nb</th>
					<th class="col5">Dernier Msg</th>
					<th class="col3">Auteur</th>
					';
					
					// Colonne pour arme v5
					if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
					{
						echo '<th></th>';
					}
					
					echo '
				</tr>
				';
				
				if($nbTopic == 0) // S'il y a 0 topics pour ce forum on l'annonce
				{
					echo '<td colspan="6"><center class="admin" style="font-family: Arial"><b><u>Il n\'y a aucun sujet de discussion √† afficher.</u></b></center></td>';
				}
				else // On liste les topics
				{
					if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92)
					{
						$sql = '';
					}
					else
					{
						$sql = 'ROUND(topic.statut) > 0 AND ';
					}
					
					if(isset($_SESSION['m']['pseudo']))
					{
					$requete = mysql_query('SELECT Smileydanslestitres FROM membres WHERE id=' . $_SESSION['m']['id']);
					$info = mysql_fetch_assoc($requete);
					}
					else
					{
					echo '';
					}
					
					$reponse = mysql_query('
					SELECT topic.id AS `idDuTopic`, topic.acces, topic.statut, topic.titre, topic.pseudo, topic.lastTimestamp, topic.nbMessage AS `nbMessage`, topic.idPseudo, membres.id, membres.acces AS `membresAcc` 
					FROM topic, membres 
					WHERE ' . $sql . 'topic.idForum=' . intval($_GET['id']) . ' AND membres.id = topic.idPseudo 
					ORDER BY ROUND(topic.statut) DESC, lastTimestamp DESC
					LIMIT ' . $premierMessageAafficher . ', ' . $nombreDeMessagesParPage . '
					') or die(mysql_error());
					
					
					
					$i = 0; // Pour l'alternance des couleurs
					while($donnees = mysql_fetch_assoc($reponse))
					{	
						$nbMessage = $donnees['nbMessage'] - 1;
						
						if($nbMessage == '-1')
						{
							$nbMessage = 0;
						}
						
						// D√©claration de la couleur du dossier
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
						else
						{
							$dossier = 4;
						}
						
						// D√©claration de l'image du topic
						$arrayImages = array(
						'1.0' => 'lock',
						'1.1' => 'dossier' . $dossier . '',
						'2.1' => 'eplock',
						'2.0' => 'epingle',
						'0.0' => 'dossier' . $dossier .'',
						);
						
						// Pour l'alternance de couleur
						if($donnees['statut'] == 0) // Topic supprim√© voyant que par les admins en jaune
						{
							$bloc = 'tr3';
						}
						else
						{
							if($i % 2 == 0)
							{
								$bloc = 'tr1';
							}	
							else
							{
								$bloc = 'tr2';	
							}
						}
						
						
						// Debut - Couleur du Pseudo
							if($donnees['membresAcc'] == 100) // Admin
							{
								$pseudo = ' <strong>' . ($COLP->afficher(100, $donnees['pseudo'])) . '</strong>';
							}
							elseif($donnees['membresAcc'] == 95) // Directeur
							{
								$pseudo = ' <strong>' . ($COLP->afficher(95, $donnees['pseudo'])) . '</strong>';
							}
							elseif($donnees['membresAcc'] == 90) // Super modo
							{
								$pseudo = ' <strong>' . ($COLP->afficher(90, $donnees['pseudo'])) . '</strong>';
							}
							elseif($donnees['membresAcc'] == 50) // ModÈrateur
							{
								$pseudo = ' <strong>' . ($COLP->afficher(50, $donnees['pseudo'])) . '</strong>';
							}
							elseif($donnees['membresAcc'] == 45) // Pubbeur
							{
								$pseudo = ' <strong>' . ($COLP->afficher(45, $donnees['pseudo'])) . '</strong>';
							}
							elseif($donnees['membresAcc'] == 98) // Codeur
							{
								$pseudo = ' <strong>' . ($COLP->afficher(98, $donnees['pseudo'])) . '</strong>';
							}
							else // Membre
							{
								$pseudo = ' <strong>' . ($COLP->afficher(10, $donnees['pseudo'])) . '</strong>';
							}
						// Fin - Couleur du Pseudo
						
						// Pour aller √† la derni√®re page du topic
						$totalDesMessages2 = $donnees['nbMesssage'];

						$nb_page2 = ceil($nbMessage / $config['forum']['nbMessageParPage']);
						
						if($donnees['lastTimestamp'] >= $_SESSION['tmp_derniere_visite']) 
						{
							// Dans ce cas, le topic est bel et bien lu
							if(isset($_SESSION['topic_lu'.$donnees['idDuTopic']]) && $_SESSION['topic_lu'.$donnees['idDuTopic']] > $donnees['lastTimestamp'])
							{
								$nbMessage = $nbMessage;
							}
							// Autrement, c'est normal, il s'agit bien d'un nouveau message non-lu
							else 
							{
								$nbMessage = '<b>' . $nbMessage . '</b>';
							}
						}       
						else 
						{
							$nbMessage = $nbMessage;
						}
						
						
								
							echo '
							<tr class="' . $bloc . '">
								<td class="col11"><a href="' . have_url($donnees['idDuTopic'] , $nb_page2 , TOPIC_URL, $titre) . '"><img src="images/' . $arrayImages[$donnees['statut']] . '.png" /></a></td>
								';
								
								// Si on est mod√©rateur ou Admin on a acc√®s au bouton de supression
								if($donnees['statut'] < 2.3 AND $donnees['statut'] > 0) // Supprimer
								{
									if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
									{
										echo '<td><a href="forum.php?id=' . intval($_GET['id']) . '&amp;supprimerTopic=' . $donnees['idDuTopic'] . '" onclick="reponse = confirm(\'√ätes-vous S√õR(E) de vouloir SUPPRIMER ce topic ?\'); return reponse;"><img src="message/supprimer.gif" /></a></td>';
									}
								}
								elseif($donnees['statut'] == 0.0) // Restaurer
								{
									if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92)
									{
										echo '<td><a href="forum.php?id=' . intval($_GET['id']) . '&amp;voirTopic=' . $donnees['idDuTopic'] . '" onclick="reponse = confirm(\'√ätes-vous S√õR(E) de vouloir REMETTRE ce topic ?\'); return reponse;"><img src="message/voir.gif" /></a></td>';
									}
								}
								if($info['Smileydanslestitres'] == 1)
								{
								$titre = bbcode(stripslashes(nl2br(htmlspecialchars($donnees['titre']))));
								}
								else
								{
								$titre = stripslashes(nl2br(htmlspecialchars($donnees['titre'])));
								}
								
								echo '
								<td class="col12"><b><a href="' . have_url($donnees['idDuTopic'] , $nb_page , TOPIC_URL, $titre) . '">' . wordwrap($titre, 400, "\n", true) . '</a></b></td>
								<td class="col14">' . $nbMessage . '</td>
								<td class="col15">' . date('d/m/Y\ H\hi', $donnees['lastTimestamp']) . '</td>
								<td class="col13">' .$pseudo. '</td>
								';
								
								// Arme v5
								if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
								{
									echo '<td><input type="checkbox" name="supprimer[]" value="' . $donnees['idDuTopic'] . '" style="margin: 0px" /></td>';
									$armev5 = 1;
								}
								
								echo '
							</tr>
							';
						$i++; // On incr√©mente pour changer la couleur
					}
				}
				echo '
			';
			
			// Formulaire de l'arme v5
			if(isset($armev5))
			{	
				echo '
				<tr class="tr2">
				<td colspan="8">
				<p align="center">
					<a href="Javascript:void(0)" onclick="Check_all(\'checktopic\', true); return false;">Tout cocher</a> / <a href="Javascript:void(0)" onclick="Check_all(\'checktopic\', false); return false;">Tout d√©cocher</a><br />
					<select name="changeStatut">
						<option value="11">Normal</option>
						<option value="10">Locker</option>
						<option value="20">Epingler</option>
						<option value="21">Epingler et Locker</option>
						<option value="12">Supprimer</option>
						';
						if(isset($_SESSION['m']['acces']) && $_SESSION['m']['acces'] > 92)
						{
						echo '
						<option value="1">Restaurer</option>
						';
						}
						echo '
					</select>
				
					<input type="submit" value="Validez" />
				</p>
				</td></tr>
				';
			}
			echo '
			</table>
		</div>
		</form>
		<br /></center>
		';
	// Fin - Listage de Topics
	
	// Syst√®me pagination
	echo' 
	<center><a href="forum.php?id=' . intval($_GET['id']) . '&amp;marquerLu=1" >Marquer tous les messages comme lu</a></center>
			';
	pagination($nombreDeMessagesParPage);
	
	// Debut - Verification si on peut poster ou non
		if(!isset($_SESSION['m']['pseudo']))
		{
			include('pied.php');
			exit;
		}

		$reponse = mysql_query('SELECT * FROM kick WHERE idPseudo=' . $_SESSION['m']['id'] . ' AND idForum=' . intval($_GET['id']) . '') OR die(mysql_error());
		$result = mysql_num_rows($reponse);

		if((int) $result != 0)
		{
			echo '<br /><b><center>Vous avez √©t√© kicker de ce forum pour une dur√©e de 72h.</center></b>';
			include('pied.php');
			exit;
		}
	// Fin - Verification si on peut poster ou non
	
	// Debut - Formulaire d'ajout topic		
	
	$reponse = mysql_query('SELECT avatar, signature FROM membres WHERE id=' . $_SESSION['m']['id'] . '');
$donnees = mysql_fetch_assoc($reponse);

		echo '<br><br><br><div class="bloc2"><h3>Poster un nouveau topic</h3>
<div class="postpic">
				<form method="post" id="form_post" name="form_post" action="forum-' . intval($_GET['id']) . '.html">
		         <a href="option.html" padding-right: 5px; padding-right: 5px;">' . $_SESSION['m']['pseudo'] . '</a> 
				<label for="titre">* Titre du sujet :</label><br /> <span style=" float: left;"><input type="text" name="titre" id="titre" maxlength="40" size="100%" value="'; if(isset($titre2)) { echo stripslashes(htmlspecialchars($titre2)); } echo '" /></span><br />
					<br />
					<input type="hidden" name="pseudo" value="' . $_SESSION['m']['pseudo'] . '" />
					
					<img src="message/souligne.png" alt="souligner" onclick="storeCaret(\'u\')"/>  <img src="message/italique.png" alt="italique" onclick="storeCaret(\'i\')"/>  <img src="message/gras.png" alt="gras" onclick="storeCaret(\'b\')"/> <img src="message/barre.png" alt="barrer" onclick="storeCaret(\'s\')"/> </a> <br />
					';//<span style="float: right;">';
//echo bbCode(":hap:"); 
//echo'</span><div style="position: absolute; left: 655px; display: block; z-index: 3; padding-top: 0pt; margin-top: 50px;" id="displayed_easysmiley"><table style="text-align: center; width: 300px; height: 340px; padding: 10px 10px 10px 22px; background: url(&quot;http://www.noelshack.com/uploads/fond047017.png&quot;) no-repeat scroll 0% 0% transparent;" cellpadding="0" cellspacing="0"><tr><tr><table style="width: 100%;position:relative;top:-340px;"><tr><td><img style="cursor: pointer;" alt=":)" title=":)" src="http://image.jeuxvideo.com/smileys_img/1.gif"></td><td><img style="cursor: pointer;" alt=":-)" title=":-)" src="http://image.jeuxvideo.com/smileys_img/46.gif"></td><td><img style="cursor: pointer;" alt=":-)))" title=":-)))" src="http://image.jeuxvideo.com/smileys_img/23.gif"></td><td><img style="cursor: pointer;" alt=":hap:" title=":hap:" src="http://image.jeuxvideo.com/smileys_img/11.gif"></td><td><img style="cursor: pointer;" alt=":content:" title=":content:" src="http://image.jeuxvideo.com/smileys_img/24.gif"></td><td><img style="cursor: pointer;" alt=":oui:" title=":oui:" src="http://image.jeuxvideo.com/smileys_img/37.gif"></td><td><img style="cursor: pointer;" alt=":cool:" title=":cool:" src="http://image.jeuxvideo.com/smileys_img/26.gif"></td><td><img style="cursor: pointer;" alt=":-D" title=":-D" src="http://image.jeuxvideo.com/smileys_img/40.gif"></td><td><img style="cursor: pointer;" alt=":rire:" title=":rire:" src="http://image.jeuxvideo.com/smileys_img/39.gif"></td><td><img style="cursor: pointer;" alt=":rire2:" title=":rire2:" src="http://image.jeuxvideo.com/smileys_img/41.gif"></td><td><img style="cursor: pointer;" alt=":o))" title=":o))" src="http://image.jeuxvideo.com/smileys_img/12.gif"></td><td><img style="cursor: pointer;" alt=":sournois:" title=":sournois:" src="http://image.jeuxvideo.com/smileys_img/67.gif"></td></tr><tr><td><img style="cursor: pointer;" alt=":snif:" title=":snif:" src="http://image.jeuxvideo.com/smileys_img/20.gif"></td><td><img style="cursor: pointer;" alt=":snif2:" title=":snif2:" src="http://image.jeuxvideo.com/smileys_img/13.gif"></td><td><img style="cursor: pointer;" alt=":ouch:" title=":ouch:" src="http://image.jeuxvideo.com/smileys_img/22.gif"></td><td><img style="cursor: pointer;" alt=":ouch2:" title=":ouch2:" src="http://image.jeuxvideo.com/smileys_img/57.gif"></td><td><img style="cursor: pointer;" alt=":p)" title=":p)" src="http://image.jeuxvideo.com/smileys_img/7.gif"></td><td><img style="cursor: pointer;" alt=":(" title=":(" src="http://image.jeuxvideo.com/smileys_img/45.gif"></td><td><img style="cursor: pointer;" alt=":-(" title=":-(" src="http://image.jeuxvideo.com/smileys_img/14.gif"></td><td><img style="cursor: pointer;" alt=":-((" title=":-((" src="http://image.jeuxvideo.com/smileys_img/15.gif"></td><td><img style="cursor: pointer;" alt=":nonnon:" title=":nonnon:" src="http://image.jeuxvideo.com/smileys_img/25.gif"></td><td><img style="cursor: pointer;" alt=":non2:" title=":non2:" src="http://image.jeuxvideo.com/smileys_img/33.gif"></td><td><img style="cursor: pointer;" alt=":nah:" title=":nah:" src="http://image.jeuxvideo.com/smileys_img/19.gif"></td><td><img style="cursor: pointer;" alt=":non:" title=":non:" src="http://image.jeuxvideo.com/smileys_img/35.gif"></td></tr><tr><td><img style="cursor: pointer;" alt=":hum:" title=":hum:" src="http://image.jeuxvideo.com/smileys_img/68.gif"></td><td><img style="cursor: pointer;" alt=":gba:" title=":gba:" src="http://image.jeuxvideo.com/smileys_img/17.gif"></td><td><img style="cursor: pointer;" alt=":mac:" title=":mac:" src="http://image.jeuxvideo.com/smileys_img/16.gif"></td><td><img style="cursor: pointer;" alt=":pacg:" title=":pacg:" src="http://image.jeuxvideo.com/smileys_img/9.gif"></td><td><img style="cursor: pointer;" alt=":pacd:" title=":pacd:" src="http://image.jeuxvideo.com/smileys_img/10.gif"></td><td><img style="cursor: pointer;" alt=":-p" title=":-p" src="http://image.jeuxvideo.com/smileys_img/31.gif"></td><td><img style="cursor: pointer;" alt=":peur:" title=":peur:" src="http://image.jeuxvideo.com/smileys_img/47.gif"></td><td><img style="cursor: pointer;" alt=":fou:" title=":fou:" src="http://image.jeuxvideo.com/smileys_img/50.gif"></td><td><img style="cursor: pointer;" alt=":fier:" title=":fier:" src="http://image.jeuxvideo.com/smileys_img/53.gif"></td><td><img style="cursor: pointer;" alt=":sarcastic:" title=":sarcastic:" src="http://image.jeuxvideo.com/smileys_img/43.gif"></td><td><img style="cursor: pointer;" alt=":doute:" title=":doute:" src="http://image.jeuxvideo.com/smileys_img/28.gif"></td><td><img style="cursor: pointer;" alt=":malade:" title=":malade:" src="http://image.jeuxvideo.com/smileys_img/8.gif"></td></tr><tr><td><img style="cursor: pointer;" alt=":bravo:" title=":bravo:" src="http://image.jeuxvideo.com/smileys_img/69.gif"></td><td><img style="cursor: pointer;" alt=":bave:" title=":bave:" src="http://image.jeuxvideo.com/smileys_img/71.gif"></td><td><img style="cursor: pointer;" alt=":g)" title=":g)" src="http://image.jeuxvideo.com/smileys_img/3.gif"></td><td><img style="cursor: pointer;" alt=":d)" title=":d)" src="http://image.jeuxvideo.com/smileys_img/4.gif"></td><td><img style="cursor: pointer;" alt=":cd:" title=":cd:" src="http://image.jeuxvideo.com/smileys_img/5.gif"></td><td><img style="cursor: pointer;" alt=":globe:" title=":globe:" src="http://image.jeuxvideo.com/smileys_img/6.gif"></td><td><img style="cursor: pointer;" alt=":ok:" title=":ok:" src="http://image.jeuxvideo.com/smileys_img/36.gif"></td><td><img style="cursor: pointer;" alt=":noel:" title=":noel:" src="http://image.jeuxvideo.com/smileys_img/18.gif"></td><td><img style="cursor: pointer;" alt=":mort:" title=":mort:" src="http://image.jeuxvideo.com/smileys_img/21.gif"></td><td><img style="cursor: pointer;" alt=":honte:" title=":honte:" src="http://image.jeuxvideo.com/smileys_img/30.gif"></td><td><img style="cursor: pointer;" alt=":monoeil:" title=":monoeil:" src="http://image.jeuxvideo.com/smileys_img/34.gif"></td><td><img style="cursor: pointer;" alt=":rouge:" title=":rouge:" src="http://image.jeuxvideo.com/smileys_img/55.gif"></td></tr></table><table style="width: 100%;"><tr><td><img style="cursor: pointer;" alt=":coeur:" title=":coeur:" src="http://image.jeuxvideo.com/smileys_img/54.gif"></td><td><img style="cursor: pointer;" alt=":question:" title=":question:" src="http://image.jeuxvideo.com/smileys_img/2.gif"></td><td><img style="cursor: pointer;" alt=":fete:" title=":fete:" src="http://image.jeuxvideo.com/smileys_img/66.gif"></td><td><img style="cursor: pointer;" alt=":ange:" title=":ange:" src="http://image.jeuxvideo.com/smileys_img/60.gif"></td><td><img style="cursor: pointer;" alt=":diable:" title=":diable:" src="http://image.jeuxvideo.com/smileys_img/61.gif"></td><td><img style="cursor: pointer;" alt=":sleep:" title=":sleep:" src="http://image.jeuxvideo.com/smileys_img/27.gif"></td><td><img style="cursor: pointer;" alt=":gni:" title=":gni:" src="http://image.jeuxvideo.com/smileys_img/62.gif"></td><td><img style="cursor: pointer;" alt=":banzai:" title=":banzai:" src="http://image.jeuxvideo.com/smileys_img/70.gif"></td></tr></table><table style="width: 100%;"><tr><td><img style="cursor: pointer;" alt=":spoiler:" title=":spoiler:" src="http://image.jeuxvideo.com/smileys_img/63.gif"></td><td><img style="cursor: pointer;" alt=":sors:" title=":sors:" src="http://image.jeuxvideo.com/smileys_img/56.gif"></td><td><img style="cursor: pointer;" alt=":rechercher:" title=":rechercher:" src="http://image.jeuxvideo.com/smileys_img/38.gif"></td><td><img style="cursor: pointer;" alt=":hs:" title=":hs:" src="http://image.jeuxvideo.com/smileys_img/64.gif"></td></tr><tr><td><img style="cursor: pointer;" alt=":lol:" title=":lol:" src="http://image.jeuxvideo.com/smileys_img/32.gif"></td><td><img style="cursor: pointer;" alt=":dpdr:" title=":dpdr:" src="http://image.jeuxvideo.com/smileys_img/49.gif"></td><td><img style="cursor: pointer;" alt=":desole:" title=":desole:" src="http://image.jeuxvideo.com/smileys_img/65.gif"></td><td><img style="cursor: pointer;" alt=":merci:" title=":merci:" src="http://image.jeuxvideo.com/smileys_img/58.gif"></td></tr><tr><td><img style="cursor: pointer;" alt=":svp:" title=":svp:" src="http://image.jeuxvideo.com/smileys_img/59.gif"></td><td><img style="cursor: pointer;" alt=":salut:" title=":salut:" src="http://image.jeuxvideo.com/smileys_img/42.gif"></td><td><img style="cursor: pointer;" alt=":hello:" title=":hello:" src="http://image.jeuxvideo.com/smileys_img/29.gif"></td><td><img style="cursor: pointer;" alt=":up:" title=":up:" src="http://image.jeuxvideo.com/smileys_img/44.gif"></td></tr></table><table style="width: 100%;"><tr><td><img style="cursor: pointer;" alt=":bye:" title=":bye:" src="http://image.jeuxvideo.com/smileys_img/48.gif"></td><td><img style="cursor: pointer;" alt=":gne:" title=":gne:" src="http://image.jeuxvideo.com/smileys_img/51.gif"></td><td><img style="cursor: pointer;" alt=":dehors:" title=":dehors:" src="http://image.jeuxvideo.com/smileys_img/52.gif"></td></tr></table></tr></tr></table></div>';
echo'
<textarea wrap="virtual" onmouseover="this.focus();" style="background: url(\'http://www.auplod.com/u/dlopua660d.png\') bottom right no-repeat white; width:90%;" name="message" id="message" rows="11" cols="47">'; if(isset($message2)) { echo stripslashes($message2); } echo '</textarea>';
				   
 echo'<br />
					
					' . bbCode(text_i($donnees['signature'])) . '<br />
					
					 <span style="float:right; margin-right:15px;"><input type="button" action="topic.php" onclick="return apercu();" class="bouton" value="Apercu"/> <input type="submit" value="Poster" class="bouton"/></span>
				</form>
			<form action="apercu.html" method="post" name="apercuMessage"  style="display: inline;">
		 <input type="hidden" name="pseudo" />
 
		 <input type="hidden" name="message" /> 
		</form>
				</div></div>
			    </div>
		  	 </div>
	     </div></div>
<br />
		';
	// Fin - Formulaire d'ajout topic

	include('pied.php');

	if(isset($_SESSION['m']['moderateur'][$_GET['id']]) OR $_SESSION['m']['acces'] == 50)
	{
		$_SESSION['m']['acces'] = 10;
	}

// Fin - Affichage de la page