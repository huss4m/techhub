<?php
include('base.php');

// Debut - Vérification d'accès à la page
	// Vérifions que l'url n'a pas été modifié par le visiteur
	if(!isset($_GET['id']) OR !ctype_digit($_GET['id']))
	{
		include('tete.php');
		avert('Le lien n\'est pas correct');
		include('pied.php');
		exit;
	}

	// Vérifions si la news existe bien
	$reponse = mysql_query('SELECT * FROM news WHERE id=' . $_GET['id']) OR die(mysql_error());
	$result = mysql_num_rows($reponse);

	if((int) $result == 0) // ( pour php, 0 = false, donc il faut préciser qu'on attend un chiffre )
	{
		include('tete.php');
		avert('Les commentaires de la news que vous avez demandé n\'existe pas ou n\'existe plus.');
		include('pied.php');
		exit;
	}

	// Vérifions si la news n'a pas été mise hors ligne
	$reponse = mysql_query('SELECT * FROM news WHERE id=' . $_GET['id']) OR die(mysql_error());
	$donnees = mysql_fetch_array($reponse);

	if($donnees['statut'] == 0) // Si la news vaut false (Hors ligne)
	{
		include('tete.php');
		avert('La news a été mise hors ligne pour le moment et par conséquent les commentaires aussi.');
		include('pied.php');
		exit;
	}
// Fin - Accès la page

// Debut - Ajout d'un commentaire
	if(isset($_POST['message']) AND isset($_SESSION['m']['id']))
	{
		if($_POST['message'] != NULL)
		{
			if(isset($_POST['verif_code']) AND !Empty($_POST['verif_code'])) // Le champ du code de confirmation a été remplis
			{ 
				if($_POST['verif_code'] == $_SESSION['aleat_nbr']) // Si le champ est égal au code généré par l'image
				{
					$message = secure($_POST['message']);
					
					mysql_query('INSERT INTO commentaires VALUES("", "' . $_SESSION['m']['pseudo'] . '", "' . $message . '", "' . secure($_GET['id']) . '", "' . time() . '")')or die (mysql_error());
					header('Location: commentaires-' . intval($_GET['id']) . '.html');
					exit;
				}
			}
		}
	}
// Fin - Ajout d'un commentaire

$reponse = mysql_query('SELECT * FROM bannIp WHERE ip="' . $_SERVER['REMOTE_ADDR'] . '"') OR die(mysql_error());
$result = mysql_num_rows($reponse);

if((int) $result != 0)
{
	setcookie('lol', 1, (time() + 365*24*3600));
	echo 'Vous avez été banni du site.';
	exit;
}

if(isset($_COOKIE['lol']) && $_COOKIE['lol'] == 1)
{
	echo 'Vous avez été banni du site.';
	exit;
}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>• Spadox •</title>
		<link rel="shortcut icon" type="image/jpg" href="favicon.gif" /> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" media="screen" type="text/css" href="design.php" />
		<script type="text/javascript" src="js.js"></script>
	</head>
    <body class="apercu">
	';

// Récupérons le titre de la news
$reponse = mysql_query('SELECT titre FROM news WHERE id=' . $_GET['id']) OR die(mysql_error());
$donnees = mysql_fetch_array($reponse);
$titreNews = $donnees['titre'];
$formulaire = '
<div id="bloc1">
	<h3>Ajouter un nouveau commentaire</h3>
	<div class="texte">
		<form method="post">
			<b><font color="#dd0000">* Pseudo :</font></b> <a href="option.html" style="line-height: 20px">' . $_SESSION['m']['pseudo'] . '</a><br />
			<b>* Message : </b><br />
			<textarea name="message" rows="8" cols="60"></textarea><br /><br />
			<strong style="color: #dd0000;">Recopiez le code ci-contre :</strong> <input type="text" name="verif_code" size="6" maxlength="6"/> <img src="verif_code_gen.php" alt="Code de vérification" /><br />
			<input type="hidden" name="idcomment" value="' . $_GET['id'] . '" />
			<center><input type="submit" value="Envoyer !" /></center>
		</form>
	</div>	
	<div class="bloc1bas"></div>
';

// Si on demande à supprimer un commentaire
if(isset($_GET['supprimerCom']) AND $_SESSION['m']['acces'] == 100)
{
	mysql_query('DELETE FROM commentaires WHERE id=' . db_sql::escape(intval($_GET['supprimerCom'])) . '');
	avert('Le message a bien été supprimé.');
}

echo '
<h1><center>Commentez : <q>' . stripslashes(nl2br(htmlspecialchars($titreNews))) . '</q></center></h1>
';

// Système de pagination :
$nombreDeMessagesParPage = 10;

$retour = mysql_query('SELECT COUNT(*) AS nb_messages FROM commentaires WHERE idNews=' . $_GET['id'] . '');
$donnees = mysql_fetch_array($retour);
$totalDesMessages = $donnees['nb_messages'];

if($totalDesMessages == 0)
{
	avert('Pas de commentaires pour le moment.<br /><br />');
	
	if(!isset($_SESSION['m']['pseudo']))
	{
		echo '<center><b>Vous devez être connecté pour pouvoir ajouter un commentaire.</b></center>';
	}
	else
	{
		echo '
		' . $formulaire . '
	</div>
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
' . page('commentaires-' . $_GET['id'], $page, $nb_page);


// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;

$reponse = mysql_query('SELECT * FROM commentaires WHERE idNews=' . $_GET['id'] . ' ORDER BY id DESC LIMIT ' . $premierMessageAafficher . ', ' . $nombreDeMessagesParPage) OR die(mysql_error());

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

	if(isset($_SESSION['m']['pseudo']) AND $_SESSION['m']['acces'] == 100)
	{	
		$supprimer_message = '<a href="commentaires.php?id=' . $_GET['id'] . '&amp;page=' . $page . '&amp;supprimerCom=' . $donnees['id'] . '" title="Supprimer ce message" onclick="reponse = confirm(\'Êtes-vous SÛR(E) de vouloir SUPPRIMER ce message ?\'); return reponse;"><img src="images/dell.gif" /></a> ';
	}
	else
	{
		$supprimer_message = '';
	}
	
	echo '
	<div class="blocMessage">
		<div class="blocMessageHaut' . $a . '"></div>
		<div class="' . $msg_msg . '" id="message_' . $donnees['id'] . '">
			<ul>
				<li class="pseudo">' . $supprimer_message . '<strong>' . $donnees['pseudo'] . '</strong></li>
				<li class="date">Posté le ';
				$timestamp = $donnees['timestamp'];
				//Tableau des jours et des moi en français
				$mois_fr = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
				// on extrait la date du jour
				list($jour, $mois, $annee) = explode('/', date('d/n/Y', $timestamp));
				echo $jour . ' ' . $mois_fr[$mois] . ' ' . $annee; 
				echo ' à  ';
				echo date('H\:i\:s', $timestamp);
			 		
			echo '
			</li>
				<li class="post">
				' . bbCode(stripslashes(nl2br(htmlspecialchars($donnees['message'])))) . '
				';
				echo '
				</li>
			</ul>
		</div>
		<div class="blocMessageBas' . $a . '"></div>
	</div>
	';
	$i++;
}

echo page('commentaires-' . $_GET['id'], $page, $nb_page);

if(!isset($_SESSION['m']['pseudo']))
{
	avert('Vous devez être connecté pour pouvoir ajouter un commentaire.');
}
else
{
	echo $formulaire;
?>
<br />
<center>
	<a href="javascript:window.close()">Fermer la fenêtre</a>
	<br /><br />
</center>
<?php
}
?>