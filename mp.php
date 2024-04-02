<?php
include('base.php');
include('tete.php');
 
// Destination des images
$repertoire = 'images/mp/';

// Si la session est démarrée et si les variables $_GET['mp'] et$_GET['action'] n'existent pas, alors on affiche la page
if(isset($_SESSION['m']['id']) AND !isset($_GET['mp']) AND !isset($_GET['action']))
{
	// On compte le nombre de messages non lus et dont le destinataire est le membre actuellement connecté
	$nbr_non_vus = mysql_query("SELECT COUNT(*) AS nbre FROM mp WHERE destinataire='".$_SESSION['m']['pseudo']."' AND vu='0' AND (efface='0' OR efface='2')")or die(mysql_error());

	// On en fait un array
	$nbre_non_vus = mysql_fetch_assoc($nbr_non_vus);

	// On récupère les données sur les messages adressés au membre connecté.
	$retour = mysql_query("SELECT id, sujet, expediteur, timestamp, vu FROM mp WHERE destinataire='".$_SESSION['m']['pseudo']."' AND (efface='0' OR efface='2') ORDER BY id DESC");
	echo '
	<div class="blocacc">
		<div class="texte">
			<br />
			<span style="float: left;"><img src="' . $repertoire . 'enveloppe.png"/></span><center class="titreSujet">« Boîte de Réception »</center>
			<br /><br />
			<div class="separate"></div>
			<center><a href="mp.html?action=envoyer"><img src="' . $repertoire . 'envoyé.png"/></a> <a href="mp.html"><img src="' . $repertoire . 'messagerie.png"/></a> <a href="mp.html?action=ecrire"><img src="' . $repertoire . 'nouveau.png"/></a></center>
			<br />
		</div>
	</div>
<br />
	<div class="listageTopic"><table>
		<tr>
			<th width="40px">Lu ?</th>
			<th class="col2">Sujet</th>
			<th class="col3">Auteur</th>
			<th class="col5">Date</th>
			<th></th>
			<th></th>
		</tr>
		';
		
		// On crée une boucle
		while($donnees = mysql_fetch_assoc($retour))
		{
			// on enlève les slashs inutiles qui se seraient ajoutés
			$sujet = stripslashes($donnees['sujet']);
			$expediteur = stripslashes($donnees['expediteur']);
			$date = $donnees['timestamp'];
			
			if($i % 2 == 0)
			{
				$bloc = 'tr1';
			}	
			else
			{
				$bloc = 'tr2';	
			}
			 
			if($donnees['vu'] == 0) // si le message n'est pas lu, on le montre et on marque son sujet en gras
			{
				// on crée une ligne sur le tableau
				echo'<tr class="' . $bloc . '"><td class="td"><strong>Non lu</strong></td><td><strong><a href="mp.html?action=lire&amp;mp='.$donnees['id'].'">'.$sujet.'</a></strong></td><td class="td"><center>'.$expediteur.'</center></td><td class="td">' .date('d/m/Y H\hi', $date).'</td><td class="td"><a href="mp.html?action=supprimer&amp;suppr=1&amp;id='.$donnees['id'].'"><img src="images/moderation/supprimer.gif"></a></td></tr>';
			}
			else // sinon, on marque que le sujet a été lu, et on met en italique
			{
				// on crée une nouvelle ligne sur le tableau
				echo '<tr class="' . $bloc . '"><td class="td"><em>Lu</em></td><td><em><a href="mp.html?action=lire&amp;mp='.$donnees['id'].'">'.$sujet.'</a></em></td><td class="td"><center>'.$expediteur.'</center></td><td class="td">' .date('d/m/Y H\hi', $date).'</td><td class="td"><a href="mp.html?action=supprimer&amp;suppr=1&amp;id='.$donnees['id'].'"><img src="images/moderation/supprimer.gif"></a></td></tr>';
			}
			$i++;
		}

		echo '
	</table></div>
	';
}

elseif(isset($_GET['mp']) AND isset($_GET['action']) AND $_GET['action'] == 'lire' AND isset($_SESSION['m']['id']))
{
	$id_mp = $_GET['mp'];
	
	// On récupère les données où l'id est égale à l'id envoyée par l'URL
	$retour = mysql_query("SELECT destinataire, sujet, expediteur, timestamp, message FROM mp WHERE id='".$id_mp."'")or die(mysql_error());
	$donnees = mysql_fetch_assoc($retour);
	
	// Vérification pour qu'une autre personne que le destinataire ne puisse voir le message
	if($donnees['destinataire'] == $_SESSION['m']['pseudo_mp'] OR $donnees['expediteur'] == $_SESSION['m']['pseudo_mp'])
	{
		// On enlève les slashs inutiles
			$sujet = stripslashes($donnees['sujet']);
			$expediteur = stripslashes($donnees['expediteur']);
			
			// On met la date au format Jour/mois/année à heure h minutes
			$date = date('d/m/Y \à H\hi', $donnees['timestamp']);
			$mp = stripslashes($donnees['message']); 
			
		echo '
		<table class="table">
			<thead>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
			
			';
			
			// On affiche le MP       
			echo '
	<div class="blocacc">
		<div class="texte">
			<br />
			<span style="float: left;"><img src="' . $repertoire . 'enveloppe.png"/></span><center class="titreSujet"><font color="#ab9999">Sujet :</font> « ' . $sujet . ' »</center>
			<br /><br />
<div class="separate"></div>
			<center><a href="mp.html?action=envoyer"><img src="' . $repertoire . 'envoyé.png"/></a> <a href="mp.html"><img src="' . $repertoire . 'messagerie.png"/></a> <a href="mp.html?action=ecrire"><img src="' . $repertoire . 'nouveau.png"/></a></center>
			<br />

		</div>
	</div>
<br />
<span style="margin-left: 12px"><a href="mp.html"><img src="' . $repertoire . 'retour.png" /></a></span>
<span style="float: right; padding-right: 12px">
				<a href="mp.html?action=ecrire&reponse='.$id_mp.'"><img src="' . $repertoire . 'repondre.png" /></a>
</span>
<br /><br />
				<div class="message" id="message_' . $lire['id'] . '">
					<div class="table">
						<div class="tr">
							<div class="msg2">
								<div class="td gauche">
									<br /><b><center>De ' . $expediteur . '</center></b>
								</div>
								<div class="td droite">
									<div class="fonctions">
										<b>Envoyer le ' . $date . '</b>
										<div class="post">
										' . nl2br(bbCode($mp)) . '
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><br />
			';
			// On met que le message a été lu.
			$nbr_desja_vu = mysql_query("SELECT COUNT(*) AS nbre FROM mp WHERE id='".$id_mp."' AND vu='0' ")or die(mysql_error());
			mysql_query("UPDATE mp SET vu='1' WHERE id='".$id_mp."'")or die(mysql_error());
			if ($nbr_desja_vu > 0)
			{
			$_SESSION['m']['messagereoupas'] = 0;
			}
			
			echo '
			</tbody>
		</table>
		';
	}
	else
	{
		// On affiche un message d'erreur si on essaye de lire un message qui n'est pas adressé à soi-même.
		echo 'Ceci est un message privé qui ne s\'adresse pas à vous mais à '.$donnees['destinataire'].'';
	}
}

// Sinon si l'URL indique qu'on veut envoyer un nouveau message ('ecrire'), on affiche un formulaire d'envoi.
elseif(isset($_GET['action']) AND $_GET['action'] == 'ecrire' AND isset($_SESSION['m']['id']))
{
	if(!isset($_GET['reponse'])) // Si la variable $_GET['reponse'] n'existe pas, alors c'est un nouveau message
	{
		echo '
	<div class="blocacc">
		<div class="texte">
			<br />
			<span style="float: left;"><img src="' . $repertoire . 'enveloppe.png"/></span><center class="titreSujet">« Ecrire un message »</center>
			<br /><br />
<div class="separate"></div>
			<center><a href="mp.html?action=envoyer"><img src="' . $repertoire . 'envoyé.png"/></a> <a href="mp.html"><img src="' . $repertoire . 'messagerie.png"/></a> <a href="mp.html?action=ecrire"><img src="' . $repertoire . 'nouveau.png"/></a></center>
			<br />

		</div>
	</div>
<br />
<span style="margin-left: 12px"><a href="mp.html"><img src="' . $repertoire . 'retour.png" /></a></span><br /><br />
		<div class="bloc1">
			<h3>Envoyer un message privé</h3>
			<div class="texte">
			<form action="mp.html?action=traitement" method="post">
				<label><b>Sujet :</b></label> <input type="text" name="sujet" value="'.$_POST['sujet'].'"/><br />
				<label><b>Destinataire :</b></label>
				';
				
				$reponse2 = mysql_query('SELECT pseudo, id FROM membres ORDER BY pseudo ASC');
				
				echo '
				<select name="destinataire">
				';
				while($donnees2 = mysql_fetch_assoc($reponse2))
				{
					if(isset($_GET['pseudo']) AND $_GET['pseudo'] == $donnees2['id'])
					{
						echo '<option value="' . $donnees2['pseudo'] . '" selected="selected">' . $donnees2['pseudo'] . '</option>';
					}
					else
					{
						echo '<option value="' . $donnees2['pseudo'] . '">' . $donnees2['pseudo'] . '</option>';
					}
				}
				echo '
				</select><br />
				
				<b><label>Message :</label></b> <textarea name="message" rows="10" cols="40">'.$_POST['message'].'</textarea><br />
				<label></label><input type="submit" value="Envoyer le message" />
			</form>
			</div>
			<div class="bloc1bas"></div>
		</div>
		';
	}
	else // Sinon, c'est une réponse
	{
		// On récupère les données du mp dont l'id est égale à celui auquel on veut répondre
		$retour_reponse = mysql_query("SELECT sujet, expediteur FROM mp WHERE id='".$_GET['reponse']."'");
		$donnees_reponse = mysql_fetch_assoc($retour_reponse);
		$expediteur = $donnees_reponse['expediteur'];
		
		echo '
	<div class="blocacc">
		<div class="texte">
			<br />
			<span style="float: left;"><img src="' . $repertoire . 'enveloppe.png"/></span><center class="titreSujet">« R&eacute;pondre &agrave; un membre »</center>
			<br /><br />
<div class="separate"></div>
			<center><a href="mp.html?action=envoyer"><img src="' . $repertoire . 'envoyé.png"/></a> <a href="mp.html"><img src="' . $repertoire . 'messagerie.png"/></a> <a href="mp.html?action=ecrire"><img src="' . $repertoire . 'nouveau.png"/></a></center>
			<br />

		</div>
	</div>
<br />
		<div class="bloc1">
		<h3>Répondre à ce membre</h3>
		<div class="texte">
		<form action="mp.html?action=traitement" method="post">
			<b>Sujet :</b> <input type="text" name="sujet" value="RE : ' . $donnees_reponse['sujet'] . '" /><br />
			<b>Destinataire :</b>
			';
			
			$reponse2 = mysql_query('SELECT pseudo FROM membres ORDER BY pseudo ASC');
			
			echo '
			<select name="destinataire">
			';
			while($donnees2 = mysql_fetch_assoc($reponse2))
			{
				if($expediteur == $donnees2['pseudo'])
				{
					echo '<option value="' . $donnees2['pseudo'] . '" selected="selected">' . $donnees2['pseudo'] . '</option>';
				}
				else
				{
					echo '<option value="' . $donnees2['pseudo'] . '">' . $donnees2['pseudo'] . '</option>';
				}
			}
			echo '
			</select><br />
			
			<b>Message :</b><br /> <textarea name="message" rows="10" cols="40"></textarea><br />
			<input type="submit" value="Envoyer le message" />
		</form>
		</div>
		<div class="bloc1bas"></div>
		</div>
		';
	}
}

// Sinon si la variable $_GET['action'] est égale à 'traitement', alors on traite les données envoyées par le fomulaire
elseif(isset($_GET['action']) AND $_GET['action'] == 'traitement' AND isset($_SESSION['m']['id']))
{
	// Si les champs "message", "sujet" et "destinataire" ne sont pas vides       
	if(!empty($_POST['sujet']) AND !empty($_POST['destinataire']) AND !empty($_POST['message']))
	{
		// On regarde s'il existe une entrée avec le pseudo du destinataire
		$nbr_entree = mysql_query("SELECT COUNT(*) AS nbre_entrees FROM membres WHERE pseudo='".$_POST['destinataire']."'")or die(mysql_error());
		$nbr_entrees = mysql_fetch_assoc($nbr_entree);
		
		// S'il existe
		if($nbr_entrees['nbre_entrees'] == 1)
		{
			// On sécurise les valeurs envoyées
            $sujet = mysql_real_escape_string(htmlspecialchars($_POST['sujet']));
			$destinataire = mysql_real_escape_string(htmlspecialchars($_POST['destinataire'])); // On utilise mysql_real_escape_string et htmlspecialchars par mesure de sécurité
            $message = mysql_real_escape_string(htmlspecialchars($_POST['message'])); // De même pour le message
            $expediteur = $_SESSION['m']['pseudo'];
            $timestamp = time();
			
			// On récupère le dernier message envoyé au destinataire
			$retour = mysql_query("SELECT destinataire, sujet, message FROM mp WHERE expediteur='$expediteur' ORDER BY id DESC LIMIT 0,1");
			$donnees = mysql_fetch_assoc($retour);
			
			// Si c'est le même que celui qu'on veut envoyer
			if($donnees['destinataire'] == $destinataire AND $donnees['sujet'] == $sujet AND $donnees['message'] == $message)
			{
				// On ne l'enregistre pas, et on affiche un message d'erreur
				avert('Vous ne pouvez pas poster le même message 2 fois d\'affilée');
			}
			else // Sinon ce n'est pas un double post
			{
				// Alors on enregistre dans la base de données
				mysql_query("INSERT INTO mp(sujet, expediteur, destinataire, message, timestamp, vu, efface) VALUES('" . $sujet . "', '" . $expediteur . "', '" . $destinataire . "', '" . $message . "', '" . $timestamp . "', '0', '0')")or die(mysql_error());
				// On met un message
				avert('Votre message a bien été envoyé à '.$destinataire.'. Vous allez être redirigé vers votre boîte de réception dans une seconde.');
				// Et on redirige vers la boîte de réception
				
				redirection('mp.html');
			}
		}
		
		else // Sinon le membre n'est pas enregistré dans la table
		{
			avert('Le membre à qui vous souhaitez envoyer ce message n\'existe pas/plus. Vous allez être redirigé vers votre boîte de réception dans 2 secondes');
			redirection('mp.html');
		}
	}
	else // Sinon tous les champs ne sont pas rempli
	{
		echo' 
		<form method="post" action="mp.html?action=ecrire">
		<input type="hidden" name="destinataire" value="'.$_POST['destinataire'].'" />
		<input type="hidden" name="sujet" value="'.$_POST['sujet'].'" />
		<input type="hidden" name="message" value="'.$_POST['message'].'" />
		<input type="hidden" name="recommencer" value="oui" />
		';
		avert('Vous devez remplir tout les champs <input class="boutonlien" type="submit" value="Recommencer" />');
		echo '
		</form>
		';
	}
}

// Sinon si la variable $_Get['action'] est égale à 'LireMpRecu', on affiche la boîte d'envoi
elseif($_GET['action'] == 'envoyer' AND isset($_SESSION['m']['id']) AND !isset($_GET['mp']))
{
	// On récupère les messages qu'on a envoyés et que l'on n'a pas supprimés
	$retour = mysql_query("SELECT id, destinataire, sujet, timestamp FROM mp WHERE expediteur='".$_SESSION['m']['pseudo_mp']."' AND (efface='0' OR efface='1') ORDER BY id DESC")or die(mysql_error());
	
	echo '
	<div class="blocacc">
		<div class="texte">
			<br />
			<span style="float: left;"><img src="' . $repertoire . 'enveloppe.png"/></span><center class="titreSujet">« Message envoy&eacute; »</center>
			<br /><br />
<div class="separate"></div>
			<center><a href="mp.html?action=envoyer"><img src="' . $repertoire . 'envoyé.png"/></a> <a href="mp.html"><img src="' . $repertoire . 'messagerie.png"/></a> <a href="mp.html?action=ecrire"><img src="' . $repertoire . 'nouveau.png"/></a></center>
			<br />

		</div>
	</div>
<br />
	<div class="listageTopic"><table>
		<tr>
			<th class="col2">Sujet</th>
			<th class="col3">Destinataire</th>
			<th width="120">Date</th>
			<th></th>
		</tr>
		';
		
		$ii = 0;
		// On crée une boucle avec les entrées de la table
		while($donnees = mysql_fetch_assoc($retour))
		{
			if($ii % 2 == 0)
			{
				$bloc = 'tr1';
			}	
			else
			{
				$bloc = 'tr2';	
			}
			
			// On enlève les éventuels slashs superflus
			$sujet = stripslashes($donnees['sujet']);
			$destinataire = stripslashes($donnees['destinataire']);   
			$date = $donnees['timestamp'];
			// On ajoute une ligne au tableau pour chaque message
			echo'<tr class="' . $bloc . '"><td><a href="mp.html?action=lire&amp;mp='.$donnees['id'].'">'.$sujet.'</a></td><td class="td"><center>'.$destinataire.'</center></td><td class="td"><center>' .date('d/m/Y H\hi', $date).'</center></td><td class="td"><a href="mp.html?action=supprimer&amp;suppr=2&amp;id='.$donnees['id'].'"><img src="images/moderation/supprimer.gif"></a></td></tr>';
			// On ferme la boucle
			$ii++;
		}
		
		echo '
		
	</table></div>
	';
}

// Si la variable $_GET['id'] qui contient l'id du message existe, si la variable $_GET['suppr'] qui indique qui a supprimé le message (destinataire ou expéditeur) existe et si le variable $_GET['action'] est égale à 'supprimer' qui indique la suppression d'un message, alors on le supprime.
elseif(isset($_GET['action']) AND isset($_GET['suppr']) AND isset($_GET['id']) AND $_GET['action'] == 'supprimer')
{
	$id = $_GET['id'];
	
	if($_GET['suppr'] == 2) // Si c'est l'expéditeur qui supprime le message
	{
		// Alors on récupère les données où l'id du message à supprimer est égale à l'id d'un message         
		$retour = mysql_query("SELECT expediteur, efface FROM mp WHERE id='".$id."'")or die(mysql_error());
		// On les met dans un array
		$donnees = mysql_fetch_assoc($retour);
		
		if($_SESSION['m']['pseudo_mp'] == $donnees['expediteur']) // Si l'expéditeur est bien le membre qui veut supprimer le message
		{
			if($donnees['efface'] == 1) // Et si le message a déjà été supprimé par le destinataire
			{
				// On supprime l'entrée correspondante de la table
				mysql_query("DELETE FROM mp WHERE id='".$id."'")or die(mysql_error());
				
				// On affiche un message
				avert('Le message a été supprimé avec succès. Vous allez être redirigé vers votre boîte de réception dans 2 secondes.');
				
				// Et on redirige
				redirection($_SERVER['HTTP_REFERER']);
			}
			elseif($donnees['efface'] == 0) // Sinon si le message n'a pas été supprimé par le destinataire
			{
				// alors on modifie le champ efface par 2 pour que le destinataire puisse encore voir le message
				mysql_query("UPDATE mp SET efface='2' WHERE id='".$id."'")or die(mysql_error());
				
				// on affiche un message
				avert('Le message a été supprimé avec succès. Vous allez être redirigé vers votre boîte de réception dans 2 secondes.');
				
				// et on redirige
				redirection($_SERVER['HTTP_REFERER']);
			}
			else
			{
				avert('Une erreur est survenue lors de votre demande. Veuillez recommencer ultèrieurement.');
			} 
		}
		else // Sinon, le membre qui veut supprimer le message n'est pas l'expéditeur
		{
			avert('Vous ne pouvez pas supprimer un message que vous n\'avez pas envoyé vous-même.');
		}       
	}
	elseif($_GET['suppr'] == 1) // sinon si c'est le destinataire qui veut supprimer un message
	{
		// On récupère les données sur le message que l'on veut supprimer
		$retour = mysql_query("SELECT destinataire, efface FROM mp WHERE id='".$id."'")or die(mysql_error());
		$donnees = mysql_fetch_assoc($retour);
		
		// Si le destinataire du message est bien le membre qui veut supprimer le message
		if($_SESSION['m']['pseudo_mp'] == $donnees['destinataire'])
		{
			if($donnees['efface'] == 2)
			{
				mysql_query("DELETE FROM mp WHERE id='".$id."'")or die(mysql_error());

				avert('Le message a été supprimé avec succès. Vous allez être redirigé vers votre boîte de réception dans 2 secondes.');

				redirection($_SERVER['HTTP_REFERER']);
			}
			// sinon si le message n'a pas été supprimé par l'expéditeur
			elseif($donnees['efface'] == 0)
			{
				// alors on modifie la valeur de efface par 1 pour que l'expéditeur puisse encore voir le message
				mysql_query("UPDATE mp SET efface='1' WHERE id='".$id."'")or die(mysql_error());

				avert('Le message a été supprimé avec succès. Vous allez être redirigé vers votre boîte de réception dans 2 secondes.');

				redirection($_SERVER['HTTP_REFERER']);
			}
			else
			{
				avert('Une erreur est survenue lors de votre demande. Veuillez recommencer ultèrieurement.');
			} 
		}
		else // sinon le membre qui veut supprimer le message n'est pas le destinataire de celui-ci
		{
			// donc on affiche un message d'erreur
			avert('Vous ne pouvez pas supprimer un message qui ne vous a pas été envoyé.');
		}   
	}
}
else // Sinon on met un message d'erreur qui envoie un lien pour se connecter.
{
	avert('Vous n\'êtes pas connecté ou une erreur est survenue lors de votre demande ; veuillez recommencer ultérieurement.<a href="connexion.html">Se connecter</a>');
}

include('pied.php');
?>