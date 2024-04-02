<?php
include('base.php');
include('tete.php');

if(isset($_GET['id'])) {
	if(isset($_POST) && !empty($_POST)) {
	
	$membre = secure($_POST['membre']);
	
	$req = mysql_query('SELECT id FROM membres WHERE pseudo="'.$membre.'"');
	$donnees = mysql_fetch_assoc($req);
	
	mysql_query('INSERT INTO joindreProjet VALUES("", "'.$_GET['id'].'", "'.$donnees['id'].'")');
	avert('Membre ajouté avec succès.');
	}
?>

<div class="bloc2" class="redigerNews">
	<form method="post">
		<h3>Ajouter un membre</h3> 
		<div class="texte">
			<label for="membre">Membre: </label> <input type="text" name="membre" value="<?php echo stripslashes(nl2br(htmlspecialchars($membre))); ?>" /><br />
			<center><input type="submit" class="bouton" value="Ajouter au projet" /></center>
		</div>
		</div>
<?php
}
include('pied.php');
?>