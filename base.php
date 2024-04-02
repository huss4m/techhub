<?php
@session_start();
error_reporting(E_ERROR | E_PARSE);

error_reporting(E_ERROR | E_PARSE); // On reporte les erreus possible
$cookie = &$_COOKIE;
$session = &$_SESSION;
$env = &$_ENV;
$post = &$_POST;
$get = &$_GET;
$server = array();
while(list($their, $key) = each($_SERVER)) {
$server[strtolower($their)] = ($_SERVER) ? $_SERVER[strtoupper($their)] : getenv(strtoupper($their));
$server[$their] = ($_SERVER) ? $_SERVER[strtoupper($their)] : getenv(strtoupper($their)); }
include('config.php'); // On inclut un autre fichier avec les fonctions

	if(!empty($_POST))
	{
		$pageReferer = str_replace('http://', '', $_SERVER['HTTP_REFERER']) ;
		$pageReferer = explode('/', $pageReferer) ;
		
		if($pageReferer[0] != $_SERVER['HTTP_HOST'] AND isset($_SERVER['HTTP_REFERER']) AND !empty($_SERVER['HTTP_REFERER'])) // Si le formulaire vient d'une page externe
		{
			include('tete.php');
			
			echo '<strong class="erreur">Vous n\'avez pas accès à cette page dans les circonstances actuelles.</strong><br />
			Si vous ne savez pas pourquoi ce message d\'erreur apparait, merci de reporter le bug <br />
			1. ' . $_SERVER['HTTP_REFERER'] . '<br />
			2. ' . $pageReferer[0] . '<br />
			3. ' . $_SERVER['HTTP_HOST'] ;
			
			$MYSQL_OUT = 1 ; // On dit à pied.php de ne pas fermer la connexion MySQL, étant donn� qu'on en a pas ouverte
			include('pied.php');
			exit;
		}
	}
	
// Debut - Eviter le renvoie de donn�s avec $_POST

	// D�but - Premi�re partie
	if(!empty($_POST) OR !empty($_FILES))
	{
		$_SESSION['sauvegarde'] = $_POST ;
		$_SESSION['sauvegardeFILES'] = $_FILES ;
		
		$fichierActuel = $_SERVER['PHP_SELF'] ;
		if(!empty($_SERVER['QUERY_STRING']))
		{
			$fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
		}
		
		header('Location: ' . $fichierActuel);
		exit;
	}
	// Fin - Premi�re partie

	// D�but - Seconde partie
	if(isset($_SESSION['sauvegarde']))
	{
		$_POST = $_SESSION['sauvegarde'] ;
		$_FILES = $_SESSION['sauvegardeFILES'] ;
		
		unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
	}
	// Fin - Seconde partie

// Fin - Eviter le renvoie de donn�s avec $_POST

// Si le mec est ban quand il est co
if(isset($_SESSION['m']['id'], $_SESSION['m']['acces']))
{
	$reponse = mysql_query('SELECT acces FROM membres WHERE id = ' . $_SESSION['m']['id'] . '');
	$donnees = mysql_fetch_assoc($reponse);
	
	if($donnees['acces'] != 100)
	{
		if($donnees['acces'] == 1)
		{
			// On le vire direct
			session_destroy();
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit;
		}
	}
}

// Le membre est co, on met � jour son ip dans la table
if(isset($_SESSION['m']['id']))
{
	$newsIp = $_SERVER['REMOTE_ADDR']; // Nouvelle ip du membre
	mysql_query('UPDATE membres SET ip=' . $newsIp . ' WHERE id=' . $_SESSION['m']['id']);
}

// Debut - Gestions liste des connect�s
	if(isset($_SESSION['m']['id']))
	{
		$idPseudo = $_SESSION['m']['id'];
		$Sacces = $_SESSION['m']['acces'];
	}
	else
	{
		$idPseudo = 0;
		$Sacces = 0;
	}

	$reponse = mysql_query('SELECT COUNT(*) AS nb FROM moderateurs WHERE idPseudo = ' . $idPseudo);
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['nb'] != 0)
	{
		$Sacces = 50;
	}
	
	$reponse = mysql_query('SELECT COUNT(*) AS nb FROM admin WHERE idPseudo=' . $idPseudo . '');
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['nb'] != 0)
	{
		$Sacces = 100;
	}
	
	$reponse = mysql_query('SELECT COUNT(*) AS nb FROM supermodo WHERE idPseudo=' . $idPseudo . '');
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['nb'] != 0)
	{
		$Sacces = 90;
	}
	
	$reponse = mysql_query('SELECT COUNT(*) AS nb FROM directeur WHERE idPseudo=' . $idPseudo . '');
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['nb'] != 0)
	{
		$Sacces = 95;
	}
	
	$reponse = mysql_query('SELECT COUNT(*) AS nb FROM codeur WHERE idPseudo=' . $idPseudo . '');
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['nb'] != 0)
	{
		$Sacces = 98;
	}

	// On regarde si l'ip n'est pas d�j� pr�sente dans la table
	$retour = mysql_query('SELECT COUNT(*) AS nbre_entrees FROM connectes WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
	$donnees = mysql_fetch_assoc($retour);

	if($donnees['nbre_entrees'] == 0) // L'ip ne se trouve pas dans la table, on va l'ajouter
	{
		mysql_query('INSERT INTO connectes VALUES(\'' . $_SERVER['REMOTE_ADDR'] . '\', ' . time() . ', ' . $idPseudo . ', ' . $Sacces . ')');
	}
	else // L'ip se trouve d�j� dans la table, on met juste � jour le timestamp
	{
		mysql_query('UPDATE connectes SET timestamp=\'' . time() . '\', acces=\'' . $Sacces . '\', idPseudo=\'' . $idPseudo . '\' WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
	}

	// Suppression des membres inactifs depuis plus de 5min
	$timestamp_5min = time() - (60 * 5);
	mysql_query('DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);
// Fin - Gestions listes des co


// Debut - Cookie/Session timestamp dernière visite
	if(!isset( $_SESSION['tmp_derniere_visite'])) 
	{
		// Si le temps de dernière visite (cookie) n'existe pas, on le crée.
		if(!isset($_COOKIE['tmp_der_visite']))
		{
			setcookie('tmp_der_visite',time(), time() + (3600*24*365));
		}
					
		// On enregistre le temps de dernière visite de la session
		$_SESSION['tmp_derniere_visite'] = $_COOKIE['tmp_der_visite'];
		
		// On met le temps de dernière visite à jour, pour les prochaines visites
		setcookie('tmp_der_visite', time(), time() + (3600*24*365));  
	}
	else 
	{
		setcookie('tmp_der_visite', time(), time() + (3600*24*365));
	}
// Fin - Cookie/Session timestamp derniere visite

	if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] != 100 AND $_SESSION['m']['acces'] != 90 AND $_SESSION['m']['acces'] != 95 AND $_SESSION['m']['acces'] != 45 AND $_SESSION['m']['acces'] != 98)
	{
		$_SESSION['m']['acces'] = 10;
	}

// Debut - Config du Forum
	$configForum = file_get_contents('configforum.txt');
	$configForum = explode('-', $configForum);

	$config['forum']['nbTopicParPage'] = $configForum[0];
	$config['forum']['nbMessageParPage'] = $configForum[1];
	$config['forum']['tempsEntreChaqueTopic'] = $configForum[2];
	$config['forum']['tempsEntreChaqueMessage'] = $configForum[3];
	$config['forum']['tempsFonctionEditer'] = $configForum[4];
	$config['forum']['nbsmiley'] = $configForum[5];
	
// Fin - Config du Forum
define('TITRE_FORUM', 'TechHub');
define('NOM_ARGENT', 'Points');

$COLP = new Color;
$COLP->setColor($OPTIONS['colorm'], $OPTIONS['colormo'], $OPTIONS['colora'], $OPTIONS['colormod'], $OPTIONS['colordir'], $OPTIONS['colored'], $OPTIONS['colorod']);

// D�but - S�curisation du script
	if (preg_match("#\D#", $_GET['page']) AND $_GET['page'] != "last")
	{
		avert(bbcode("Votre ip a bien &eacute;t&eacute; enregistr&eacute;."));
		exit;
	}
	$_GET['page'] = secure($_GET['page']);
	
	if(preg_match("#\D#", $_GET['marquer']) )
	{
		avert(bbcode("Votre ip a bien &eacute;t&eacute; enregistr&eacute;."));
		exit;
	}
	$_GET['marquer'] = secure($_GET['marquer']);
	
// Fin - S�curisation du script



?>
