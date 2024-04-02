<?php
include('base.php');
if(isset($_SESSION['m']['id']))
	{
		$reponse = mysql_query('SELECT * FROM kick WHERE idPseudo=' . $_SESSION['m']['id'] . ' AND idForum=' . intval(intval($_GET['id'])) . '') OR die(mysql_error());
		$result = mysql_num_rows($reponse);

		if((int) $result != 0)
		{
			unset($_POST['message']);
		}
	}


// Debut - Autorisation accÃ¨s de page
	if(!isset($_GET['id']) OR !ctype_digit($_GET['id']))
	{
		include('tete.php');
		avert('Le lien n\'est pas correct');
		include('pied.php');
		exit;
	}
	
	if($_GET['page'] == 0 AND isset($_SESSION['m']['acces']))
	{
		$repondre = 1;
	}
	
	
	// Vérifions si le topic existe bien
	if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92) // On est admin on peut voir les topics supprimés
	{
		$reponse = mysql_query('SELECT * FROM topic WHERE id=' . intval(intval($_GET['id'])) . '') OR die(mysql_error());
	}
	else
	{
		$reponse = mysql_query('SELECT * FROM topic WHERE id=' . intval(intval($_GET['id'])) . ' AND statut > 0') OR die(mysql_error());
	}
	
	$result = mysql_num_rows($reponse);

	if((int) $result == 0)
	{
		include('tete.php');
		avert('Ce topic n\'existe pas.');
		include('pied.php');
		exit;
	}
	
	// Récupération de l'id du Forum (trÃ¨s utile sur toute la longueur du script)
	$reponse = mysql_query('SELECT idForum FROM topic WHERE id=' . intval($_GET['id']));
	$donnees = mysql_fetch_assoc($reponse);
	$idForum = $donnees['idForum'];
	
	if(isset($_SESSION['m']['moderateur'][$donnees['idForum']]) AND $_SESSION['m']['moderateur'][$donnees['idForum']] == 1) // Si on est modérateur de ce forum
	{
		$_SESSION['m']['acces'] = 50;
	}
	else // Vérification si c'est pas un forum privé destiné qu'aux modérateurs
	{
		$reponse = mysql_query('SELECT statut FROM forum WHERE id=' . $idForum . '');
		$donnees = mysql_fetch_assoc($reponse);
		
		if($donnees['statut'] == 1)
		{
			if(isset($_SESSION['m']['id']))
			{
				$reponse2 = mysql_query('SELECT * FROM moderateurs WHERE idPseudo=' . $_SESSION['m']['id'] . '') or die(mysql_error());
				$result = mysql_num_rows($reponse2);
				
				if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] != 100 AND $_SESSION['m']['acces'] != 90 )
				{	
					if((int) $result == 0)
					{
						include('tete.php');
						avert('Ce forum n\'existe pas.');
						include('pied.php');
						exit;
					}
				}
			}
			else
			{
				include('tete.php');
				avert('Ce forum n\'existe pas.');
				include('pied.php');
				exit;
			}
		}
	}	
// Fin - Autorisation accÃ¨s de page

include('tete.php');

// Debut - Fonctions (citer, avertir, ...)

	// Debut - Topic Lu (Nouveaux messages)
		$_SESSION['topic_lu' . intval($_GET['id'])] = time() + 1; // Ce topic est lu puisqu'on est dessus
	// Fin - Topic Lu (Nouveaux messages)
	
	// Debut - Déplacer un topic 

		if(isset($_POST['deplacerTopic'], $_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50) 
		{ 
			$dT = db_sql::escape($_POST['deplacerTopic']); 
			$bdd->sql_query('UPDATE topic SET idForum=' . $dT . ' WHERE id=' . intval($_GET['id']), false, true); 
		}

	// Debut - Avertir
		if(isset($_GET['avertir'], $_SESSION['m']['id']) AND ctype_digit($_GET['avertir']))
		{
			// Vérifions si le message n'a pas déjÃ  été avertis
			$reponse = mysql_query('SELECT * FROM avertir WHERE idMessage=' . $_GET['avertir']) OR die(mysql_error());
			$result = mysql_num_rows($reponse);

			if((int) $result == 0)
			{
				mysql_query('INSERT INTO avertir VALUES("", "' . intval($_GET['avertir']) . '", "' . intval(intval($_GET['id'])) . '", "' . $page . '")');
				avert('Le message a bien &eacute;t&eacute; signal&eacute; !');
			}
			else
			{
				avert('Ce message a d&eacute;jÃ  &eacute;t&eacute; averti !');
			}
		}
		
		// Enlever l'avertissement
		if(isset($_SESSION['m']['acces'], $_GET['deavertir']) AND $_SESSION['m']['acces'] > 92)
		{
			$bdd->sql_query('DELETE FROM avertir WHERE id=' . intval($_GET['deavertir']), false, true);
		}
	// Fin - Avertir
		if(isset($_POST['supprimer'], $_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
				{
					foreach($_POST['supprimer'] as $idTopic)
					{
						if(isset($idTopic) AND ctype_digit($idTopic))
						{
						if ($_POST['ChoixDeKira'] == "Kira")
						{
							mysql_query('UPDATE message SET statut=2 WHERE id=' . intval($idTopic));
							$reponse = mysql_query('SELECT topic.nbMessage AS `nb`, topic.id
							FROM message 
							INNER JOIN topic ON topic.id = message.idTopic
							WHERE message.id = ' . intval($idTopic) . '
							');
							$donnees = mysql_fetch_assoc($reponse);
							$lavert++;
						}
						
						else
						{
						$lavert++;
						mysql_query('UPDATE message SET statut=0 WHERE id=' . intval($idTopic));
						$reponse = mysql_query('SELECT topic.nbMessage AS `plop`, topic.id
						FROM message 
						INNER JOIN topic ON topic.id = message.idTopic
						WHERE message.id = ' . intval($idTopic) . '
						');
						$donnees = mysql_fetch_assoc($reponse);
						mysql_query('UPDATE topic SET nbMessage=' . ($donnees['plop'] + 1) . ' WHERE id=' . intval($donnees['id']));

						}
						
						}
						else
						{
						avert('Aucun message s&eacute;lectionn&eacute;.');
						}
					}
						if ($_POST['ChoixDeKira'] != "Kira")
						{
						avert("Restauration des ".$lavert." messages complet&eacute;e. [/Micaiah]");
						}
						elseif ($_POST['ChoixDeKira'] != "Misa")
						{
						avert("Les ".$lavert." messages ont bien &eacute;t&eacute; supprim&eacute;s. [/Kira]");
						}
						else
						{
						avert('Une erreur s\'est produite.');
						}

				}
	// Debut - Kick
		if(isset($_GET['kicker'], $_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50) // Fonction que pour Admin/modo
		{
			// On vérifie que le pseudo n'est pas déjà  kicker dans ce forum
			$reponse = mysql_query('SELECT * FROM kick WHERE idPseudo=' . $_GET['kicker'] . ' AND idForum=' . $idForum) OR die(mysql_error());
			$result = mysql_num_rows($reponse);

			if((int) $result != 0)
			{
				avert('<b>Ce pseudo est d&eacute;j&agrave; kick&eacute; !');
			}
			else
			{
				$reponse = mysql_query('SELECT pseudo, acces FROM membres WHERE id=' . $_GET['kicker']);
				$donnees = mysql_fetch_assoc($reponse);
				
				$reponse = mysql_query('SELECT * FROM moderateurs WHERE idPseudo=' . $_GET['kicker'] . ' AND idForum=' . $idForum . '');
				$result = mysql_num_rows($reponse);
				
				if($donnees['acces'] != 100)
				{
					if($donnees['acces'] != 98)
					{
						if($donnees['acces'] != 95)
						{
							if($donnees['acces'] != 90)
							{
								if((int) $result == 0)
								{
									mysql_query('INSERT INTO kick VALUES("", "' . $_GET['kicker'] . '", "' . $idForum . '", "' . time() . '")') or die (mysql_error());
									avert($donnees['pseudo'] . ' a bien &eacute;t&eacute; kick&eacute; du forum.');
								}
								else
								{
									avert('Vous ne pouvez pas kicker un mod&eacute;rateur.');
								}
							}
							else
							{
								avert('Vous ne pouvez pas kicker un Super Modo.');
							}
						}
						else
						{
							avert('Vous ne pouvez pas kicker un Directeur.');
						}
					}
					else
					{
						avert('Vous ne pouvez pas kicker un Codeur.');
					}
				}
				else
				{
					avert('Vous ne pouvez pas kicker un Administrateur.');
				}
			}
		}
	
		// Dékicker
		if(isset($_SESSION['m']['acces'], $_GET['dekick']) AND $_SESSION['m']['acces'] >= 50)
		{
			mysql_query('DELETE FROM kick WHERE idPseudo=' . intval($_GET['dekick']) . ' AND idForum=' . $idForum);
		}
		
		// Dékickement automatique au bout de 72h
		$reponse = mysql_query('SELECT timestamp FROM kick WHERE timestamp < ' . (time() - 3600 * 72) . '');
		$result = mysql_num_rows($reponse);
				
		if($result != 0)
		{
			$bdd->sql_query('DELETE FROM kick WHERE timestamp < ' . (time() - 3600 * 72), false, true);
		}
		
		if(isset($_GET['bannirMembre'], $_GET['idMessage'], $_SESSION['m']['acces']) && ctype_digit($_GET['bannirMembre']) && ctype_digit($_GET['idMessage']) && $_SESSION['m']['acces'] >= 90)
		{
			$reponse = mysql_query('SELECT pseudo, acces FROM membres WHERE id=' . $_GET['bannirMembre'] . '');
			$donnees = mysql_fetch_assoc($reponse);
			
			if($donnees['acces'] != 100)
			{
				if($donnees['acces'] != 98)
				{
					if($donnees['acces'] != 95)
					{
						if($donnees['acces'] != 90)
						{
							mysql_query('INSERT INTO bann VALUES("", "' . $_GET['bannirMembre'] . '", "' . $_GET['idMessage'] . '")');
							mysql_query("UPDATE membres SET acces='1' WHERE id='" . intval($_GET['bannirMembre']) . "'") or die (mysql_error());
						
							$reponse = mysql_query('
							SELECT membres.pseudo, membres.email, message.message 
							FROM membres 
							INNER JOIN message ON message.idPseudo = membres.id
							WHERE membres.id=' . $_GET['bannirMembre'] . ' AND message.id=' . $_GET['idMessage']
							);
							
							$donnees = mysql_fetch_assoc($reponse);
							avert($donnees['pseudo'] . ' a bien &eacute;t&eacute; banni du forum.');
							$message = '
							<h1>Votre pseudo ' . $donnees['pseudo'] . ' vient d\'etre banni</h1>
							<br>
							<b>Message pour lequel vous venez d\'etre banni :</b><br />
							' . stripslashes(htmlspecialchars($donnees['message'])) . '<br /><br />
							';
					 
							$message = '<html><head></head><body>' . $message . '</body></html>';
														   
							$sujet = 'OriginsForum : Bannissement de votre pseudo';
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= 'From: ' . $adresseSite . '';
					 
							mail($donnees['email'], $sujet, $message, $headers);
			
						}
						else
						{
							avert('Vous ne pouvez pas bannir un Super Modo.');
						}
					}
					else
					{
						avert('Vous ne pouvez pas bannir un Directeur.');
					}
				}
				else
				{
					avert('Vous ne pouvez pas bannir un Codeur.');
				}
			}
			else
			{
				avert('Vous ne pouvez pas bannir un Administrateur.');
			}
		}
	// Fin - Bannissement
	
	// Debut - Ignorer
		if(isset($_GET['ignorerMembre']))
		{
			$_SESSION['ignore'][intval($_GET['ignorerMembre'])] = 1;
		}

		if(isset($_GET['designorerMembre']))
		{
			$_SESSION['ignore'][intval($_GET['ignorerMembre'])] = 0;
		}
	// Fin - Ignorer
	
	// Debut - Citer
		if(isset($_GET['citer']) AND ctype_digit($_GET['citer']))
		{
			$reponse = mysql_query('SELECT message, pseudo FROM message WHERE id=' . intval($_GET['citer']) . '');
			$donnees = mysql_fetch_assoc($reponse);
			
			$Cmessage = text_i($donnees['message']);
			$Cpseudo = $donnees['pseudo'];
		}
	// Fin - Citer
	
	// Debut - Changer Statut Topic
		if(isset($_SESSION['m']['acces'], $_GET['changeStatut']) && ctype_digit($_GET['changeStatut']) && $_SESSION['m']['acces'] >= 50)
		{
			$arrayStatut = array(
			'10' => '1.0',
			'11' => '1.1',
			'20' => '2.0',
			'21' => '2.1'
			);
			
			mysql_query('UPDATE topic SET statut=' . $arrayStatut[$_GET['changeStatut']] . ' WHERE id=' . intval($_GET['id'])) or die (mysql_error());
		}
	// Fin - Changer Statut Topic
	
	// Debut - Supprimer/Restaurer message
		if(isset($_SESSION['m']['pseudo'], $_GET['supprimerMessage']) AND $_SESSION['m']['acces'] >= 50 AND ctype_digit($_GET['supprimerMessage']))
		{
			if(isset($_GET['statut']) AND $_GET['statut'] == 1) // Si premier message du topic == Topic supprimé
			{
				$reponse = mysql_query('SELECT idTopic FROM message WHERE id=\'' . intval($_GET['supprimerMessage']) . '\'');
				$donnees = mysql_fetch_assoc($reponse);
				
				mysql_query('UPDATE topic SET statut=0 WHERE id=' . $donnees['idTopic']);
			}
			else
			{
				mysql_query('UPDATE message SET statut=2 WHERE id=' . intval($_GET['supprimerMessage']));
				$reponse = mysql_query('SELECT topic.nbMessage AS `nb`, topic.id
				FROM message 
				INNER JOIN topic ON topic.id = message.idTopic
				WHERE message.id = ' . intval($_GET['supprimerMessage']) . '
				');
				$donnees = mysql_fetch_assoc($reponse);
				mysql_query('UPDATE topic SET nbMessage=' . ($donnees['nb'] - 1) . ' WHERE id=' . intval($donnees['id']));
			}
		}
		
		if(isset($_SESSION['m']['pseudo'], $_GET['voirMessage']) AND $_SESSION['m']['acces'] >= 50 AND ctype_digit($_GET['voirMessage']))
		{
			mysql_query('UPDATE message SET statut=1 WHERE id=' . intval($_GET['voirMessage']));
			$reponse = mysql_query('SELECT topic.nbMessage AS `plop`, topic.id
			FROM message 
			INNER JOIN topic ON topic.id = message.idTopic
			WHERE message.id = ' . intval($_GET['voirMessage']) . '
			');
			$donnees = mysql_fetch_assoc($reponse);
			mysql_query('UPDATE topic SET nbMessage=' . ($donnees['plop'] + 1) . ' WHERE id=' . intval($donnees['id']));
		}
	// Fin - Supprimer message
	
	// Debut - Bannir Ip
		if(isset($_GET['bannIp'], $_GET['bannId'], $_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92)
		{
			$reponse = mysql_query('SELECT pseudo, acces FROM membres WHERE id=' . $_GET['bannId'] . '');
			$donnees = mysql_fetch_assoc($reponse);
			
			if($donnees['acces'] != 100)
			{
				if($donnees['acces'] != 98)
				{
					if($donnees['acces'] != 95)
					{
						if($donnees['acces'] != 90)
						{
							mysql_query('INSERT INTO bannIp VALUES("", "' . db_sql::escape($_GET['bannIp']) . '")');
							mysql_query("UPDATE membres SET bannIp='1' WHERE ip='" . db_sql::escape($_GET['bannIp']) . "'");
							avert($donnees['pseudo'] . ' a bien &eacute;t&eacute; banni Ip du forum');
						}
						else
						{
							avert('Vous ne pouvez pas bannir un Super Modo.');
						}
					}
					else
					{
						avert('Vous ne pouvez pas bannir un Directeur.');
					}
				}
				else
				{
					avert('Vous ne pouvez pas bannir un Codeur.');
				}
			}
			else
			{
				avert('Vous ne pouvez pas bannir un Administrateur.');
			}
		}
	// Fin - Bannir Ip
         
	// Debut - Editer
		if(isset($_POST['Emessage']))
		{
			$Emessage = secure($_POST['Emessage']);
			
			$reponse = mysql_query('SELECT idPseudo, timestamp FROM message WHERE id=' . intval($_POST['idMessage']) . ' LIMIT 1');
			$donnees = mysql_fetch_assoc($reponse);
			
			if($donnees['idPseudo'] == $_SESSION['m']['id'] OR $_SESSION['m']['acces'] > 92)
			{
				if((time() - $config['forum']['tempsFonctionEditer']) < $donnees['timestamp'] OR $_SESSION['m']['acces'] > 92)
				{
				
					$bdd->sql_query("UPDATE message SET message='".$Emessage."', timestampModif='".time()."', auteurModif='".$_SESSION['m']['pseudo']."' WHERE id='".db_sql::escape($_POST['idMessage'])."'", false, true);
					
					avert('
					<b>Votre message a bien &eacute;t&eacute; modifi&eacute;.</b><br />
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $_GET['page'] . '">Cliquez pour revenir au topic</a>
					');
				}
				else
				{
					avert('Vous avez d&eacute;pass&eacute; la limite de temps pour ce message.');
				}
			}
			else
			{
				avert('Vous ne pouvez &eacute;diter ce message');
			}
			
			include('pied.php');
			exit;
		}
		
		if(isset($_GET['editer'], $_SESSION['m']['id']) AND ctype_digit($_GET['editer']))
		{
			$reponse = mysql_query('SELECT idPseudo, message, timestamp FROM message WHERE id=' . intval($_GET['editer']) . '');
			$donnees = mysql_fetch_assoc($reponse);
			
			if($donnees['idPseudo'] == $_SESSION['m']['id'] OR $_SESSION['m']['acces'] > 92)
			{
				if((time() - $config['forum']['tempsFonctionEditer']) < $donnees['timestamp'] OR $_SESSION['m']['acces'] > 92)
				{
					echo '
					<div id="bloc2">
						<h3 id="form_post">Modifier un message</h3>
						<div class="texte">
							<form method="post">
								<b><font color="#dd0000">* Pseudo :</font></b> <a href="option.htm" style="line-height: 20px">' . $_SESSION['m']['pseudo'] . '</a>
								<br />
								<label for="Emessage">* Message :</label><br /> 
								<textarea name="Emessage" id="Emessage" rows="11" cols="61">' . stripslashes(htmlspecialchars($donnees['message'])) . '</textarea><br />
								<input type="hidden" name="idMessage" value="' . intval($_GET['editer']) . '" />
								<img src="images/apercu.gif" /> <input border="0" type="image" value="submit" src="images/poster.gif" class="inputBouton"/>
							</form>
							<div class="separate"></div>
							<img src="images/puce_base.gif" /> <a href="listeSmileys.htm" onclick="return ouvrirListeSmileys(this)" tabindex="5">Liste des smileys</a> | <a href="charte.htm">Charte des forums</a>
						</div>
						<div class="bloc1bas"></div>
					</div>
					';
					exit;
				}
				else
				{
					avert('
					<b>Vous ne pouvez &eacute;diter un message que ' . $config['forum']['tempsFonctionEditer'] . ' secondes aprÃ¨s votre post.</b><br /><br />
					');
				}
			}
			else
			{
				avert('Vous ne pouvez editer que vos post !');
			}
		}
	// Debut - Editer
	
	// Debut - Modifier titre
		if(isset($_POST['ModifierLeTitre'], $_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92)
		{
			$titre = secure($_POST['ModifierLeTitre']);
			$bdd->sql_query('UPDATE topic SET titre= "' . $titre . '" WHERE id=' . intval($_GET['id']), false, true);
			avert('Le titre a bien &eacute;t&eacute;  modifi&eacute;.');
		}
	// Fin - Modifier titre
	
// Fin - Fonctions (citer, avertir, ...)


// Debut - Ajout d'un message - Poster un message 
	if(isset($_POST['message']))
	{
		// On vérifie qu'il n'a posté 10s avant
		$reponse = mysql_query('
		SELECT MAX(message.timestamp) AS `time`, topic.statut 
		FROM message, topic 
		WHERE topic.id = message.idTopic AND message.statut = 0 AND message.idPseudo= ' . $_SESSION['m']['id']);
		
		$donnees = mysql_fetch_assoc($reponse);
		
		$statut = mysql_result(mysql_query('SELECT topic.statut FROM topic WHERE topic.id = ' . intval($_GET['id'])), 0);
		
		if(isset($_SESSION['m']['id']))
		{
			
			
			if(($statut != 1.0 AND $statut != 2.1 AND isset($statut)) OR $_SESSION['m']['acces'] > 10)
			{	
				if(isset($_POST['retenir']) AND $_SESSION['m']['acces'] > 10) // Cliquer sur "Poster en noir"
				{
					$Sacces = 10;
				}
				elseif(isset($_SESSION['m']['moderateur'][$donnees['idForum']]))
				{
					$Sacces = 50;
				}
				else
				{
					$Sacces = $_SESSION['m']['acces'];
				}
				
				
				 $message = secure(trim($_POST['message']));

				
				if(!empty($message))
				{
					
					if(time() - 10 > $donnees['time']) 
					{
						$requete = mysql_query('SELECT argent FROM membres WHERE id = ' . $_SESSION['m']['id']);
						$shop = mysql_fetch_assoc($requete);
						$argent = $shop['argent'];
						$argentTopic = $argent + 3;
						mysql_query('INSERT INTO message(id,pseudo,message,idTopic,acces,statut,timestamp,timestampModif,auteurModif,idPseudo,ip) VALUES("", "' . $_SESSION['m']['pseudo'] . '", "' . $message . '", "' . intval(intval($_GET['id'])) . '", "' . $Sacces . '", "0", "' . time() . '", "0", "a", "' . $_SESSION['m']['id'] . '", "' . $_SERVER['REMOTE_ADDR'] . '")');
						$messageB = 1;
						
						// On remet à jour le timestamp du dernier Message et le nb de Message dans la table topic
						$reponse = mysql_query('SELECT COUNT(*) AS `nbMessage`, MAX(message.timestamp) AS `lastTimestamp`
						FROM message
						WHERE message.idTopic=' . intval($_GET['id']) . ' AND message.statut < 2
						');
						$donnees = mysql_fetch_assoc($reponse);
						
						mysql_query("UPDATE topic SET lastTimestamp='" . $donnees['lastTimestamp'] . "', nbMessage='" . $donnees['nbMessage'] . "' WHERE id='" . intval($_GET['id']) . "'");
						
						// On donne 3 d'argent au membre
						mysql_query('UPDATE membres SET argent=\'' . secure($argentTopic) . '\' WHERE id=' . $_SESSION['m']['id']);
						$_SESSION['topic_lu' . intval($_GET['id'])] = time() + 9; // Ce topic est lu puisqu'on est dessus
					}
					else
					{
						$ERREUR = 'Vous ne pouvez poster que toutes les 10 secondes. ' . $donnees['time'];
					}
					
				}
				else
				{
					$ERREUR = 'Votre message est vide. ';
				}
			}
			else
			{
				$ERREUR = 'Le topic est lock&eacute;.';
			}
		}
		else
		{
			$ERREUR = ('Vous n\'êtes pas connect&eacute; !');
		}
		
	}
// Fin - Ajout d'un message


// Debut - SystÃ¨me de pagination
	$nombreDeMessagesParPage = $config['forum']['nbMessageParPage'];;

	$retour = mysql_query('SELECT COUNT(*) AS nb_messages FROM message WHERE idtopic=' . $_GET['id'] . ' AND statut < 2');
	$donnees = mysql_fetch_assoc($retour);
	$totalDesMessages = $donnees['nb_messages'];

	$nb_page = ceil($totalDesMessages / $nombreDeMessagesParPage);
	$nbPagesPossibles = ceil($totalDesMessages / $nombreDeMessagesParPage);	$nb_page = ceil($totalDesMessages / $nombreDeMessagesParPage);
	$nbPagesPossibles = ceil($totalDesMessages / $nombreDeMessagesParPage);
	
	// On regarde Ã  quel page on se trouve :
	if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
	{
		$page = intval($_GET['page']);
	}
	else
	{
		$page = 1;
	}
	// Reste plus qu'Ã  faire un page(lien, page actuel, nombre de page en tout);
	
// Fin - SystÃ¨me de Pagination


// Debut - Déclaration bloc principaux
	$reponse = mysql_query('SELECT topic.idForum, topic.titre AS `topicTitre`, topic.statut, forum.titre AS `forumTitre` FROM topic LEFT JOIN forum ON topic.idForum = forum.id WHERE topic.id=\'' . intval($_GET['id']) . '\'');
	$donnees = mysql_fetch_assoc($reponse);
	$idForum = $donnees['idForum'];
	$titreForum = $donnees['forumTitre'];
	$titreSujet = $donnees['topicTitre'];
	$statut = $donnees['statut'];
// Fin - Déclaration bloc principaux

	$RETOUR = '<br /><br />';
	

	if($repondre)
	{
		$RETOUR = '<center><a href="' . have_url(intval($_GET['id']), 1, TOPIC_URL, $titreForum) . '"><img src="images/retour.gif" alt="" /></a></center>';
	}

	if(isset($_SESSION['m']['id']) AND $_SESSION['m']['acces'] > 92)
	{
		$titreSujetAffiche = stripslashes('<form method="post"><input type="text" style="width:50%;text-align:center" name="ModifierLeTitre" id="ModifierLeTitre" value="'.$titreSujet.'" />
		<input type="submit" value="Ok" /></form>');
	}
	else
	{
		$titreSujetAffiche = '« ' . bbCode(stripslashes(htmlspecialchars(nl2br($titreSujet)))) . ' »';
	}


// Debut - Affichage Bloc 1 situé en haut
	echo '
	<div class="bloc2">
		<h3>Forum : <font color="white">' . htmlspecialchars(stripslashes($titreForum)) . '</font></h3>
		<div class="texte">
			<br />
			<center class="titreSujet"><font color="#ab9999">Sujet :</font> ' . $titreSujetAffiche . '</center>
			<br />
			<span style="float: right">
			
				<a href="' . have_url($idForum , 1 , FORUM_URL, $titreForum) . '"><img src="images/liste_des_sujets.png" /></a>
			</span>
			
			<span style="float: left;">
			<a href="forum-' . $idForum . '.htm#form_post"><img src="images/nouveau_sujet.png" /></a>
			</span>
			' . $RETOUR . '
			';
			
			
			echo'<div class="separate"></div>

			<div class="pagination">
			';
			
			
			echo '
			' . page('topic-' . intval($_GET['id']), $page, $nbPagesPossibles). ' 
			</div>
			<br />
			';
// Debut - Fonction déplacer un topic (pour admin)
				if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] > 92)
				{
					echo '
					<form method="post">
						<center>
							<select name="deplacerTopic">
							';
							
							$reponse = $bdd->sql_query('SELECT id, titre FROM forum WHERE statut=0 ORDER BY titre ASC');
							
							while($donnees = $bdd->sql_fetch($reponse))
							{
								echo '<option value="' . $donnees['id'] . '">' . $donnees['titre'] . '</option>';
							}
				
							echo '
							</select><input type="submit" value="D&eacute;placer le topic" />
						</center><br />
					</form>
					';
									  
				}

			// Fin - Fonction déplacer un topic (pour admin)
			echo '
		</div>
		<div class="bloc1bas"></div>
	</div>
	<br />
	<div class="espaceModeration">
	';
		
			// Debut - Acces aux outils de modération
			if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
			{
			
				switch($statut)
				{
					case 1.0:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=11"><img src="images/debloquer.gif"></a> 
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=21"><img src="images/marquer.gif"></a>
					';
					break;
					
					case 1.1:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=10"><img src="images/bloquer.gif"></a>
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=20"><img src="images/marquer.gif"></a>
					';
					break;
					
					case 2.0:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=21"><img src="images/bloquer.gif"></a> 
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=11"><img src="images/demarquer.gif"></a>
					';
					break;
					
					case 2.1:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=20"><img src="images/debloquer.gif"></a> 
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=10"><img src="images/demarquer.gif"></a>
					';
					break;
				}
			}
		// Fin - Acces aux outils de modération
		
		echo '<span style="float: right">';
		
		if($statut != '1.0' && $statut != '2.1')
		{
			if(!$repondre)
			{	
				echo '<a href="topic-' . intval($_GET['id']). '-0.htm#form_post"><img src="images/repondre.png" /></a>';
			}
		}
		
		
		
		echo '<a href="javascript:location.reload();"><img src="images/rafraichir.png"></a>
			</span>';
		
		
		echo'
	</div>
	<br />
	';
// Fin - Affichage Bloc 1 situé en haut

// Debut - Listage de Messages
	$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;
	
	
	$LIMIT = 'LIMIT ' . $premierMessageAafficher . ', ' . $nombreDeMessagesParPage. ' ';
	
	if($repondre)
	{
		$dr = ($totalDesMessages - 10);
		if($dr < 0) { $dr = 0; }
		$LIMIT = 'LIMIT ' .  $dr . ', 10';
	}
	
	$reponse = mysql_query('
	SELECT message.id, message.pseudo, message.message,membres.avatar, message.idTopic, message.acces, message.statut, message.timestamp, message.timestampModif, message.auteurModif,membres.sexe, message.idPseudo, message.ip, membres.signature, membres.timestamp AS `timeM`, membres.acces, membres.avatar 
	FROM message, membres 
	WHERE idTopic=' . intval($_GET['id']) . ' AND statut < 2 AND membres.id = message.idPseudo
	ORDER BY id ASC ' . $LIMIT);
	$i = 0;
?>
<script language="JavaScript">

					var nombre_de_case = 1;
					</script>
<?php
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
				$bann = ' <a title="Bannir ce membre" href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;bannirMembre=' . $donnees['idPseudo'] . '&amp;idMessage=' . $donnees['id'] . '" onclick="reponse = confirm(\'Etes-vous sur de vouloir bannir ce membre ?\'); return reponse;"><img src="message/bann.gif"></a>';
                $bannIp = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;bannIp=' . $donnees['ip'] . '&amp;bannId=' . $donnees['idPseudo'] . '" onclick="reponse = confirm(\'Etes-vous sur de vouloir bannir ip ce membre ?\'); return reponse;"><img src="images/bannIp.png" alt="Bannir Ip" /></a>';
			}
			else
			{
				$bann = '';
				$bannIp = '';
			}
		// Fin - Fonction que Admin
		
		// Debut - Fonction que modo ou admin
			if($donnees['statut'] != 2) // Message non-supprimé
			{
				if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] >= 50) // Modérateur ou +
				{	
					$supprimer_message = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;supprimerMessage=' . $donnees['id'] . '&amp;statut=' . $donnees['statut'] . '" title="Supprimer ce message" onclick="reponse = confirm(\'Etes-vous sur de vouloir supprimer ce message ?\'); return reponse;"><img src="message/supprimer.gif" alt="Supprimer ce message" width="11" height="12"></a> ';
					$kick = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;kicker=' . $donnees['idPseudo'] . '" title="Kicker ce membre" onclick="reponse = confirm(\'Etes-vous sur de vouloir kicker ce membre ?\'); return reponse;"><img src="images/kick.gif"></a>';
					$armev5 = '<input type="checkbox" name="supprimer[]" value="' . $donnees['id'] . '" style="margin: 0px" />';
					
					if($_SESSION['m']['acces'] >= 50) // Admin
					{
						$Sip = '<a href="recherche.php?recherche=' . $donnees['ip'] . '&listRecherche=IP">' . $donnees['ip'] . '</a>'; 
					}
				}
			}
			elseif($donnees['statut'] == 2) // Message supprimé, visible que par les admins
			{
				if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] > 92)
				{
					$supprimer_message = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;voirMessage=' . $donnees['id'] . '&amp;statut=' . $donnees['statut'] . '" title="Supprimer ce message" onclick="reponse = confirm(\'Etes-vous sur de vouloir remettre ce message ?\'); return reponse;"><img src="message/voir.gif" alt="Remettre ce message" width="11" height="12"></a> ';
					$Sip = '<b>Ip : </b><a href="recherche.php?recherche=' . $donnees['ip'] . '&listRecherche=IP">' . $donnees['ip'] . '</a>'; 
					$kick = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;kicker=' . $donnees['idPseudo'] . '" title="Kicker ce membre" onclick="reponse = confirm(\'Etes-vous sur de vouloir kicker ce membre ?\'); return reponse;"><img src="images/kick.gif"></a>';
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
			$requete = mysql_query('SELECT couleur FROM membres WHERE id = ' . $donnees['idPseudo']);
			$info = mysql_fetch_assoc($requete);
			
			if($info['couleur'] == $info['couleur']) // On affiche la couleur que le membre à activer dans le shop
			{
				$pseudo = ' <strong><font color="' . $info['couleur'] . '">' . $donnees['pseudo'] . '</font></strong>';
			}
			else // Membre
			{
				$pseudo = ' <strong>' . ($COLP->afficher(10, $donnees['pseudo'])) . '</strong>';
			}
		// Fin - Couleur du Pseudo
		
		// Debut - Fonction simple pour les membres
			$requete = mysql_query('SELECT police FROM membres WHERE id = ' . $donnees['idPseudo']);
			$info = mysql_fetch_assoc($requete);
			
			if(isset($_SESSION['ignore'][$donnees['idPseudo']]) AND $_SESSION['ignore'][$donnees['idPseudo']] == 1)
			{
				$imgI = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;designorerMembre=' . $donnees['idPseudo'] . '" title="Voir le membre"><img src="images/message/voir.png" alt="Voir le membre" /></a>';
				$message = '<i>Vous avez décidé d\'ignorer ce membre.</i><br />';
			}
			elseif($info['police'] == $info['police'])
			{
				$imgI = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;ignorerMembre=' . $donnees['idPseudo'] . '" title="Ignorer ce membre"><img src="images/message/ignorer.png" alt="Ignorer le membre" /></a>';
				$message = '<span style="font-family: ' . $info['police'] . '">' . nl2br(bbCode(htmlspecialchars(stripslashes($donnees['message'])))) . '</span>';
			}
			else
			{
				$imgI = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;ignorerMembre=' . $donnees['idPseudo'] . '" title="Ignorer ce membre"><img src="images/message/ignorer.png" alt="Ignorer le membre" /></a>';
				$message = nl2br(bbCode(htmlspecialchars(stripslashes($donnees['message']))));
			}
			
			$avertir = '<a target="avertir" rel="nofollow" href="avertir.php?idMessage=' . $donnees['id'] . '&amp;idTopic=' . $donnees['idTopic'] . '&amp;page=' . $page . '" onclick="window.open(\'\',\'avertir\',\'width=580,height=330,scrollbars=no,status=no\')"><img src="images/message/avertir.png" title="Avertir Administrateur"></a>';
			
			// Citer
			if(isset($_SESSION['m']['id']))

			{
				$citer = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;citer=' . $donnees['id'] . '#form_post"><img src="images/message/citer.png" title="Citer un message"></a>';
			}
			else
			{
				$citer = '';
			}
			
			// Bouton éditer qui peut apparaitre que 3min aprÃ¨s le post (sauf admin)
			if(((time() - 3 * 60) < $donnees['timestamp'] AND isset($_SESSION['m']['id']) AND $_SESSION['m']['id'] == $donnees['idPseudo']) OR $_SESSION['m']['acces'] > 92)
			{
				$editer = '<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;editer=' . $donnees['id'] . '"><img src="images/message/editer.png" title="Editer son messages"></a>';
			}
			else
			{
				$editer = '';
			}
		// Fin - Fonctions simple pour un membre
		
		// Debut - Affichage date du message de modification
			if($donnees['timestampModif'] > 1000)
			{
				$modif = '<div class="modification">DerniÃ¨re modification le ' . date('d/m/Y\ \Ã \ H\:i\:s', $donnees['timestampModif']) . ' par ' . $donnees['auteurModif'] . '</div>';
			}
			else
			{
				$modif = '';
			}
		// Fin - Affichage date du message de modification
		
		if(!empty($donnees['signature']))
		{
			$signature = '<br /><br /><hr /><font size="0.95em">' . htmlspecialchars(stripslashes($donnees['signature'])) . '</font>';
		}
		else
		{
			$signature = '';
		}

		
		$aysql = mysql_query('SELECT COUNT(*) FROM message WHERE idPseudo = ' . $donnees['idPseudo']);
		$posts = mysql_result($aysql, 0);

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
			
			
			$avatar = htmlspecialchars(stripslashes($donnees['avatar']));
			
			if(isset($donnees['avatar']) AND !preg_match("#^( *)$#", $donnees['avatar']))
			{	
				$avatar = '<img src="' . htmlspecialchars(stripslashes($donnees['avatar'])) . '" alt="" id="avatar" height="100" width="100" />';
			}
			else
			{
				$avatar = '<img src="http://image.jeuxvideo.com/avatars/default.jpg" height="100" width="100"/>';
			}
			$affichAva = $avatar;

			$ss = '';
			if($posts > 1)
			{
				$ss = 's';
			}
			
			$affichRang = rang($posts,10);
			$affichPost = '' . $posts . ' message' . $ss . '';
			
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
			
		// Debut - Affichage du message
			echo '
	<div class="message" id="message_' . $donnees['id'] . '">
		<div class="table">
			<div class="tr">
				<div class="msg2">
					<div class="td gauche">
						<div class="pseudo">
 <a href="cdv-' . $donnees['pseudo'] . '.html" title="Voir le profil" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/message/profil.png"></a>' . $pseudo  . '
						</div>
						<div class="img">
							' . $affichAva . '
						</div>
						<div class="nbPost">
							' . $grade . '
							<br />
							' . $imgI . ' ' . $citer . ' ' . $editer . ' <a href="mp.php?action=ecrire&amp;pseudo=' . $donnees['idPseudo'] . '"><img src="images/message/mp.png" title="Envoyer un MP à ce membre"></a>  <br />
							' . $affichPost . '<br />
							' . $Sip . '
						</div>
					</div>
					<div class="td droite">
						<div class="date">
						
						</div>
						<div class="fonctions">
						<b>' . $armev5 . ' ' . $supprimer_message  . ' ';
						
						$timestamp = $donnees['timestamp'];
						
						// Tableau des mois en français
						$mois_fr = array('', 'janvier', 'f&eacute;vrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'd&eacute;cembre');
						
						// Tableau de la date
						// $jour_modif = array('', '1er', 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
						
						// On extrait la date du jour
						list($jour, $mois, $annee) = explode('/', date('d/n/Y', $timestamp));
							
						echo '
						' . $kick . '' .  $bann . ' ' .  $bannIp . '</b> <span style="float: right;">';echo $jour . ' ' . $mois_fr[$mois] . ' ' . $annee; 
						echo ' &agrave; ';
						echo date('H\:i\:s', $timestamp);  echo'  ' . $avertir . '</span>
						<div class="post" width="480">
                      	' . $message . '
						' . bbCode($signature) . '
						' . $modif . '
						<div class="lienPermanent">
						<a href="topic-' . intval($_GET['id']) . '-' . $page . '.html#message_' . $donnees['id'] . '">#. ' . $donnees['id'] . '</a>
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
// Fin - Listage de Messages


// Debut - Affichage bloc 1 du bas
	echo '
	<div class="espaceModeration">
	';
		echo '<span style="float: right">';
		
		if($statut != '1.0' && $statut != '2.1')
		{
			if(!$repondre)
			{	
				echo '<a href="topic-' . intval($_GET['id']). '-0.htm#form_post"><img src="images/repondre.png" /></a>';
			}
		}
		
		echo '<a href="javascript:location.reload();"><img src="images/rafraichir.png"></a>
			</span>';
		
		
		
		// Debut - Acces aux outils de modération
			if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
			{
				switch($statut)
				{
					case 1.0:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=11"><img src="images/debloquer.gif"></a> 
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=21"><img src="images/marquer.gif"></a>
					';
					break;
					
					case 1.1:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=10"><img src="images/bloquer.gif"></a>
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=20"><img src="images/marquer.gif"></a>
					';
					break;
					
					case 2.0:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=21"><img src="images/bloquer.gif"></a> 
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=11"><img src="images/demarquer.gif"></a>
					';
					break;
					
					case 2.1:
					echo '
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=20"><img src="images/debloquer.gif"></a> 
					<a href="topic.php?id=' . intval($_GET['id']) . '&amp;page=' . $page . '&amp;changeStatut=10"><img src="images/demarquer.gif"></a>
					';
					break;
				}
			}
		// Fin - Acces aux outils de modération

		echo'
	</div>
	<br />
	<div class="bloc2">
		<div class="texte">
			<br />
			<center class="titreSujet"><font color="#ab9999">Sujet :</font> « ' . bbCode(stripslashes(htmlspecialchars(nl2br($titreSujet)))) . ' »</center>
			<br />';
			
			echo'
			<span style="float: right">
			
				<a href="' . have_url($idForum , 1 , FORUM_URL, $titreForum) . '"><img src="images/liste_des_sujets.png" /></a>
			</span>';
		
			echo'<span style="float: left;">
			<a href="forum-' . $idForum . '.htm#form_post"><img src="images/nouveau_sujet.png" /></a>
			</span>';
			
			
			
			if($repondre)
			{
				echo  '<center><a href="' . have_url(intval($_GET['id']), 1, TOPIC_URL, $titreForum) . '"><img src="images/retour.gif" alt="" /></a></center>';
			}
			echo'
			<div class="separate"></div><div class="pagination">
			';
			
						echo '
			' . page('topic-' . intval($_GET['id']), $page, $nbPagesPossibles) . '
			<br />
			</div>
		</div>
		<div class="bloc1bas"></div>
	</div>
	';
// Fin - Affichage bloc 1 du bas
// Debut - Autorisation acces au forumulaire
	if(!isset($_SESSION['m']['pseudo'])) // On est pas connecté
	{
		include('pied.php');
		exit;
	}

	// Si on est kicker
	$reponse = mysql_query('SELECT * FROM kick WHERE idPseudo=' . $_SESSION['m']['id']) OR die(mysql_error());
	$result = mysql_num_rows($reponse);

	if((int) $result != 0) // ( pour php, 0 = false, donc il faut préciser qu'on attend un chiffre )
	{
		$reponse = mysql_unbuffered_query('SELECT idForum FROM kick WHERE idPseudo=' . $_SESSION['m']['id'] . '');
		$donnees = mysql_fetch_assoc($reponse);
		
		if($donnees['idForum'] == $idForum)
		{
			echo '<br /><b><center>Vous avez &eacute;t&eacute; kicker de ce forum pour une dur&eacute;e de 72h.</center></b><br />';
			include('pied.php');
			exit;
		}
	}

	// Si le topic est verrouiller et qu'on est pas admin ou modo
	if(($statut == 1.0 OR $statut == 2.1) AND $_SESSION['m']['acces'] == 10)
	{
		include('pied.php');
		exit;
	}
// Fin - Autorisation acces au forumulaire
$reponse = mysql_query('SELECT avatar FROM membres WHERE id=' . $_SESSION['m']['id'] . '');
$donnees = mysql_fetch_assoc($reponse);

if($repondre)
{
echo'
<br />
<div class="bloc2">
<h3>Répondre au sujet de discussion</h3>
	<div class="texte" style="padding: 4px">
		<form method="post" name="form_post" id="form_post" action="topic-' . intval($_GET['id']) . '-' . $nbPagesPossibles . '.html">
			<a href="option.html" style=" float: right; padding-right: 5px; padding-right: 5px;">' . $_SESSION['m']['pseudo'] . '</a> <br />
					';
					$requete = mysql_query('SELECT COUNT(*) AS nbMessages FROM message WHERE idPseudo=\'' . $_SESSION['m']['id'] . '\'');
				$infos = mysql_fetch_assoc($requete);
				$messages = stripslashes(number_format($infos['nbMessages'], 0, '', '.'));
				
				if(empty($donnees['avatar']) OR !isset($donnees['avatar']))
				{
					$donnees['avatar'] = 'http://image.jeuxvideo.com/avatars/default.jpg';
				}
				
				
				if(isset($donnees['avatar']))
				{
					echo '<span style=" float: right; padding-right: 5px; padding-right: 5px;"><a href="' . htmlspecialchars(stripslashes($donnees['avatar'])) . '"><img src="' . htmlspecialchars(stripslashes($donnees['avatar'])) . '" alt="" id="avatar" height="70" width="70" /></a><br /><b></b></span>';
				}
				else
				{
				echo'
				<span style=" float: right; padding-right: 5px; padding-right: 5px;"><img src="../images/noavatar.png" alt="" id="avatar" height="70" width="70"/><br /><b></b></span>
				';
				}
				
				
			
				if (isset($Cmessage, $Cpseudo)) 
			{ 
				$messagea = '[b]Citation de ' . $Cpseudo . '[/b] : [citer]' . $Cmessage . '[/citer]'; 
			} 
			elseif(isset($_POST['message']) AND !isset($messageB))
			{
					$messagea = stripslashes($_POST['message']);
			}
			else
			{
				$messagea = '';
			}
			?>
			<br />
			<b style="color: #dd0000;">* Message :</b><br /> 
				
					<input type="hidden" name="pseudo" value="<?php echo $_SESSION['m']['pseudo']; ?>" />
					<?php echo'<img src="message/souligne.png" alt="souligner" onclick="storeCaret(\'u\')"/>  <img src="message/italique.png" alt="italique" onclick="storeCaret(\'i\')"/>  <img src="message/gras.png" alt="gras" onclick="storeCaret(\'b\')"/> <img src="message/barre.png" alt="barrer" onclick="storeCaret(\'s\')"/> </a> '; ?><br />
					<textarea name="message" id="message" rows="11"  style="width:99%;"><?php echo $messagea; ?></textarea><br />
				
					<img style="vertical-align:top;" href="topic.php" onclick="return apercu();" src="images/apercu.png" alt="Aperçu" border="0" type="image" class="inputBouton"/></a> <input style="vertical-align:top;" border="0" type="image" value="submit" src="images/poster.png" class="inputBouton"/>
				</form>
			<form action="apercu.html" method="post" name="apercuMessage"  style="display: inline;">
		 <input type="hidden" name="pseudo" />
 
		 <input type="hidden" name="message" /> 
		</form>
				<div class="separate"></div>
				<img src="images/puce_base.gif" /><a target="popup" onclick="window.open('','popup','width=800,height=820,scrollbars=yes,status=no')" href="listeSmileys.php">Liste des smileys</a> | <a href="charte.htm">Charte des forums</a>
			</div>
			<div class="bloc1bas"></div>
		</div><br /><br />

<?php

}

// Si on est admin ou modo on peut gérer les kicks
if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] >= 50)
{
	$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS nb FROM kick WHERE idForum=' . $idForum . '');
	$donnees = mysql_fetch_assoc($reponse);
	$nbKick = $donnees['nb'];
	
	if($nbKick == 0)
	{
		echo '<br /><b><center>Aucun membres n\'a &eacute;t&eacute; kick&eacute;.</center></b>';
	}
	else
	{
		echo '
		<br /><div class="bloc2"><h3><center>Liste des Kicks en cours du Forum</center></h3>
		<div class="texte">
		<div class="listageKick" >
			<table>
				<tr>
					<th style="width: 10px;"></th>
					<th style="width: 305px;">Pseudo</th>
					<th style="width: 202px;">Dur&eacute;e restante</th>
				</tr>
				';
				
				$reponse = mysql_unbuffered_query('SELECT kick.id, kick.idPseudo, membres.pseudo, kick.timestamp FROM kick INNER JOIN membres ON membres.id = kick.idPseudo WHERE idForum=' . $idForum . ' ORDER BY timestamp ASC');
				
				while($donnees = mysql_fetch_assoc($reponse))
				{
					$tempsExpiration = $donnees['timestamp'] + (60 * 60 * 72);
					$tempsRestant = $tempsExpiration - time();
					$tempsRestantHeures = ceil($tempsRestant / 60 / 60);
					
					echo '
					<tr>
						<td><a href="topic.php?id=' . intval($_GET['id']) . '&amp;dekick=' . $donnees['idPseudo'] . '"><img src="message/supprimer.gif"></a></td>
						<td>' . $donnees['pseudo'] . '</td>
						<td>' . $tempsRestantHeures . 'h</td>
					</tr>
					';
				}
				echo '
			</table>
		</div></div></div>
		';
	}
}

if(isset($_SESSION['m']['moderateur'][$idForum]) OR $_SESSION['m']['acces'] == 50)
{
	$_SESSION['m']['acces'] = 10;
}

include('pied.php');
?>