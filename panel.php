<?php
include('base.php');

if (preg_match("#\D#", $_GET['id']))
{
    avert(bbcode("interdit"));
	exit();
}
$_GET['id']= secure($_GET['id']);



// Debut - Autorisation d'acces à la page
	if(!isset($_SESSION['m']['acces']) OR $_SESSION['m']['acces'] < 92) // Si le membre fait parti de l'équipe
	{
		include('tete.php');
		avert('Vous n\'avez pas acces au Panel.');
		include('pied.php');
		exit;
	}

// Fin - Autorisation d'acces à la page

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Panel administration</title>
    <link href="css/admin.css"rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
      }
    </style>
    <link rel="shortcut icon" href="images/ico.png">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
	<link href="popup/popup.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="popup/popup.js"></script>
	<script type="text/javascript" src="js.js"></script>
  </head>
  <body>
  <div id="lolilol" onDblclick="popupoff('wait');"></div>
<?php
// Début - On défini le pied de la page
$url = $_SERVER['REQUEST_URI'];

if(stristr($url, 'panel.php?accueil') || stristr($url, 'panel.htm?accueil') || stristr($url, 'panel.html?accueil')) // On est sur l'accueil
{
	$hautDePage = ''; // On n'affiche pas le Haut de Page
}
else // On est sur les autres pages
{
	// Alors on l'affiche
	$hautDePage = '<br /><div style="margin-left: 350px;"><a href="#" class="btn info"><img src="images/panel/haut.png"> Haut de page</a></div>';
}

$pied = '
		' . $hautDePage . '
        <footer>
          <p>&copy; <a href="accueil.htm">TechHub</a> | <a href="panel.htm?accueil">Panel d\'administration</a></a></p>
        </footer>
      </div>
    </div>
	
  </body>
</html>';
// Fin - On défini le pied de la page

// Début - Define du panel
define('PIED', $pied);
define('PSEUDO', $_SESSION['m']['pseudo']);
// Fin - Define du panel

// Début - Fonction
function error($message)
{
	echo'<center><font color="red">
	<b>' . $message . '</b>
	</font></center>';
}
// Fin - Fonction

// Début - Affichage des éléments importants de la page
	// Début - Bandeau du haut
	echo'
		<div class="topbar">
		  <div class="topbar-inner">
			<div class="container-fluid">
			  <a class="brand" href="panel.htm?accueil">Administration</a>
			  <ul class="nav">
				<li><a href="accueil.htm">Retour au forum</a></li>
				</ul>
			  <p class="pull-right">Bienvenue ' . PSEUDO . '</p>
			</div>
		  </div>
		</div>
	';
	// Fin - Bandeau du haut

	// Début - Menu de gauche
	echo'
		<div class="container-fluid">
		  <div class="sidebar">
			<div class="well">
			  <h5>Gestions/créations</h5>
			  <ul>
				<li><a href="panel.htm?accueil">Accueil panel</a></li>
				<li><a href="panel.htm?gestionForum">Gestion forum</a></li>
				<li><a href="panel.htm?gestionStaff">Gestion staff</a></li>
				<li><a href="panel.htm?gestionMembres">Gestion membre</a></li>
				<li><a href="panel.htm?gestionSmiley">Gestion smiley</a></li>
				<li><a href="panel.htm?espaceRedaction">Espace Rédaction</a></li>
			  </ul>
			</div>
		  </div>
	';
	// Fin - Menu de gauche

	echo'<div class="content">'; // Affichage de la page
	
// Fin - Affichage des éléments importants de la page

// Début - Ajout, suppression, et edition

	// Début - Configuration
	if(isset($_POST['tempsChaqueTopic'], $_POST['tempsChaqueMessage'], $_POST['editer'], $_POST['nbTopicParPage'], $_POST['nbMessageParPage'], $_POST['nbsmiley']))
	{
		$fichier = fopen('configforum.txt', 'r+');
		$contenuDuFichier = intval($_POST['nbTopicParPage']) . '-' . intval($_POST['nbMessageParPage']) . '-' . intval($_POST['tempsChaqueTopic']) . '-' . intval($_POST['tempsChaqueMessage']) . '-' . intval($_POST['editer']) . '-' . intval($_POST['nbsmiley']) . '-';
		fseek($fichier, 0); 
		fputs($fichier, '                         ');
		fseek($fichier, 0);
		fputs($fichier, $contenuDuFichier);
		redirection('panel.htm?gestionForum');
		error('La configuration des forums a bien été modifiée.');
	}
	// Fin - Configuration
	
			// Debut - Ajouter un admin
			if(isset($_POST['admin'])) // Si $_POST['admin'] n'est pas vide
			{
				$requete = mysql_query('SELECT * FROM admin WHERE idPseudo = ' . secure($_POST['admin'])); // On cherche s'il est pas déjà admin
				$result = mysql_num_rows($requete);
				
				if($result > 0) // S'il est déjà admin, on fait rien du tout
				{
					avert('Ce membre est déjà admin');
				}
				elseif($result == 0)  // Sinon on insère dans la table
				{
					$tablettre = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",";","^","$","!",":","[","]","{","}");
				 
					mt_srand((float) microtime()*10000);


					$a = mt_rand(0, 9);
					$b = mt_rand(0, 9);
					$c = mt_rand(0, 9);
					$d = mt_rand(0, 9);
					$e = mt_rand(0, 9);
					
					$f = mt_rand(0, 35);
					$g = mt_rand(0, 35);
					$h = mt_rand(0, 35);
					$i = mt_rand(0, 35);
					$j = mt_rand(0, 35);

					$motaleatoire =  $a.$tablettre[$f].$b.$tablettre[$g].$c.$tablettre[$h].$d.$tablettre[$i].$e.$tablettre[$j]; // Avec les nombres généré on attribut un caractère de la tablette
					
					// On insère le tout dans la table
					mysql_query('INSERT INTO admin VALUES("", "' . secure($_POST['admin']) . '", "' . $motaleatoire . '")');
					mysql_query("UPDATE membres SET acces='100' WHERE id='" . secure($_POST['admin']) . "'");
					avert('L\'admin a bien été désigné');
				}
				else // S'il y a une erreur quelconque
				{
				avert('Une erreur c\'est produite');
				}
			}
		// Fin - Ajouter admin

		// Debut - Ajouter un modérateur
			if(isset($_POST['modo']))
			{
				// Recherche s'il est pas déjà modo sur un forum
				$requete = mysql_query('SELECT * FROM moderateurs WHERE idForum = ' . secure($_POST['modoForum']) . ' AND idPseudo = ' . secure($_POST['modo']));
				$result = mysql_num_rows($requete);

				if($result > 0)
				{
					avert('Ce membre est déjà modérateur de ce forum');
				}
				elseif($result == 0)
				{
					$tablettre = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",";","^","$","!",":","[","]","{","}");
				 
					mt_srand((float) microtime()*10000);


					$a = mt_rand(0, 9);
					$b = mt_rand(0, 9);
					$c = mt_rand(0, 9);
					$d = mt_rand(0, 9);
					$e = mt_rand(0, 9);
					
					$f = mt_rand(0, 35);
					$g = mt_rand(0, 35);
					$h = mt_rand(0, 35);
					$i = mt_rand(0, 35);
					$j = mt_rand(0, 35);

					$motaleatoire =  $a.$tablettre[$f].$b.$tablettre[$g].$c.$tablettre[$h].$d.$tablettre[$i].$e.$tablettre[$j];
					mysql_query('INSERT INTO moderateurs VALUES("", "' . secure($_POST['modo']) . '", "' . secure($_POST['modoForum']) . '", "' . $motaleatoire . '")');
					avert('Le modérateur a bien été désigné');
				}
				else // S'il y a une erreur quelconque
				{
				avert('Une erreur c\'est produite');
				}
				
			}
		// Fin - Ajouter un modérateur
		
		// Debut - Supprimer un admin
			if(isset($_GET['supprimerAdmin']) AND intval($_GET['supprimerAdmin']))
			{
				$continuer = 0;
				
				mysql_query("UPDATE membres SET acces='10' WHERE id='" . intval($_GET['supprimerAdmin']) . "'");	
				mysql_query("UPDATE message SET acces='10' WHERE idPseudo='" . intval($_GET['supprimerAdmin']) . "'");
				mysql_query("UPDATE topic SET acces='10' WHERE idPseudo='" . intval($_GET['supprimerAdmin']) . "'");
				
				$continuer = 1;
				
				if($continuer == 1)
				{
					mysql_query('DELETE FROM admin WHERE id=' . intval($_GET['idAdmin']) . '');
				}
				
				avert('L\'admin a bien été dé-assigné.');
			}
		// Fin - Supprimer un admin
		
		// Debut - Modifier Pass Admin
			if(isset($_GET['modifierPassAdmin']))
			{
					$tablettre = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",";","^","$","!",":","[","]","{","}");
				 
					mt_srand((float) microtime()*10000);


					$a = mt_rand(0, 9);
					$b = mt_rand(0, 9);
					$c = mt_rand(0, 9);
					$d = mt_rand(0, 9);
					$e = mt_rand(0, 9);
					
					$f = mt_rand(0, 35);
					$g = mt_rand(0, 35);
					$h = mt_rand(0, 35);
					$i = mt_rand(0, 35);
					$j = mt_rand(0, 35);

				$motaleatoire =  $a.$tablettre[$f].$b.$tablettre[$g].$c.$tablettre[$h].$d.$tablettre[$i].$e.$tablettre[$j];
				
				mysql_query("UPDATE admin SET passe='" . secure($motaleatoire) . "' WHERE id='" . intval($_GET['modifierPassAdmin']) . "'");
				avert('Le pass de l\'admin est actualisé');
			}
		// Fin - Modofier Pass Admin

		// Debut - Supprimer un modérateur
			if(isset($_GET['supprimerModo']) AND intval($_GET['supprimerModo']))
			{
				$reponse = mysql_query('SELECT idPseudo FROM moderateurs WHERE id=' . $_GET['supprimerModo'] . '');
				$donnees = mysql_fetch_assoc($reponse);
				$idPseudoModo = htmlspecialchars(stripslashes($donnees['idPseudo']));
				
				mysql_query("DELETE FROM moderateurs WHERE id='" . intval($_GET['supprimerModo']) . "'");
				mysql_query("UPDATE topic SET acces='10' WHERE idPseudo='" . $idPseudoModo . "'");
				mysql_query("UPDATE message SET acces='10' WHERE idPseudo='" . $idPseudoModo . "'");
				
				avert('Le modérateur a bien été dé-assigné.');
			}
		// Fin - Supprimer un modérateur
		
		// Debut - Modifier Pass Modo
			if(isset($_GET['modifierPassModo']) AND intval($_GET['modifierPassModo']))
			{
					$tablettre = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",";","^","$","!",":","[","]","{","}");
				 
					mt_srand((float) microtime()*10000);


					$a = mt_rand(0, 9);
					$b = mt_rand(0, 9);
					$c = mt_rand(0, 9);
					$d = mt_rand(0, 9);
					$e = mt_rand(0, 9);
					
					$f = mt_rand(0, 35);
					$g = mt_rand(0, 35);
					$h = mt_rand(0, 35);
					$i = mt_rand(0, 35);
					$j = mt_rand(0, 35);

				$motaleatoire =  $a.$tablettre[$f].$b.$tablettre[$g].$c.$tablettre[$h].$d.$tablettre[$i].$e.$tablettre[$j];
				
				mysql_query("UPDATE moderateurs SET passe='" . $motaleatoire . "' WHERE id='" . intval($_GET['modifierPassModo']) . "'");
				avert('Le pass de modérateur du modérateur est actualisé');
			}
		// Fin - Modifier Pass Modo
	
	// Debut - Ajouter une catégorie
	if(isset($_POST['addCat']))
	{
		mysql_query('INSERT INTO categories VALUE("", "' . secure($_POST['addCat']) . '")');
		error('La catégorie a bien été ajoutée !');
	}
	// Fin - Ajouter une catégorie
	
	// Debut - Supprimer une catégorie
	if(isset($_GET['deleteCat']) AND intval($_GET['deleteCat']))
	{
		mysql_query('DELETE FROM categories WHERE id=\'' . intval($_GET['deleteCat']) . '\'');
		mysql_query('DELETE FROM forum WHERE idCat=\'' . intval($_GET['deleteCat']) . '\'');
		mysql_query('DELETE FROM message WHERE idCat=\'' . intval($_GET['deleteCat']) . '\'');
		error('La catégorie a bien été supprimée.');
	}
	// Fin - Supprimer une catégorie
	
	// Début - Modifier une catégorie
	if(isset($_POST['editCat']) && !empty($_POST['editCat']))
	{
		$editCat = secure($_POST['editCat']);
		mysql_query('UPDATE categories SET nom=\'' . $editCat . '\' WHERE id=' . $_POST['idCat']);
		error('La catégorie a bien été modifiée.');
	}
	// Fin - Modifier une catégorie
	
	// Debut - Ajouter un forum
	if(isset($_POST['forumNom']) AND isset($_POST['forumDpt']))
	{
		$titre = secure($_POST['forumNom']);
		$dpt = secure($_POST['forumDpt']);
		$idCat = intval($_POST['forumCat']);
		$statut = secure($_POST['statut']);	
		mysql_query('INSERT INTO forum VALUE("", "' . $titre . '", "' . $dpt . '", "' . $idCat . '", "' . $statut . '", "' . time() . '")');
		error('Le forum a bien été ajouté.');
	}
	// Fin - Ajouter un forum
	
	// Début - Supprimer un forum & Déplacer Topic
	if(isset($_POST['deplacer']) AND $_POST['deplacer'] == 1)
	{
		mysql_query('UPDATE topic SET idForum=\'' . intval($_POST['forumDestinataire']) . '\' WHERE idForum=' . intval($_POST['forumSuppr']))or die(mysql_error());
		mysql_query('DELETE FROM forum WHERE id=\'' . intval($_POST['forumSuppr']) . '\'');
		error('Le forum a bien été supprimée et les topics déplacés dans le forum destinataire');
	}
	
	if(isset($_POST['deplacer']) AND $_POST['deplacer'] == 0)
	{
		mysql_query('DELETE FROM forum WHERE id=\'' . intval($_POST['forumSuppr']) . '\'');
		error('Le forum a bien été supprimée');
	}
	// Fin - Supprimer un forum & Déplacer Topic
	
	// Début - Modifier un forum
	if(isset($_POST['nomForum']))
	{
		$titreForum = secure($_POST['nomForum']);
		$modifierDpt = secure($_POST['descrForum']);
		$statut = secure($_POST['statut']);
		$forumCat = intval($_POST['forumCat']);
		$forumId = intval($_POST['forumId']);
		
		mysql_query('UPDATE forum SET titre=\'' . $titreForum . '\', dpt=\'' . $modifierDpt . '\', idCat=\'' . $forumCat . '\', statut=\'' . $statut . '\' WHERE id=' . $forumId . '');
		error('Le forum à bien été modifié');
	}
	// Fin - Modifier un forum
	
	// Debut - Bannir, Débannir, Tuer, etc..
	if(isset($_GET['bann']))
	{
		$reponse = mysql_query('SELECT * FROM membres WHERE pseudo=\'' . secure($_GET['bann']) . '\'');
		$donnees = mysql_fetch_assoc($reponse);
		mysql_query("UPDATE membres SET acces='1' WHERE pseudo='" . secure($_GET['bann']) . "'");
		mysql_query('INSERT INTO bann VALUES("", "' . intval($donnees['id']) . '", "' . intval($_GET['idMessage']) . '")');
		error(text_i($donnees['pseudo']) . ' a bien été banni.');
	}
	if(isset($_GET['debann']))
	{
		$reponse = mysql_query('SELECT * FROM membres WHERE pseudo=\'' . secure($_GET['debann']) . '\'');
		$donnees = mysql_fetch_assoc($reponse);
		mysql_query("UPDATE membres SET acces='10' WHERE pseudo='" . secure($_GET['debann']) . "'");
		mysql_query('DELETE FROM bann WHERE idPseudo=\'' . intval($donnees['id']) . '\'');
		error(text_i($donnees['pseudo']) . ' a bien été débanni.');
	}
	if(isset($_GET['bannIp']))
	{
		$reponse = mysql_query('SELECT * FROM membres WHERE ip=\'' . secure($_GET['bannIp']) . '\'');
		$donnees = mysql_fetch_assoc($reponse);
		mysql_query("UPDATE membres SET bannIp='1' WHERE ip='" . secure($_GET['bannIp']) . "'");
		mysql_query('INSERT INTO bannIp VALUES("", "' . secure($_GET['bannIp']) . '")');
		error(text_i($donnees['pseudo']) . ' a bien été banni ip.');
	}
	if(isset($_GET['debannIp']))
	{
		$reponse = mysql_query('SELECT * FROM membres WHERE ip=\'' . secure($_GET['debannIp']) . '\'');
		$donnees = mysql_fetch_assoc($reponse);
		mysql_query("UPDATE membres SET bannIp='0' WHERE ip='" . secure($_GET['debannIp']) . "'");
		mysql_query('DELETE FROM bannIp WHERE ip=\'' . secure($_GET['debannIp']) . '\'');
		error(text_i($donnees['pseudo']) . ' a bien été débanni ip.');
	}
	if(isset($_GET['suppr']))
	{
		$reponse = mysql_query('SELECT * FROM membres WHERE id=\'' . secure($_GET['suppr']) . '\'');
		$donnees = mysql_fetch_assoc($reponse);
		mysql_query('DELETE FROM membres WHERE id=' . secure($_GET['suppr']) . '');
		mysql_query('DELETE FROM bann WHERE idPseudo=' . secure($_GET['suppr']) . '');
		error(text_i($donnees['pseudo']) . ' a bien été supprimer.');
	}
	if(isset($_GET['tuer']))
	{
		$reponse = mysql_query('SELECT * FROM membres WHERE pseudo=\'' . secure($_GET['tuer']) . '\'');
		$donnees = mysql_fetch_assoc($reponse);
		mysql_query('DELETE FROM topic WHERE idPseudo=' . intval($donnees['id']) . '');
		mysql_query('DELETE FROM message WHERE idPseudo=' . intval($donnees['id']) . '');
		mysql_query('DELETE FROM commentaires WHERE pseudo=\'' . secure($_GET['tuer']) . '\'');
		error(text_i($donnees['pseudo']) . ' a bien été tuer.');
	}
	// Fin - Bannir, Débannir, Tuer, etc..
	
	// Début - Arme v5
	if(isset($_POST['deleteAll']))
	{
		foreach($_POST['deleteAll'] as $idMember)
		{
			if(isset($idMember) AND ctype_digit($idMember))
			{
				if(isset($_POST['actionM']))
				{
					$action = secure($_POST['actionM']);
					$reponse = mysql_query('SELECT * FROM membres WHERE id="' . secure($idMember) . '"');
					$donnees = mysql_fetch_assoc($reponse);
					$laVictime = $donnees['pseudo'];
					$ipVictime = $donnees['ip'];
					
					switch($action) 
					{
						case 1: // Bannir un Membre
						mysql_unbuffered_query('UPDATE membres SET acces="1" WHERE id="' . secure($idMember) . '"');
						mysql_unbuffered_query('INSERT INTO bann VALUES("", "' . secure($idMember) . '", "0")');
						error(text_i($laVictime) . ' a bien été banni.');
						break;
						
						case 10: // Débannir un Membre
						mysql_unbuffered_query('UPDATE membres SET acces="10" WHERE id="' . secure($idMember) . '"');
						mysql_unbuffered_query('DELETE FROM bann WHERE idPseudo="' . secure($idMember) . '"');
						error(text_i($laVictime) . ' a bien été débanni.');
						break;
						
						case 11: // Bannir un Membre IP
						mysql_unbuffered_query('UPDATE membres SET bannIp="1" WHERE id="' . secure($idMember) . '"');
						mysql_unbuffered_query('INSERT INTO bannIp VALUES("", "' . secure($ipVictime) . '")');
						error(text_i($laVictime) . ' a bien été banni IP.');
						break;
						
						case 12: // Débannir un Membre IP
						mysql_unbuffered_query('UPDATE membres SET bannIp="0" WHERE id="' . secure($idMember) . '"');
						mysql_unbuffered_query('DELETE FROM bannIp WHERE ip="' . secure($ipVictime) . '"');
						error(text_i($laVictime) . ' a bien été débanni IP.');
						break;
						
						case 20: // Supprimer un Membre
						mysql_unbuffered_query('DELETE FROM membres WHERE id="' . secure($idMember) . '"');
						mysql_unbuffered_query('DELETE FROM bann WHERE idPseudo="' . secure($idMember) . '"');
						error(text_i($laVictime) . ' a bien été supprimé.');
						break;
						
						case 21: // Tuer un Membre
						mysql_query('DELETE FROM mp WHERE expediteur="' . $laVictime . '"');
						mysql_query('DELETE FROM topic WHERE idPseudo="' . secure($idMember) . '"');
						mysql_query('DELETE FROM message WHERE idPseudo="' . secure($idMember) . '"');
						mysql_query('DELETE FROM commentaires WHERE pseudo="' . $laVictime . '"');
						error(text_i($laVictime) . ' a bien été tuer.');
						break;

						default: // Erreur
						error('Une erreur est survenue, veuillez recommencer.');
					}
				}
			}
		}
	}
	// Fin - Arme v5
	
	// Début - Rechercher un membre
	
		// Par pseudo
		if(isset($_POST['rechPseud']))
		{
			$reponse = mysql_query('SELECT pseudo, acces FROM membres WHERE pseudo="' . secure($_POST['rechPseud']) . '"');
			$calcul = mysql_query('SELECT COUNT(*) as `calcul` FROM membres WHERE pseudo="' . secure($_POST['rechPseud']) . '"');
			$resultat = mysql_fetch_assoc($calcul);
			
			if($resultat['calcul'] == 0)
			{
				echo'
				<div class="row">
				  <div class="span16">';
				error('Aucun résultat de recherche pour le pseudo ' . text_i($_POST['rechPseud']) . '<br /><br />
				<a class="btn info" href="panel.htm?gestionMembres"><img src="images/panel/gauche.png"> Retour</a>');
				echo'
				  </div>
				</div>
				';
				exit;
			}
			
			echo'
			<div class="row">
			  <div class="span16">
					' . error('<font color="green">' .text_i($resultat['calcul']) . '</font> pseudo trouvé') . '
					<a class="btn info" href="panel.htm?gestionMembres"><img src="images/panel/gauche.png"> Retour</a><br /><br />
					<h3>Résultat de recherche pour le pseudo ' . text_i($_POST['rechPseud']) . '</h3>';
					
					while($donnees = mysql_fetch_assoc($reponse))
					{
						echo'
						<div id="wait" style="height:200px;">
						  <p style="float:right; padding-right:15px;padding-top:4px;">
							<a href="#" OnClick="popupoff(\'wait\');" name="modal"><div class="popClose"><img  src="images/close_greybox.png" /></div></a>
						  </p><br />
						  <div class="popTitre"><img src="images/pop_fleche.png" /> <b>Information sur ' . ($COLP->afficher($donnees['acces'], $donnees['pseudo'])) . '</b></div>
							<div class="popTexte">
								lol
							</div>
						</div>
						<b><img src="images/panel/droite.png"> ' . ($COLP->afficher($donnees['acces'], $donnees['pseudo'])) . '</b>
						<a href="#" OnClick="popupon(\'wait\');" name="modal">Information</a>
						<br />';
					}
				echo'
			  </div>
			</div>
			';
			exit;
		}
		
		// Par password
		if(isset($_POST['rechPass']))
		{
			$reponse = mysql_query('SELECT pseudo, acces FROM membres WHERE passe="' . secure(md5($_POST['rechPass'])) . '"');
			$calcul = mysql_query('SELECT COUNT(*) as `calcul` FROM membres WHERE passe="' . secure(md5($_POST['rechPass'])) . '"');
			$resultat = mysql_fetch_assoc($calcul);
			
			if($resultat['calcul'] == 0)
			{
				echo'
				<div class="row">
				  <div class="span16">';
				error('Aucun pseudo n\'utilise le mot de passe ' . text_i($_POST['rechPass']) . '<br /><br />
				<a class="btn info" href="panel.htm?gestionMembres"><img src="images/panel/gauche.png"> Retour</a>');
				echo'
				  </div>
				</div>
				';
				exit;
			}
			
			echo'
			<div class="row">
			  <div class="span16">
				<h3>Résultat de recherche des pseudos utilisant le mot de passe ' . text_i($_POST['rechPass']) . '</h3>
					' . error('<font color="green">' .text_i($resultat['calcul']) . '</font> pseudo trouvé') . '<br /><br />';
					
					while($donnees = mysql_fetch_assoc($reponse))
					{
						echo'<b><img src="images/panel/droite.png"> ' . ($COLP->afficher($donnees['acces'], $donnees['pseudo'])) . '</b><br />';
					}
				echo'
			  </div>
			</div>
			';
			exit;
		}
		
		// Par ip
		
		// Par grade
		
	// Fin - Rechercher un membre
	
// Fin - Ajout, suppression, et edition

// Début - Affichage du corps de la page

	// Début - Page d'accueil
	if(isset($_GET['accueil']))
	{
		echo'
			<div class="hero-unit">
			  <h1>Bienvenue dans l\'administration</h1>
			  <p>Vous pouvez ici gérer le contenu de TechHub.</p>
			</div>

			<div class="row">
			  <div class="span6">
				<h2>Gestions des forums</h2>
				<p>Section pour gérer les forums/catégories, ainsi que les configurations globals.</p>
				<p><a class="btn info" href="panel.htm?gestionForum">Go &raquo;</a></p>
			  </div>
			  
			  <div class="span5">
				<h2>Gestions des membres</h2>
				 <p>Section pour gérer les membres (Bannissement, etc..).</p>
				<p><a class="btn info" href="panel.htm?gestionMembres">Go &raquo;</a></p>
			 </div>
			  
			  <div class="span5">
				<h2>Gestion des smiley</h2>
				<p>Cette section est faite pour gérer les smiley
				(ajout,supression,edition).</p>
				<p><a class="btn info" href="panel.htm?gestionSmiley">Go &raquo;</a></p>
			  </div>

			<div class="row">
			  <div class="span6">
				<h2>Espace Rédaction</h2>
				<p>Cette section est faite pour gérer les news, les patchs et les wallpaper(création,supression,edition).</p>
				<p><a class="btn info" href="panel.htm?espaceRedaction">Go &raquo;</a></p>
			  </div></div>';
	}
	// Fin - Page d'accueil

	// Début - Gestion des forums & catégories
	
		// Début - Modification
		if(isset($_GET['editCat']))
		{
			$reponse = mysql_query('SELECT nom FROM categories WHERE id = ' . intval($_GET['editCat']));
			$donnees = mysql_fetch_assoc($reponse);
			
			echo '
			<div class="row">
			  <div class="span16">
				  <h3>Modifier une catégorie</h3>
					<form method="post" action="panel.htm?gestionForum" class="form-stacked">
						<input type="hidden" name="idCat" value="' . $_GET['editCat'] . '" />
						<fieldset>
						<div class="clearfix">
							<label for="editCat">Nom de la Catégorie</label>
							<div class="input">
								<input class="xlarge" id="editCat" name="editCat" size="30" type="text" value="' . text_i($donnees['nom']) . '">
							</div>
						</div><br />
						<button type="submit" class="btn success">Sauvegarder</button>
					  </fieldset>
					</form>
					<br />
					<a href="panel.htm?gestionForum">Revenir à la gestion des forums</a>
				</div>
			</div>
			';
			echo PIED;
			exit;
		}
		
		if(isset($_GET['editForum']))
		{
			$reponse = mysql_query('SELECT titre, dpt, idCat FROM forum WHERE id = ' . intval($_GET['editForum']));
			$donnees = mysql_fetch_assoc($reponse);
			
			echo '
			<div class="row">
			  <div class="span16">
				  <h3>Modifier un forum</h3>
					<form method="post" action="panel.htm?gestionForum" class="form-stacked">
					<fieldset>
					<div class="clearfix">
							<label for="xlInput3">Nom du Forum</label>
							<div class="input">
								<input class="xlarge" id="nomForum" name="nomForum" size="30" type="text" value="' . text_i($donnees['titre']) . '">
							</div>
						</div>
						<div class="clearfix">
							<label for="xlInput3">Description du Forum</label>
							<div class="input">
								<input class="xlarge" id="descrForum" name="descrForum" size="30" type="text" value="' . text_i($donnees['dpt']) . '">
							</div>
						</div>
						<div class="clearfix">
							<label for="xlInput3">Catégorie du Forum</label>
							<div class="input">
								<select name="forumCat">';
								$requete = mysql_query('SELECT * FROM categories ORDER BY id ASC');
								
								while($info = mysql_fetch_assoc($requete))
								{
									echo '
									<option value="' . intval($info['id']) . '">' . text_i($info['nom']) . '</option>
									';
								}
								echo '
								</select>
							</div>
						</div>
						<div class="clearfix">
							<label for="xlInput3">Droit d\'accès</label>
							<div class="input">
								<select name="statut">
									<option value="0">Tous le monde</option>
									<option value="1">Modos/admins</option>
								</select>
							</div>
						</div>
						<input type="hidden" name="forumId" id="forumId" value="' . intval($_GET['editForum']) . '" /><br />
						<button type="submit" class="btn success">Sauvegarder</button>
						</fieldset>
					</form>
					<br />
					<a href="panel.htm?gestionForum">Revenir à la gestion des forums</a>
				</div>
			</div>
			';
			echo PIED;
			exit;
		}
		// Fin - Modification
		
		// Début - Ajout
		if(isset($_GET['addCat']))
		{
			echo '
			<div class="row">
			  <div class="span16">
				  <h3>Ajouter une catégorie</h3>
					<form method="post" action="panel.htm?gestionForum" class="form-stacked">
					  <fieldset>
						<div class="clearfix">
							<label for="addCat">Nom de la Catégorie</label>
							  <div class="input">
								<input class="xlarge" id="addCat" name="addCat" size="30" type="text"">
							  </div>
						</div><br />
						<button type="submit" class="btn success">Ajouter</button>&nbsp;<button type="reset" class="btn">Vider</button>
					  </fieldset>
					</form>
					<br />
					<a href="panel.htm?gestionForum">Revenir à la gestion des forums</a>
				</div>
			</div>
			';
			echo PIED;
			exit;
		}
		
		if(isset($_GET['addForum']))
		{
			echo '
			<div class="row">
			  <div class="span16">
				  <h3>Ajouter une catégorie</h3>
					<form method="post" action="panel.htm?gestionForum" class="form-stacked">
					  <fieldset>
						<div class="clearfix">
							<label for="addCat">Nom du Forum</label>
							  <div class="input">
								<input class="xlarge" id="forumNom" name="forumNom" size="30" type="text"">
							  </div>
						</div>
						<div class="clearfix">
							<label for="addCat">Description du Forum</label>
							  <div class="input">
								<input class="xlarge" id="forumDpt" name="forumDpt" size="30" type="text"">
							  </div>
						</div>
						<div class="clearfix">
							<label for="addCat">Catégorie du Forum</label>
							  <div class="input">
								<select name="forumCat">';
								$reponse = mysql_query('SELECT * FROM categories ORDER BY id ASC');
							
								while($donnees = mysql_fetch_assoc($reponse))
								{
									echo '
									<option value="' . intval($donnees['id']) . '">' . text_i($donnees['nom']) . '</option>
									';
								}
								echo'</select>
							  </div>
						</div>
						<div class="clearfix">
							<label for="addCat">Droit d\'accès du Forum</label>
							  <div class="input">
								<select name="statut">
									<option value="0">Tous le monde</option>
									<option value="1">Modos/admins</option>
								</select>
							  </div>
						</div>
						<br />
						<button type="submit" class="btn success">Ajouter</button>&nbsp;<button type="reset" class="btn">Vider</button>
					  </fieldset>
					</form>
					<br />
					<a href="panel.htm?gestionForum">Revenir à la gestion des forums</a>
				</div>
			</div>
			';
			echo PIED;
			exit;
		}
		// Fin - Ajout
		
		// Début - Supprimer Forum & Déplacer Topic
		if(isset($_GET['deplacerTopic']) AND intval($_GET['deplacerTopic']))
		{
			echo'
			<div class="row">
			  <div class="span16">
				  <h3>Supprimer le forum ' . text_i($_GET['titre']) . '</h3>
					<form method="post" action="panel.htm?gestionForum" class="form-stacked">
					  <fieldset>
						<div class="clearfix">
							<label for="deplacerTopic">Déplacer les topics dans un autres forum :</label>
								Non : <input type="radio" name="deplacer" value="0"/> Oui : <input type="radio" name="deplacer" value="1"/><br />
								<select name="forumDestinataire">
								';
								$requete = mysql_query('SELECT * FROM forum ORDER BY titre ASC');
								
								while($info = mysql_fetch_assoc($requete))
								{
									echo '
									<option value="' . intval($info['id']) . '">' . text_i($info['titre']) . '</option>															';
								}
								echo '
								</select>
						</div>
						<input type="hidden" name="forumSuppr" id="forumSuppr" value="' . intval($_GET['deplacerTopic']) . '" />
						<button type="submit" class="btn success">Valider</button>
					  </fieldset>
					</form>
					<br />
					<a href="panel.htm?gestionForum">Revenir à la gestion des forums</a>
				</div>
			</div>';
			echo PIED;
			exit;
		}
		// Fin - Supprimer Forum & Déplacer Topic
		
	if(isset($_GET['gestionForum']))
	{
		echo'
			<div class="row">
			  <div class="span16">
				  <h3>Gestion des catégories</h3>
				<table class="bordered-table">
			<thead>
			  <tr>
				<th>Id</th>
				<th>Nom de la catégorie</th>
				<th>Action</th>
			  </tr>
			</thead>
			<tbody>';
			$reponse = mysql_query('SELECT * FROM categories ORDER BY id ASC');
			
			while($donnees = mysql_fetch_assoc($reponse))
			{
			 echo'
			  <tr>
				<th>' . intval($donnees['id']) . '</th>
				<td>' . text_i($donnees['nom']) . '</td>
				<td><a href="panel.htm?gestionForum&amp;deleteCat=' . intval($donnees['id']) . '" onclick="confirmation = confirm(\'Voulez-vous vraiment supprimer cette catégorie ?\'); if(confirmation == false) { return false; }"><img src="images/del.png"></a> | <a href="panel.htm?editCat=' . intval($donnees['id']) . '"><img src="images/edit.png"></a></td>
			  </tr>
			 ';
			}
			echo'
			</tbody>
		  </table>
		  
		  <a class="btn success large" href="panel.htm?addCat">Ajouter une catégorie</a>
		  <hr>
			  </div>
		  </div>
		   <div class="row">
			  <div class="span16">
				<h3>Gestion des forum</h3>
				<table class="bordered-table">
			<thead>
			  <tr>
				<th>Id</th>
				<th>Nom du forum</th>
				<th>Nom de la catégorie</th>
				<th>Droit d\'accès</th>
				<th>Lien du forum</th>
				<th>Action</th>
			  </tr>
			</thead>
			<tbody>';
			$reponse = mysql_query('SELECT forum.titre AS `titreDuForum`, forum.id AS `idDuForum`, forum.idCat AS `idCatDuForum`, forum.statut AS `statut`, 
			categories.nom AS `titreCategorie`, categories.id FROM forum INNER JOIN categories ON forum.idCat = categories.id ORDER BY idDuForum ASC');
			
			while($donnees = mysql_fetch_assoc($reponse))
			{
			 echo'
			  <tr>
				<th>' . $donnees['idDuForum'] . '</th>
				<td>' . text_i($donnees['titreDuForum']) . '</td>
				<td>' . text_i($donnees['titreCategorie']) . '</td>
				<td>';	if($donnees['statut'] == 0) { echo 'Membres'; } elseif($donnees['statut'] == 1) { echo 'Admins/modos'; } else { echo 'Nb'; } echo'</td>
				<td><a class="btn info" href="' . have_url($donnees['idDuForum'], 1 , FORUM_URL, $donnees['titreDuForum']) . '" target="_bank"">Go &raquo;</a></td>
				<td><a href="panel.htm?gestionForum&amp;deplacerTopic=' . intval($donnees['idDuForum']) . '" onclick="confirmation = confirm(\'Voulez-vous vraiment supprimer ce forum ?\'); if(confirmation == false) { return false; }"><img src="images/del.png"></a> | <a href="panel.htm?editForum=' . intval($donnees['idDuForum']) . '"><img src="images/edit.png"></a></td>
			  </tr>';
			}
			echo'
			</tbody>
		  </table>
		  
		  <a class="btn success large"href="panel.htm?addForum">Ajouter un forum</a>
		  <hr>
			  </div>
		  </div>
		 <div class="row">   
		<div class="span16">
		  <form method="post" action="panel.htm?gestionForum" class="form-stacked">
			<fieldset>
			  <legend>Modification des forums</legend>
			  <div class="clearfix">
				<label for="nbTopicParPage">Nombre de topics par pages</label>
				<div class="input">
				  <input class="xlarge" id="nbTopicParPage" name="nbTopicParPage" size="30" type="text" value="' . $config['forum']['nbTopicParPage'] . '">
				</div>
			  </div>
			  <div class="clearfix">
				<label for="nbMessageParPage">Nombre de messages par pages</label>
				<div class="input">
				  <input class="xlarge" id="nbMessageParPage" name="nbMessageParPage" size="30" type="text" value="' . $config['forum']['nbMessageParPage'] . '">
				</div>
			  </div>
			  <div class="clearfix">
				<label for="tempsChaqueTopic">Temps entre chaque création de topics</label>
				<div class="input">
				  <input class="xlarge" id="tempsChaqueTopic" name="tempsChaqueTopic" size="30" type="text" value="' . $config['forum']['tempsEntreChaqueTopic'] . '">
				</div>
			  </div>
			  <div class="clearfix">
				<label for="tempsChaqueMessage">Temps entre chaque messages</label>
				<div class="input">
				  <input class="xlarge" id="tempsChaqueMessage" name="tempsChaqueMessage" size="30" type="text" value="' . $config['forum']['tempsEntreChaqueMessage'] . '">
				</div>
			  </div>
			  <div class="clearfix">
				<label for="editer">Temps avant que la fonction "editer" disparaisse :</label>
				<div class="input">
				  <input class="xlarge" id="editer" name="editer" size="30" type="text" value="' . $config['forum']['tempsFonctionEditer'] . '">
				</div>
			  </div>
		  <div class="clearfix">
				<label for="nbsmiley">Nombre de smiley par messages :</label>
				<div class="input">
				  <input class="xlarge" id="nbsmiley" name="nbsmiley" size="30" type="text" value="' . $config['forum']['nbsmiley'] . '">
				</div>
			  </div>
			</fieldset>
			  <button type="submit" class="btn success">Sauvegarder</button>
		  </form>
		</div>
	  </div>
	  ';
	}
	// Fin - Gestion des forums & catégories
	
	// Début - Gestion des membres
	if(isset($_GET['gestionMembres']))
	{
		$pseudoParPage = 20;
		$retour_total = mysql_query('SELECT COUNT(*) AS total FROM membres');
		$donnees_total = mysql_fetch_assoc($retour_total);
		$total = $donnees_total['total'];
		
		$nombreDePages = ceil($total/$pseudoParPage);
		
		if(isset($_GET['pageMembres']))
		{
			$pageActuelle = intval($_GET['pageMembres']);
			if($pageActuelle > $nombreDePages)
			{
				$pageActuelle = $nombreDePages;
			}
		}
		else
		{
		    $pageActuelle = 1;
		}
		
		$premiereEntree = ($pageActuelle - 1) * $pseudoParPage;
		
		if(isset($_GET['triMembres']))
		{
			$valeurTriMembres = htmlspecialchars($_GET['triMembres']);
		}
		else
		{
			$valeurTriMembres = 'id';
		}
		
		if(isset($_GET['pageMembres']))
		{
			$pageMembres = intval($_GET['pageMembres']);
		}
		else
		{
			$pageMembres = 1;
		}
		
		if(isset($_GET['ordreTri']) AND $_GET['ordreTri'] == 'normal')
		{
			$ordreTri = '';
			$lienOrdre = 'normal';
		}
		else
		{
			$ordreTri = 'DESC ';
			$lienOrdre = 'inverser';
		}
		
		$reponse = mysql_query('SELECT * FROM membres ORDER BY ' . secure($valeurTriMembres) . ' 
		' . secure($ordreTri) . 'LIMIT ' . $premiereEntree . ', ' . $pseudoParPage)or die(mysql_error());
		
	  echo'
        <div class="row">
          <div class="span16">
            <h3>Gestion des membres</h3>
				<div class="pagination">
				 <ul>';
					if($pageActuelle != 1)
					{
						 echo'
							<li class="next"><a href="panel.htm?gestionMembres&amp;pageMembres=' . ($pageActuelle - 1) . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '">&larr; Precédent</a></li>
						';
					}
					else
					{
						 echo'
							 <li class="next"><a href="panel.htm?gestionMembres&amp;pageMembres=' . $pageActuelle . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '">&larr; Precédent</a></li>
						';
					}
					for($i=1; $i <= $nombreDePages; $i++)
					{
						echo'<a href="panel.htm?gestionMembres&amp;pageMembres=' . $i . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '">' . $i . '</a>';
					}
					if($pageActuelle > $nombreDePage)
					{
						 echo'
							<li class="next"><a href="panel.htm?gestionMembres&amp;pageMembres=' . ($pageActuelle + 1) . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '">Suivant &rarr;</a></li>
						';
					}
					else
					{
						 echo'
							 <li class="next"><a href="panel.htm?gestionMembres&amp;pageMembres=' . $pageActuelle . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '">Suivant &rarr;</a></li>
						';
					}
					echo'
				 </ul>
				</div>
				<div class="pagination">
				<ul>
				<li><a href="panel.htm?gestionMembres&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=normal">Croissant</a></li>
				<li><a href="panel.htm?gestionMembres&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=inverser">Décroissant</a></li>
				</ul>
				</div>
	  <form method="post" id="checkpanel">
       <table class="bordered-table">
        <thead>
          <tr>
            <th>Id</th>
			<th></th>
			<th>Ip</th>
			<th>Pseudo</th>
			<th>Argent</th>
			<th>Grade</th>
			<th>Email</th>
			<th>Sexe</th>
			<th>Pays</th>
			<th>Messages</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>';
			while($donnees = mysql_fetch_assoc($reponse))
			{
				$idDuPseudo = text_i($donnees['id']);
				$pseudo = text_i($donnees['pseudo']);
				$argent = text_i($donnees['argent']);
				$email = text_i($donnees['email']);
				
				if(!empty($donnees['ip']))
				{
					$ipDuMembre = text_i($donnees['ip']);
				}
				else
				{
					$ipDuMembre = 'Non localisé';
				}
				
				if(!empty($donnees['pays']))
				{
					$pays = text_i($donnees['pays']);
				}
				else
				{
					$pays = 'Aucun';
				}
				
				if($donnees['sexe'] == 'm')
				{
					$sexe = '<img src="images/sexeM.gif" alt="Masculin" title="Masculin">';
				}
				elseif($donnees['sexe'] == 'f')
				{
					$sexe = '<img src="images/sexeF.gif" alt="Féminin" title="Féminin">';
				}
				else
				{
					$sexe = 'Aucun';
				}
				
				$aysql = mysql_query('SELECT COUNT(*) FROM message WHERE idPseudo = ' . $idDuPseudo);
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
				
				$ss = '';
				if($posts > 1)
				{
					$ss = 's';
				}
				
				if($donnees['acces'] == 100) // Admin
				{
					$grade = 'Administrateur';
				}
				elseif($donnees['acces'] == 95) // Directeur
				{
					$grade = 'Directeur';
				}
				elseif($donnees['acces'] == 90) // Super modo
				{
					$grade = 'Super modo';
				}
				elseif($donnees['acces'] == 50) // Modérateur
				{
					$grade = 'Mod&eacute;rateur';
				}
				elseif($donnees['acces'] == 45) // Pubbeur
				{
					$grade = 'Pubbeur';
				}
				elseif($donnees['acces'] == 98) // Codeur
				{
					$grade = 'Codeur';
				}
				elseif($donnees['acces'] == 1) // Bann
				{
					$grade = '<font color="red">Banni</font>';
				}
				else // Membre
				{
					$grade = 'Membre';
				}
				
				if($donnees['bannIp'] == 1) // Bann Ip
				{
					$ipOuPas = '<font color="purple">Banni Ip</font>';
				}
				else
				{
					$ipOuPas = '';
				}
				
				if($donnees['acces'] == 1) // Si Banni
				{
					$pseudal = '<s><strong>' . $pseudo . '</strong></s>';
				}
				else
				{
					$pseudal = ($COLP->afficher($donnees['acces'], $pseudo)); // Pseudo
				}
				$rang = ($COLP->afficher($donnees['acces'], $grade)); // Grade
				$posts = $posts . ' message' . $ss; // Message
				$msn = '<a href="panel.htm?gestionMembres&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" title="' . text_i($donnees['email']) . '" alt="' . text_i($donnees['email']) . '">
				' . mb_substr($email, 0, 13) . '[...]</a>'; // Email
				
				if($donnees['acces'] == 1) // Si Banni
				{
					$bann = '
					<a href="panel.htm?gestionMembres&amp;debann=' . $pseudo . '&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" alt="Débannir ' . $pseudo . '" title="Débannir ' . $pseudo . '" onclick="confirmation = confirm(\'Voulez-vous vraiment débannir ' . $pseudo . ' ? Il pourra à présent ré-utiliser ce pseudo pour se connecter.\'); if(confirmation == false) { return false; }"><img src="images/panel/debann.png"></a> 
					';
				}
				else
				{
					$bann = '
					<a href="panel.htm?gestionMembres&amp;bann=' . $pseudo . '&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" alt="Bannir ' . $pseudo . '" title="Bannir ' . $pseudo . '" onclick="confirmation = confirm(\'Voulez-vous vraiment bannir ' . $pseudo . ' ? Il ne pourra plus utiliser ce pseudo pour se connecter.\'); if(confirmation == false) { return false; }"><img src="images/panel/bann.png"></a> 
					';
				}
				
				if($donnees['bannIp'] == 1)
				{
					$btnIp = '<a href="panel.htm?gestionMembres&amp;debannIp=' . $ipDuMembre . '&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" alt="Débannir ip ' . $pseudo . '" title="Débannir ip ' . $pseudo . '" onclick="confirmation = confirm(\'Voulez-vous vraiment débannir ip ' . $pseudo . ' ? Il pourra à présent ré-acceder au site.\'); if(confirmation == false) { return false; }"><img src="images/panel/debannip.png"></a>';
				}
				else
				{
					$btnIp = '<a href="panel.htm?gestionMembres&amp;bannIp=' . $ipDuMembre . '&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" alt="Bannir ip ' . $pseudo . '" title="Bannir ip ' . $pseudo . '" onclick="confirmation = confirm(\'Voulez-vous vraiment bannir ip ' . $pseudo . ' ? Il ne pourra plus du tout accéder au site.\'); if(confirmation == false) { return false; }"><img src="images/panel/bannip.png"></a>';
				}
				
				$tuer = '<a href="panel.htm?gestionMembres&amp;tuer=' . $pseudo . '&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" alt="Tuer ' . $pseudo . '" title="Tuer ' . $pseudo . '" onclick="confirmation = confirm(\'Voulez-vous vraiment tuer ' . $pseudo . ' ? Tous les messages de ' . $pseudo . ' postés sur les news, forums, et topics seront supprimés. Utiliser cette fonction uniquement sur un Floodeur !\'); if(confirmation == false) { return false; }"><img src="images/panel/tuer.png"></a>';
				$suppr = '<a href="panel.htm?gestionMembres&amp;suppr=' . $idDuPseudo . '&amp;pageMembres=' . $pageMembres . '&amp;triMembres=' . $valeurTriMembres . '&amp;ordreTri=' . $lienOrdre . '" alt="Supprimer ' . $pseudo . '" title="Supprimer ' . $pseudo . '" onclick="confirmation = confirm(\'Voulez-vous vraiment supprimer ' . $pseudo . ' ? Il sera définitivement effacer de la base de donnée.\'); if(confirmation == false) { return false; }"><img src="images/del.png"></a>';
				
				echo'
				<tr>
				<th>' . $idDuPseudo . '</th>
				<td><input type="checkbox" name="deleteAll[]" value="' . $idDuPseudo . '" style="margin: 0px" /></td>';
				$armev5 = 1;
				echo'
				<td><a href="recherche.php?recherche=' . $ipDuMembre . '&listRecherche=IP" title="' . $ipDuMembre . '" target="_blank"><img src="images/ico.png"></a></td>
				<td><b>' . $pseudal . '</b></td>
				<td>' . $argent . '</td>
				<td><b>' . $rang . ' ' . $ipOuPas . '</b></td>
				<td>' . $msn . '</td>
				<td><center>' . $sexe . '</center></td>
				<td>' . $pays . '</td>
				<td>' . $posts . '</td>
				<td>' . $bann . ' ' . $tuer . ' ' . $btnIp . ' ' . $suppr . '</td>
				</tr>
				';
			}
			if(isset($armev5))
			{
				echo'
				 <div class="onCheckSelect">
					<select name="actionM">
						<option value="1">Bannir</option>
						<option value="10">Débannir</option>
						<option value="11">Bannir IP</option>
						<option value="12">Débannir IP</option>
						<option value="20">Supprimer</option>
						<option value="21">Tuer</option>
					</select>
					<input class="btn info" type="submit" value="Validez" />
				 </div>
				<div class="onCheckPanel">
				  <ul>
					<li><a href="Javascript:void(0)" onclick="Check_all(\'checkpanel\', true); return false;">Tout cocher</a></li>
					<li><a href="Javascript:void(0)" onclick="Check_all(\'checkpanel\', false); return false;">Tout décocher</a></li>
				  </ul>
				 </div>
				';
			}
          echo'
        </tbody>
      </table>
	 </form>
	 <hr>
          </div>
      </div>';
	  
	  echo'
        <div class="row">
          <div class="span16">
            <h3>La recherche par message est encore en construction.</h3>
		  </div>
		</div>
	  ';
	  
	  
	  echo'
		 <div class="row">   
		<div class="span16">
		  <div class="form-stacked">
			<fieldset>
			  <legend>Rechercher un membre</legend>
			  <div class="clearfix">
				<label for="xlInput3">Par pseudo :</label>
				<div class="input">
				 <form method="post">
				  <input class="xlarge" id="rechPseud" name="rechPseud" size="30" type="text"> <button type="submit" class="btn primary">Rechercher</button>
				 </form>
				</div>
			  </div>
			  <div class="clearfix">
				<label for="xlInput3">Par password :</label>
				<div class="input">
				 <form method="post">
				  <input class="xlarge" id="rechPass" name="rechPass" size="30" type="text"> <button type="submit" class="btn primary">Rechercher</button>
				 </form>
				</div>
			  </div>
			  <div class="clearfix">
				<label for="xlInput3">Par ip :</label>
				<div class="input">
				 <form method="post">
				  <input class="xlarge" id="rechIp" name="rechIp" size="30" type="text"> <button type="submit" class="btn primary">Rechercher</button>
				 </form>
				</div>
			  </div>
		  <div class="clearfix">
				<label for="xlInput3">Par grade :</label>
				<div class="input">
				 <form method="post">
				  	<select name="rechGrade" class="xlarge">
						<option value="directeur">Directeur</option>
						<option value="admin">Administrateur</option>
						<option value="supermodo">Super Modo</option>
						<option value="codeur">Codeur</option>
						<option value="modo">Modérateur</option>
						<option value="newseur">Newseur</option>
					</select> <button type="submit" class="btn primary">Rechercher</button>
				 </form>
				</div>
			  </div>
			</fieldset>
		  </div>
		</div>
	  </div>'; 
	}
	// Fin - Gestion des Membres
	// Début- Gestion des Smileys
	if(isset($_GET['gestionSmiley']))
	{
	if(isset($_GET['supprimer']))
			{
				
				if(ctype_digit($_GET['supprimer']))
				{
					$id = intval($_GET['supprimer']);
					$mysql = mysql_query('SELECT lien FROM smileys WHERE id = ' . $id);
					$lien = mysql_result($mysql, 0);
					
					if(!empty($lien))
					{
						mysql_query('DELETE FROM smileys WHERE id = ' . $id) or die('Non reussi' . include('pied.php')); // Etape 1 : BDD
						unlink('smileys/' . $lien . '.gif'); // Etape 2 : Image dans le FTP
						avert('Suppression rÃ©ussie.');
					}
					else
					{
						avert('Le smiley n\'existe pas.');
					}
					
				}
				else
				{
					avert('L\'id de suppression est incorrect.');
				}
				
			}
			
			if(isset($_POST['lien']) AND isset($_POST['smiley']))
			{
				echo'<br />';
				$lien = $_POST['lien'];
				$smiley = secure($_POST['smiley']);
				
				$contenu = file_get_contents($lien);
				
				if(!empty($contenu))
				{
					if(preg_match('#\.gif$#i', $lien))
					{
						$q = mysql_query('SELECT COUNT(*) FROM smileys WHERE code = "' . $smiley. '"');
						$res = mysql_result($q, 0);
						if($res == 0)
						{
						
							$lnk = substr(md5(uniqid()), 0, 15);
							
							$fichier = fopen('smileys/' . $lnk. '.gif', 'w+');
							fwrite($fichier, $contenu);
							fclose($fichier);
							
							avert('Ajout rÃ©ussi du smiley "' . $smiley . '" dans le dossier smileys/');
							
							mysql_query('INSERT INTO smileys VALUES("", "' . $smiley . '", "' . $lnk . '")');
							
							
							avert('Ajout rÃ©ussi dans la bdd : <a href="smileys/' . $lnk . '.gif">Cliquez ici</a>');
						}
						else
						{
							avert('Le code smiley est dÃ©jÃ  utilisÃ©.');
						}
					}
					else
					{
						avert('L\'image n\'est pas au format gif.');
					}
				}
				else
				{
					avert('Le contenu est vide');
				}
				
			}
			echo'	<div class="row">
			  <div class="span16">
			  <b>Ajouter un Smiley :</b><br />
			  <font color="red">Attention pour que le smiley soit ajouté, il doit être au format .GIF ! </font><br />
<form action="panel.php?gestionSmiley" method="post">
						<label for="lien">Lien du smiley :</label> <input type="text" name="lien" value="" /><br />
						<label for="smiley">Code smiley :</label> <input type="text" name="smiley" value="" /><br >
						<input type="submit" name="Valider" />
					</form>
					<b>Smileys déjà  ajoutés :</b>
			<table class="bordered-table">
			<thead>
					<tr>
						<th><img src="message/supprimer.gif" alt="X"/></th>
						<th>Smiley</th>
						<th>Code</th>
					</tr>
					';
					
					$requete = mysql_query('SELECT id, lien, code FROM smileys');
					
					while($ligne = mysql_fetch_array($requete))
					{					
						echo'<tr>
						<td><a href="panel.php?gestionSmiley&amp;supprimer=' . $ligne['id'] . '"><img src="message/supprimer.gif" alt="X"/></a></td>
						<td><img src="smileys/' . $ligne['lien'] . '.gif" /></td>
						<td><b>' . $ligne['code'] . '</b></td>
						</tr>';
					}
					echo'</thead>
			<tbody>
					</div></div></tbody>
		  </table>';
	}
	// Fin - Gestion des Smileys
	
	// Début - Espace Rédaction
	// Debut - Charte
	// Debut - modifier
	if(isset($_POST['charte']) && !empty($_POST['charte']))
	 {
		$charte = $_POST['charte'];
		file_put_contents('txt/charte.txt',$charte);
		avert('La charte a bien &eacute;t&eacute; modifi&eacute;');
	 }
	// Fin - modifier
// Fin - Charte

// Debut - FAQ
	// Debut - modifier
	if(isset($_POST['faq']) && !empty($_POST['faq']))
	 {
		$faq = $_POST['faq'];
		file_put_contents('txt/faq.txt',$faq);
		avert('La FAQ a bien &eacute;t&eacute; modifi&eacute;');
	 }
	// Fin - modifier
// Fin - FAQ
	if(isset($_GET['espaceRedaction']))
	{echo'<div class="row">
			  <div class="span16"><span style="float : right"> <h2> La FAQ du site </h2>';
	$faq = file_get_contents('txt/faq.txt');
			echo'
			<form method="post">
			<label>Rédigez ici la FAQ :</label><br />
			<textarea name="faq" id="faq" rows="11" cols="61">' .$faq. '</textarea><br />
			<input type="submit" />
			</form></span>
			  <h2> La charte du site </h2>';
			  $charte = file_get_contents('txt/charte.txt');
			echo'
			<form method="post">
			<label>Rédigez ici la charte :</label><br />
			<textarea name="charte" id="charte" rows="11" cols="61">' .$charte. '</textarea><br />
			<input type="submit" />
			</form>
			
			 ';
	echo'</div></div>';}
	// Fin - Espace Rédaction
	//Add Admin
	if(isset($_GET['gestionStaff']))
	{echo'<div class="row">
			  <div class="span16">
			  
			
				<h3 class="h3Normal">Nommer un Administrateur</h3>
				
					<table style="width: 722px; text-align: center;">
						<tr style="height: 33px; color: rgb(255, 255, 255); font-weight: bold;" class="tete">
							<th>Pseudo</th>
							<th>Pass admin</th>
							<th style="width: 80px;">Action</th>
						</tr>
					';
						
					$reponse = mysql_query('
					SELECT 
					membres.id AS `idDuPseudo`, 
					membres.pseudo, 
					admin.passe,
					admin.id AS `idAdmin`
					FROM membres 
					INNER JOIN admin 
					ON admin.idPseudo = membres.id 
					WHERE acces=\'100\'');
					
					while($donnees = mysql_fetch_assoc($reponse))
					{
						echo '
						<tr>
							<td class="tdGris">
							' . stripslashes(htmlspecialchars($donnees['pseudo'])) . ' <a href="cdv-' . intval($donnees['idDuPseudo']) . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/imgs/user.png" /></a>
							</td> 
							
							<td>
							' . stripslashes(htmlspecialchars($donnees['passe'])) .''; if($donnees['idDuPseudo'] != 1) { echo '<a href="panel.php?gestionStaff&amp;modifierPassAdmin=' . intval($donnees['idAdmin']) . '&amp;sid=' . $tokenAdmin . '" onclick="confirmation = confirm(\'Voulez-vous vraiment modifier le pass de modération de cet admin ?\'); if(confirmation == false) { return false; }"><img src="images/edit.png" /></a>'; } echo '
							</td>
							
							<td class="tdGris">
							<a href="panel.php?gestionStaff&amp;supprimerAdmin=' . intval($donnees['idDuPseudo']) . '&amp;idAdmin=' . intval($donnees['idAdmin']) . '&amp;sid=' . $tokenAdmin . '"><img src="images/del.png" /></a>
							</td>
						</tr>
						';
					}
						
					echo '
					</table>
					<br />
					<form method="post">
						<select name="admin">
							';
							$reponse = mysql_query('SELECT id, pseudo, passe FROM membres ORDER BY pseudo ASC');
							while($donnees = mysql_fetch_assoc($reponse))
							{
								echo '
								<option value="' . intval($donnees['id']) . '">' . stripslashes(htmlspecialchars($donnees['pseudo'])) . '</option>
								';
							}
							echo '
						</select>
						<input type="hidden" name="tokenAdmin" id="tokenAdmin" value="' . $tokenAdmin . '"/>
						<input type="submit" value="Valider" />
					</form>
				
			';
		
	// Fin - Nomination des Admins
	
			// Debut - Editer modérateur
			
					$reponse = mysql_query('SELECT * FROM forum');
					$donnees = mysql_fetch_assoc($reponse);
					
					echo '
					
						<h3 class="h3FondGris">Modifier Modérateur</h3>
						
							<form method="post" action="#">
								<b>Forum modéré :</b>
								<select name="forumModo">
								';
								$requete = mysql_query('SELECT * FROM forum ORDER BY id ASC');
							
								while($info = mysql_fetch_assoc($requete))
								{
									echo '
									<option value="' . intval($info['id']) . '">' . stripslashes(htmlspecialchars($info['titre'])) . '</option>															';
								}
								echo '
								</select>
								<input type="hidden" name="tokenAdmin" id="tokenAdmin" value="' . $tokenAdmin . '"/>
								<input type="submit" value="Valider" />
							</form>
							<br />
							<a href="panel.php">Revenir à la gestion des forums</a>
				
				
					';
					
					
			
			// Fin - Editer modérateur
	
	// Debut - Bloc nomination des Modérateurs
		
			echo '
			
				<h3 class="h3Normal">Nommer un Modérateur</h3>
				
					<table style="width: 722px; text-align: center;">
						<tr style="height: 33px; color: rgb(255, 255, 255); font-weight: bold;" class="tete">
							<th>Pseudo</th>
							<th>Pass mod\'</th>
							<th>Forum</th>
							<th style="width: 80px;">Action</th>
						</tr>
					';
					
					$reponse = mysql_query('
					SELECT moderateurs.id AS `idModo`,
					moderateurs.idPseudo AS `idDuPseudo`, 
					moderateurs.idForum, 
					moderateurs.passe, 
					membres.pseudo, 
					forum.titre AS `titreDuForum`, 
					forum.id AS `idDuForum`
					FROM moderateurs 
					LEFT JOIN membres 
					ON moderateurs.idPseudo = membres.id 
					LEFT JOIN forum 
					ON forum.id = moderateurs.idForum') or die(mysql_error());
					
					while($donnees = mysql_fetch_assoc($reponse))
					{
						echo '
						<tr>
							<td>
							' . stripslashes(htmlspecialchars($donnees['pseudo'])) . ' <a href="cdv-' . intval($donnees['idDuPseudo']) . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/imgs/user.png" /></a>
							</td>
							
							<td class="tdGris">
							' . stripslashes(htmlspecialchars($donnees['passe'])) . ' <a href="panel.php?gestionStaff&amp;modifierPassModo=' . intval($donnees['idModo']) . '&amp;sid=' . $tokenAdmin . '" onclick="confirmation = confirm(\'Voulez-vous vraiment actualisé le pass de modération de ce modérateur ?\'); if(confirmation == false) { return false; }"><img src="images/admin/actualiser.gif" /></a> 
							</td>
							
							<td>
							' . stripslashes(htmlspecialchars($donnees['titreDuForum'])) . ' <a href="forum-' . intval($donnees['idDuForum']) . '.html" target="__bank"><img src="images/admin/bt_aller_forum.png" style="width: 15px;"></a> 
							</td>
							
							<td class="tdGris">
							<a href="panel.php?gestionStaff&amp;modifierModo=' . intval($donnees['idModo']) . '&amp;sid=' . $tokenAdmin . '"><img src="images/editer.gif" /></a> 
							<a href="panel.php?gestionStaff&amp;supprimerModo=' . intval($donnees['idModo']) . '&amp;sid=' . $tokenAdmin . '"><img src="images/supprime.gif" /></a> 
							</td>
						</tr>
						';
					}
					
					echo '
					</table>
					<br />
					<form method="post">
						<label>Pseudo :</label>
						<select name="modo">
							';
							$reponse = mysql_query('SELECT id, pseudo FROM membres ORDER BY pseudo ASC');
							while($donnees = mysql_fetch_assoc($reponse))
							{
								echo '
								<option value="' . intval($donnees['id']) . '">' . stripslashes(htmlspecialchars($donnees['pseudo'])) . '</option>
								';
							}
							echo '
						</select>
						<br />
						<label>Forum : </label>
						<select name="modoForum">
							';
							$reponse = mysql_query('SELECT id, titre FROM forum ORDER BY id ASC');
							while($donnees = mysql_fetch_assoc($reponse))
							{
								echo '
								<option value="' . intval($donnees['id']) . '">' . stripslashes(htmlspecialchars($donnees['titre'])) . '</option>
								';
							}
							echo '
						</select>
						<br/>
						<input type="hidden" name="tokenAdmin" id="tokenAdmin" value="' . $tokenAdmin . '"/>
						<label></label>
						<input type="submit" value="Valider" />
					</form>
				
		
			  ';
			  echo'</div></div>';}
	
	//Fin admin
	
echo PIED;
// Fin - Affichage du corps de la page
?>