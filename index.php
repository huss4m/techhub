<?php
include('base.php');
include('tete.php');

// -----------------------
// Système de pagination : 
// -----------------------


 
$nombreDeNewsParPage = 9;

$retour = mysql_query('SELECT COUNT(*) AS nb_News FROM news WHERE statut=\'1\'');
$donnees = mysql_fetch_array($retour);
$totalDesNews = $donnees['nb_News'];

// Si il n'y a aucune news, on affiche rien et on va pas plus loin
if($totalDesNews == 0)
{
	echo '<i>Pas de News pour le moment</i>';
	include('pied.php');
	exit;
}


$nb_page  = ceil($totalDesNews / $nombreDeNewsParPage);

// On regarde à quel page on se trouve :
if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
{
	$page = intval($_GET['page']);
}
else
{
	$page = 1;
}

echo page('index', $page, $nb_page);

// On calcule le numéro du premier message qu'on prend pour le LIMIT de MySQL
$premiereNewsAafficher = ($page - 1) * $nombreDeNewsParPage;


// ------------------
// Affichage des news
// ------------------

// Sélection des news en fonction de l'ordre des news à afficher
$reponse = mysql_query('SELECT * FROM news WHERE statut=\'1\' ORDER BY id DESC LIMIT ' . $premiereNewsAafficher . ', ' . $nombreDeNewsParPage);

$i = 0;
while($donnees = mysql_fetch_array($reponse))
{
	$mots_complets = $donnees['intro']; // Affichage de l'introduction
	$nb_mots = 5; // Nombre de mot
	$mot_courts = debutchaine($mots_complets, $nb_mots);
	$titres_complets = $donnees['titre']; // Affichage du Titre
	$nb_titres = 1; // Nombre de mot
	$titre_courts = debutchaine($titres_complets, $nb_titres);
	echo'
 	<div class="bloc5">
	  <h3>' . nl2br(text_i($titre_courts)) . '</h3>
   	 	<div class="texte">
   	 	<img src="' . nl2br(text_i($donnees['lien'])) . '" width="170px" height="88px"><br />
		<div class="separate"></div>
		<b>Ecrit par <font color="red">' . nl2br(text_i($donnees['auteur'])) . '</font></b><br /><br />
		' . nl2br(bbCode(text_i($mot_courts))) . '
		<br /><br /><img src="images/puce_base.gif"> <a href="news.htm?id=' . $donnees['id'] . '">Lire la suite</a>
		</div>
	 </div>
	  ';
	$i++;
}
echo'<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'; // Quelques <br /> pour espacer

include('pied.php');
?>
