<?php
include('base.php');
include('tete.php');

// Début - Statistiques
     // Nombre de Membres
     $donnees = $bdd->sql_query('SELECT COUNT(*) AS nbMembre FROM membres', true);
     $nbMembre = $donnees['nbMembre'];

// Nombre de Topics
     $donnees = $bdd->sql_query('SELECT COUNT(*) AS nbTopic FROM topic', true);
     $nbTopic = $donnees['nbTopic'];

// Nombre de Messages 
     $donnees = $bdd->sql_query('SELECT COUNT(*) AS nbMessage FROM message', true);
     $nbMessage = $donnees['nbMessage'];

// Nombre de Forums
     $donnees = $bdd->sql_query('SELECT COUNT(*) AS nbForum FROM forum', true);
     $nbForum = $donnees['nbForum'];
// Fin - Statistiques

$infos = $bdd->sql_query('SELECT COUNT(*) AS `nbMessages` FROM message WHERE idPseudo=?', true, false, true, $DNS["id"]) or die ($db->errorCode);
$posts = $infos['nbMessages'];
$messages = stripslashes(number_format($infos['nbMessages'], 0, '', '.'));

echo'
	
	<div class="separate"></div><br />
';

if(!isset($_SESSION['m']['id']))
{
 echo'
  <div class="bloc2">
    <h3>Bienvenue sur ' . TITRE_FORUM . ' !</h3>
	  <div class="texte">
	 <b><center>Bienvenue <font color="red">Visiteur</font> ! </center><br />
	 Nous sommes contents de ta pr&eacute;sence sur TechHub. N\'h&eacute;site pas &agrave; nous donner tes id&eacute;es sur le forum Suggestion et R&eacute;clamation.
Nous te souhaitons une bonne navigation sur le site. 
	  </div></b>
  </div><br />
 ';
}
else
{
 echo '
  <div class="bloc2">
    <h3>Bienvenue sur ' . TITRE_FORUM . ' !</h3>
	  <div class="texte">
	  ';
	  if($messages == 0) { echo'<b><center>Bonjour <font color="red">' . $_SESSION['m']['pseudo'] . '</font> ! </center><br />
	  Tu es apparemment nouveau ici, tu n\'as post&eacute; aucun message. Nous te souhaitons donc la bienvenue sur ' . TITRE_FORUM . ' ! <br />
	  Si jamais tu es perdu, ou si tu as des questions, adresses-toi &agrave; un administrateur, il sera ravie de t\'aider. <br />
	  Nous te souhaitons une bonne navigation sur le site. <br />
	  <img src="images/puce_forum.png"> Message post&eacute; : <font color="green">aucun</font>'; }
	  if($messages > 0) { echo'<b><center>Bonjour <font color="red">' . $_SESSION['m']['pseudo'] . '</font> ! </center><br />
	  Nous sommes contents de ta pr&eacute;sence sur ' . TITRE_FORUM . '. N\'h&eacute;site pas &agrave; nous donner tes id&eacute;es sur le forum Suggestion et R&eacute;clamation.<br />
	  Nous te souhaitons une bonne navigation sur le site. <br />
	  <img src="images/puce_forum.png"> Messages post&eacute;s : <font color="green">' . $messages . '</font>'; }
	  echo'</b>
	  </div>
  </div><br />
 ';
}
// Fin - Bloc de bienvenue

// Début - Bloc dernier membre et stats divers

/* Membre */
echo'<div id="dern_news"><strong>' . TITRE_FORUM . '</strong></div>';
	 $nombreDeMembres = 5; // Nombre de Membre a afficher
	 $donnees = $bdd->sql_query('SELECT COUNT(*) AS nb_Membres FROM membres', true);
	 $totalDeMembres = $donnees['nb_Membres'];
	 echo '<div class=bloc2><h3> Les '.$nombreDeMembres.' Derniers Membres</h3><div class=texte>';
	 $premierMembre = 0 * $nombreDeMembres;
	 $reponse = $bdd->sql_query("select * from `membres` order by `id` desc limit $premierMembre, $nombreDeMembres");
	 $i = 0;
	 echo'
	 <center>
	 ';
	 while($donnees = $bdd->sql_fetch($reponse))
	 {
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
		elseif($donnees['acces'] == 98) // Codeur
		{
			$pseudo = ' <strong>' . ($COLP->afficher(98, $donnees['pseudo'])) . '</strong>';
		}
		else // Membre
		{
			$pseudo = ' <strong>' . ($COLP->afficher(10, $donnees['pseudo'])) . '</strong>';
		}
	  echo '
	   <strong><a href="cdv-' . nl2br(text_i($donnees['pseudo'])) . '.htm"" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/imgs/user.png"></a> ' . nl2br($pseudo) . '</strong><br />';
	   $i++;
	 }
if($totalDeMembres == 0)
{
	 echo'
	 <i>Aucun Membre inscrit pour le moment</i><br />';
}

echo '<br /><center><b><img src="images/puce_forum.png" /> <a href="liste-membre.htm">Liste compl&egrave;te des Membres</a></b> <img src="images/puce_forum2.png" /></center></div></div>';
/* Membre */

/* Stats */
echo'
<br />
   <div class="bloc2">
	<h3>Statistiques diverses</h3>
	  <div class="texte">
	  <center><strong>Nombre de Membres inscrit : ' . $nbMembre . '<br />Nombre de Forums cr&eacute;&eacute; : ' . $nbForum . '<br />Nombre de Topic post&eacute; : ' . $nbTopic . '<br /> Nombre de Message post&eacute; : ' . $nbMessage . '</strong></center>
	  <br /><center><b><a href="stats.htm">Plus de Stats</a></b> <img src="images/puce_forum2.png" /></center>
	  </div>
   </div>
';
/* Stats */

// Fin - Bloc dernier membre et stats divers

echo'<br /><br />'; // Quelques <br /> pour espacer

// Début - Bloc dernière news
echo'<div id="dern_news"><strong>Les 3 derni&egrave;res News</strong></div>';
	 $nombreDeNews = 3; // Nombre de News a afficher
	 $donnees = $bdd->sql_query('SELECT COUNT(*) AS nb_News FROM news WHERE statut=\'1\'', true);
	 $totalDesNews = $donnees['nb_News'];
	 $premiereNewsAafficher = '0' * $nombreDeNews;
	 $reponse = $bdd->sql_query('SELECT * FROM news WHERE statut=\'1\' ORDER BY id DESC LIMIT ' . $premiereNewsAafficher . ', ' . $nombreDeNews);
	 $i = 0;
	 // Fin - Limitation de mots
	 while($donnees = $bdd->sql_fetch($reponse))
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

if($totalDesNews == 0)
{
	 echo'
	 <i>Aucune News pour le moment.</i><br /><br />';
}
// Fin - Bloc dernière news

echo'<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'; // Quelques <br /> pour espacer

include('pied.php');
?> 
