<?php
include('base.php');
include('tete.php');
?>

<br><br>
<?php

$reponse = mysql_query('SELECT * FROM projets');

while($donnees = mysql_fetch_assoc($reponse)) {

	$mots_complets = $donnees['intro']; // Affichage de l'introduction
	$nb_mots = 11; // Nombre de mot
	$mot_courts = debutchaine($mots_complets, $nb_mots);
	$titres_complets = $donnees['titre']; // Affichage du Titre
	$nb_titres = 10; // Nombre de mot
	$titre_courts = debutchaine($titres_complets, $nb_titres);
	echo'
 	<div class="bloc5">
	  <h3>' . nl2br(text_i($titre_courts)) . '</h3>
   	 	<div class="texte">
   	 	<img src="' . nl2br(text_i($donnees['lien'])) . '" width="170px" height="88px"><br />
		<div class="separate"></div>
		<b>Créé par <font color="red">' . nl2br(text_i($donnees['leader'])) . '</font></b><br /><br />
		' . nl2br(bbCode(text_i($mot_courts))) . '
		<br /><br /><img src="images/puce_base.gif"> <a href="projet.php?id=' . $donnees['id'] . '">Lire la suite</a>';
		
		
	
	
		echo'
		
		</div>
	 </div>
	  ';
	$i++;
}
if($donnees = null) {
	avert('Pas de projets pour le moment');
}
echo'<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'; // Quelques <br /> pour espacer
	

?>


<?php
include('pied.php');
?>