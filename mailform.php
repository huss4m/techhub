<?php
include('base.php');
include('tete.php');
	 
// Début - Déclaration des Puces
$puce = '<img src="images/puce_base.gif">';
$puce2 = '<img src="images/puce_forum.png">';
// Fin - Déclaration des Puces

// Début - Le Staff
$directeur = mysql_query('SELECT id, pseudo FROM membres WHERE acces = 95'); // Directeur
$admin = mysql_query('SELECT id, pseudo FROM membres WHERE acces = 100'); // Admin
$codeur = mysql_query('SELECT id, pseudo FROM membres WHERE acces = 98'); // Codeur
$modochef = mysql_query('SELECT id, pseudo FROM membres WHERE acces = 90'); // Super Modo
$modo = mysql_query('SELECT moderateurs.idPseudo, moderateurs.idForum, membres.pseudo FROM moderateurs INNER JOIN membres ON membres.id = moderateurs.idPseudo'); // Modo
$newseur = mysql_query('SELECT newseur.idpseudo, membres.id, membres.pseudo AS `pseudo` FROM newseur INNER JOIN membres ON newseur.idPseudo = membres.id ORDER BY membres.pseudo'); // Newseur
$membres = mysql_query('SELECT id, pseudo FROM membres WHERE acces = 10'); // Membres

$nomDir = 'Directeur<div class="separate"></div>'; // Nom Admin
$nomAdmin = 'Administrateur<div class="separate"></div>'; // Nom Admin
$nomCodeur = 'Codeur<div class="separate"></div>'; // Nom Codeur
$nomModChef = 'Super Modo<div class="separate"></div>'; // Nom Super Modo
$nomModo = 'Mod&eacute;rateur<div class="separate"></div>'; // Nom Modo
$nomNewseur = 'Newseur<div class="separate"></div>'; // Nom Newseur
$nomMembre = 'Membres<div class="separate"></div>'; // Nom Membre

echo'
  <div class="bloc2">
    <h3>Le Staff de ' . TITRE_FORUM . '</h3>
	  <div class="texte">
	  <strong>' . ($COLP->afficher(95, $nomDir)) . '</strong>';
		$retourDir = mysql_query('SELECT COUNT(*) AS nb_Dir FROM directeur');
		$donneesDir = mysql_fetch_array($retourDir);
		$totalDesDir = $donneesDir['nb_Dir'];
	    while($donnees = mysql_fetch_assoc($directeur))
		{
			echo'' . $puce2 . ' <a href="cdv-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a><br />';
		}
		if($totalDesDir == 0)
		{
			echo'<i>En recrutement</i><br />';
		}
	  echo'<br /><strong>' . ($COLP->afficher(98, $nomCodeur)) . '</strong>';
		$retourCod = mysql_query('SELECT COUNT(*) AS nb_Cod FROM codeur');
		$donneesCod = mysql_fetch_array($retourCod);
		$totalDesCod = $donneesCod['nb_Cod'];
	    while($donnees = mysql_fetch_assoc($codeur))
		{
			echo'' . $puce2 . ' <a href="cdv-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a><br />';
		}
		if($totalDesCod == 0)
		{
			echo'<i>En recrutement</i><br />';
		}
	  echo'<br /><strong>' . ($COLP->afficher(100, $nomAdmin)) . '</strong>';
		$retourA = mysql_query('SELECT COUNT(*) AS nb_A FROM admin');
		$donneesA = mysql_fetch_array($retourA);
		$totalDesA = $donneesA['nb_A'];
	    while($donnees = mysql_fetch_assoc($admin))
		{
			echo'' . $puce2 . ' <a href="cdv-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a><br />';
		}
		if($totalDesA == 0)
		{
			echo'<i>En recrutement</i><br />';
		}
	  echo'<br /><strong>' . ($COLP->afficher(90, $nomModChef)) . '</strong>';
		$retourS = mysql_query('SELECT COUNT(*) AS nb_S FROM supermodo');
		$donneesS = mysql_fetch_array($retourS);
		$totalDesS = $donneesS['nb_S'];
	    while($donnees = mysql_fetch_assoc($modochef))
		{
			echo'' . $puce2 . ' <a href="cdv-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a><br />';
		}
		if($totalDesS == 0)
		{
			echo'<i>En recrutement</i><br />';
		}
	  echo'<br /><strong>' . ($COLP->afficher(50, $nomModo)) . '</strong>';
		$retourM = mysql_query('SELECT COUNT(*) AS nb_M FROM moderateurs');
		$donneesM = mysql_fetch_array($retourM);
		$totalDesM = $donneesM['nb_M'];
	    while($donnees = mysql_fetch_assoc($modo))
		{
			echo'' . $puce2 . ' <a href="cdv-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a><span style="float: right;"><a href="forum-' . $donnees['idForum'] . '-1.html" target="_blank">Voir le Forum</a></span><br />';
		}
		if($totalDesM == 0)
		{
			echo'<i>En recrutement</i><br />';
		}
	  echo'<br /><strong>' . $nomNewseur . '</strong>';
		$retourN = mysql_query('SELECT COUNT(*) AS nb_N FROM newseur');
		$donneesN = mysql_fetch_array($retourN);
		$totalDesN = $donneesN['nb_N'];
	    while($donnees = mysql_fetch_assoc($newseur))
		{
			echo'' . $puce2 . ' <a href="cdv-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a><br />';
		}
		if($totalDesN == 0)
		{
			echo'<i>En recrutement</i><br />';
		}
	  echo'<br /><strong>' . ($COLP->afficher(10, $nomMembre)) . '</strong>';
			echo'' . $puce2 . ' <a href="liste-membre.html" target="_blank"">Liste des Membres</a><br />';
	 echo'</div>
  </div><br />
';
// Fin - Le Staff

include('pied.php');
?>