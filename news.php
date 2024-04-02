<?php
include('base.php');

// Vérifions que l'url n'a pas été modifié par le visiteur
if(!isset($_GET['id']) OR !ctype_digit($_GET['id']))
{
	include('tete.php');
	avert('Erreur 404 : Ce lien existe plus/pas ou alors est redirigé autre part.</b>');
	include('pied.php');
	exit;
}



include('tete.php');

if(isset($_GET['supprimerCom']) AND $_SESSION['m']['acces'] > 92)
{
	$bdd->sql_query('DELETE FROM commentaires WHERE id=?', false, true, true, array(intval($_GET['supprimerCom'])));
	avert('Le message a bien été supprimé.');
}

if(isset($_GET['id']) AND ctype_digit($_GET['id']))
{
			$retour = mysql_query('SELECT * FROM cours WHERE id=' . $_GET['id'] . '') ;
			$donnees = mysql_fetch_assoc($retour);
			
				echo '
                    <script type="text/javascript">
					//<![CDATA[
                    <!--
                    function resize(imageposte)
                    {
                    var resizeLargeur = imageposte.offsetWidth ;
                    var resizeHauteur = imageposte.offsetHeight ;


                    // { Début - Redimensionnement si trop grand
                    if(resizeLargeur > 70)
                    {
                    imageposte.width = 70 ;
                    resizeLargeur -= 70 ;
                    }

                    if(resizeHauteur > 70)
                    {
                    imageposte.height = 70 ;
                    resizeHauteur -= 70 ;
                    }
                    // } Fin - Redimensionnement si trop grand


                    // { Début - Redimensionnement si écart plus grand que 150px
                    var differance = resizeLargeur - resizeHauteur ;
                    if(differance > 70 || differance < -70)
                    {
                    var largeurFinale = 100 ;
                    var hauteurFinale = 100 ;

                    if(differance > 0) // La largeur est plus grande que la hauteur
                    {
                    // Nb de fois que la largeur a été retranchée
                    var nbRetranche = Math.round(imageposte.width / 140);
                    hauteurFinale -= (25 * nbRetranche);
                    }
                    else // La hauteur est plus grande que la largeur
                    {
                    var nbRetranche = Math.round(imageposte.height / 140);
                    largeurFinale -= (25 * nbRetranche);
                    }

                    if(largeurFinale > 30 && hauteurFinale > 30)
                    {
                    imageposte.width = largeurFinale ;
                    imageposte.height = hauteurFinale ;
                    }
                    }
                    // } Fin - Redimensionnement si écart trop grand
                    }
                    // -->
                    //]]>
                    </script>
				';
	echo '
	<div class="desavertissement">
	<center><b>' . nl2br(text_i($donnees['titre'])) . '</b></center>
	</div><br />
	<div class="bloc2">
			<h3>Introduction</h3>
			<div class="texte">
			<span style="float: left;"><img src="' . nl2br(text_i($donnees['lien'])) . '" width="170px" height="88px"></span>
			<div class="readIntro">' . nl2br(bbCode(text_i($donnees['intro']))) . '<br /><br /><br /><br /><br /><br /></div>
			</div>
		</div><br />
		<div class="bloc2">
		<h3>Cours</h3>
		<div class="texte">
			' . nl2br(bbCode(text_i($donnees['cours']))) . '<br /><br />
		<span style="float: right;"><b>Publi&eacute; le ' . date('d/m/Y\ H\:i\\:s', $donnees['timestamp']) . '</b></span>
		<span style="float: left;"><b>Ecrit par <font color="red">' . nl2br(text_i($donnees['auteur'])) . '</font></b></span><br />
		</div>
		</div>
			<br /><br />
			';
	}
/*
// Debut - Ajout d'un commentaire
	if(isset($_POST['message']))
	{
		if(isset($_SESSION['m']['id']))
		{
			if($_POST['message'] != NULL)
			{
				$requete = mysql_query('SELECT argent FROM membres WHERE id = ' . $_SESSION['m']['id']);
				$shop = mysql_fetch_assoc($requete);
				$argent = $shop['argent'];
				$argentcours = $argent + 3;
				$pseudo = secure($_POST['pseudo']);
				$message = secure($_POST['message']);
				$idcomment = secure($_POST['idcomment']);
				
				// On insère le commentaire dans la table
				$bdd->sql_query('INSERT INTO commentaires VALUES("", "' . $_SESSION['m']['pseudo'] . '", "' . $message . '", "' . $idcomment . '", "' . time() . '")', false, true);
				avert('Votre message a bien été ajouté.');
				
				// On donne 3 d'argent au membre
				$bdd->sql_query('UPDATE membres SET argent=\'' . db_sql::escape($argentcours) . '\' WHERE id=' . $_SESSION['m']['id'], false, true);
			}
		}
	}
// Fin - Ajout d'un commentaire

$reponse = mysql_query('SELECT * FROM bannIp WHERE ip="' . $_SERVER['REMOTE_ADDR'] . '"') OR die(mysql_error());
$result = mysql_num_rows($reponse);

if((int) $result != 0)
{
	setcookie('lol', 1, (time() + 365*24*3600));
	avert('Vous avez été banni du site.');
	exit;
}

if(isset($_COOKIE['lol']) && $_COOKIE['lol'] == 1)
{
	avert('Vous avez été banni du site.');
	exit;
}
	
	// Debut - Couleur du Pseudo
			if($donnees['acces'] > 92) // Admin
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
			else // Membre
			{
				$pseudo = ' <strong>' . $donnees['pseudo'] . '</strong>';
			}
		// Fin - Couleur du Pseudo
*/
// Récupérons le titre de la cours
$reponse = mysql_query('SELECT titre FROM cours WHERE id=' . $_GET['id']) OR die(mysql_error());
$donnees = mysql_fetch_array($reponse);
$titrecours = $donnees['titre'];
$formulaire = '
<br /><div class="bloc2">
	<h3>Ajouter votre commentaire</h3>
	<div class="texte">
		<form method="post" action="cours.php?id=' . $_GET['id'] . '&d=0">
			<b><font color="#dd0000">* Pseudo :</font></b> <a href="option.html" style="line-height: 20px">' . $_SESSION['m']['pseudo'] . '</a><br />
			<b>* Message : </b><br />
			<textarea name="message" rows="8" cols="60"></textarea><br /><br />
			<input type="hidden" name="idcomment" value="' . $_GET['id'] . '" />
			<img src="images/bouton/apercu.gif" alt="Aperçu"/></a> 
			<input border="0" type="image" value="submit" src="images/bouton/poster.gif" class="inputBouton" />
			
			<form action="apercu.html" method="post" name="apercuMessage" style="display: inline;"> 
		 <input type="hidden" name="pseudo" />
		 
		 
			<div class="separate"></div>
<img src="images/defaut/puce_base.gif" /> <a href="listeSmileys.php" onclick="window.open(\'\',\'popup\',\'width=800,height=820,scrollbars=yes,status=no\')" target="popup">Liste des smileys</a> | <a href="charte.html">Charte des forums</a><br />
		</form>
	</div>	
	<div class="bloc1bas"></div></div>
';
?>

<?php/*
// Si on demande à supprimer un commentaire
$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS nbCom FROm commentaires WHERE idcours=' . intval($_GET['id']));
$donnees = mysql_fetch_array($reponse);
$totalDeCom = $donnees['nbCom'];
if ($totalDeCom >= 2)
{
$comacs = 1;
}

echo '
<br />
			<div class="bloc2">
			<h3><span>LES COMMENTAIRES DES LECTEURS</span></h3>
		<div class="texte">
			<center>Il y a actuellement <font color="#dd0000"><b>' . $totalDeCom . ' </b></font> commentaire'; if ($comacs == 1) { echo's'; } echo'</center>
			<br />
</div>
</div><br /><br />';



// Système de pagination :
$nombreDeMessagesParPage = 10;

$retour = mysql_query('SELECT COUNT(*) AS nb_messages FROM commentaires WHERE idcours=' . $_GET['id'] . '');
$donnees = mysql_fetch_array($retour);
$totalDesMessages = $donnees['nb_messages'];

if($totalDesMessages == 0)
{
	
	if(!isset($_SESSION['m']['pseudo']))
	{
		avert('Vous devez être connecté pour pouvoir ajouter un commentaire.</b></center>');
	}
	else
	{
		echo '
		' . $formulaire . '
		';
	}
	
	include('pied.php');
	exit;
}

$nb_page = ceil($totalDesMessages / $nombreDeMessagesParPage);

// On regarde à quel page on se trouve :
if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
{
    $page = intval($_GET['page']);
}
else
{
    $page = 1;
}

echo '
<br />
' . page('cours-' . $_GET['id'], $page, $nb_page);


// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;

$reponse = mysql_query('SELECT * FROM commentaires WHERE idcours=' . $_GET['id'] . ' ORDER BY id LIMIT ' . $premierMessageAafficher . ', ' . $nombreDeMessagesParPage) OR die(mysql_error());

$i = 0;
while($donnees = mysql_fetch_assoc($reponse))
{
	// Alternance de couleur
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

	if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] > 92)
	{	
		$supprimer_message = '<a href="cours.php?id=' . $_GET['id'] . '&amp;page=' . $page . '&amp;supprimerCom=' . $donnees['id'] . '" title="Supprimer ce message" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir SUPPRIMER ce Commentaire ?\'); return reponse;"><img src="images/moderation/supprimer.gif" /></a> ';
	}
	else
	{
		$supprimer_message = '';
	}
	
	echo '
	<div class="message" id="message_1">
		<div class="table">
			<div class="tr">
				<div class="msg2">
					<div class="td gauche">
						<div class="pseudo">
						    ' . $donnees['pseudo'] . '
						</div>
						<div class="img">
						</div>
						<div class="nbPost">
						</div>
					</div>
					<div class="td droite">
						<div class="date">
						
						</div>
						<div class="fonctions">
						<b><a><img src="images/message/bt_forum_profil.gif" width="11" height="12"></a> ' . $supprimer_message  . ' <img src="images/message/bt_forum_avertirmod.gif"> Post&eacute; ' . $mobile .  ' le ';
						
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
						<div class="post">
                      	' . bbCode(stripslashes(nl2br(htmlspecialchars($donnees['message'])))) . '
						<div class="lienPermanent">
						<br /><br /><a>Message</a>
					    </div>
					  </div>
				    </div>
			    </div>
		  	 </div>
	     </div>
	   </div>
    </div>
			';
	$i++;
}

echo page('cours-' . $_GET['id'], $page, $nb_page);

if(!isset($_SESSION['m']['pseudo']))
{
	avert('Vous devez être connecté pour pouvoir ajouter un commentaire.');
}
if(isset($_SESSION['m']['pseudo']))
{
echo $formulaire;
}*/
include('pied.php');
?>
