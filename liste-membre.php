<?php
include('base.php'); // Base
include('tete.php'); // Test

$nombreDeMembresParPage = 20;

$retour = mysql_query('SELECT COUNT(*) AS nb_Membres FROM membres');
$donnees = mysql_fetch_array($retour);
$totalDesMembres = $donnees['nb_Membres'];

// Si il n'y a aucun membres, on affiche rien et on va pas plus loin
if($totalDesMembres == 0)
{
echo '<i>Pas de Membres pour le moment</i>';
include('pied.php');
exit;
}


$nb_page = ceil($totalDesMembres / $nombreDeMembresParPage);

// On regarde √ quel page on se trouve :
if(isset($_GET['page']) AND is_numeric($_GET['page']) AND $_GET['page'] <= $nb_page AND $_GET['page'] > 1)
{
$page = intval($_GET['page']);
}
else
{
$page = 1;
}

// On calcule le num√©ro du premier message qu'on prend pour le LIMIT de MySQL
$premiereMembresAafficher = ($page - 1) * $nombreDeMembresParPage;

// S√©lection des membres en fonction de l'ordre des membres √ afficher
$reponse = mysql_query('SELECT * FROM membres WHERE valide=1 ORDER BY id DESC LIMIT ' . $premiereMembresAafficher . ', ' . $nombreDeMembresParPage);

$i = 0;
	echo '
		<center>
		<form method="get" action="liste-membre.php">
			<span style="
		border: 1px solid black;
		background: white;
		padding: 15px;
		">
		<b>Rechercher un pseudo :</b> <input type="text" name="pseudo" /> <input type="submit" value="Valider" class="bouton2"/>
		</span>
		</form></center>
		<br />
	<div class="bloc2">
		<h3>Liste des membres</h3>
		<div class="texte">
		';
		if(isset($_GET['pseudo']))
		{
			$reponse = mysql_query('SELECT id, pseudo FROM membres WHERE pseudo LIKE \'%' . nojs(nl2br(bbCode(text_i($_GET['pseudo'])))) . '%\' ORDER BY pseudo ASC');
		    echo'<center><b><font color="#dd0000">R&eacute;sultat de la recherche pour </font> ' . nojs(nl2br(bbCode(text_i($_GET['pseudo'])))) . ' <font color="#dd0000"></font></b></center><br />';
			echo '<table>';
			while($donnees = mysql_fetch_assoc($reponse))
			{
				echo '
				<tr>
					<td><img src="images/gris/puce_liste_bleue.gif"> <b>' . text_i($donnees['pseudo']) .  '</b> <a href="cdv-' . text_i($donnees['pseudo']) . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/imgs/user.png" /></a> ' .$ban. '</td>
				</tr>
				';
			}
			echo '</table><br /><img src="images/puce_base.gif"> <a href="liste-membre.php">Retour</a>';
		}
		else
		{
			echo page_i('liste-membre.php?page=', $page, $nb_page);
			echo'<table>
			';
			$premierMembreAafficher = ($page - 1) * $nombreDeMembresParPage;
			$reponse = mysql_query('SELECT id, pseudo FROM membres ORDER BY pseudo ASC LIMIT ' . $premierMembreAafficher . ', ' . $nombreDeMembresParPage);
			
			while($donnees = mysql_fetch_assoc($reponse))
			{
				echo '
				<tr>
					<td><img src="images/puce_base.gif" /> <b>' . text_i($donnees['pseudo']) .  '</b> <a href="cdv-' . text_i($donnees['pseudo']) . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/imgs/user.png" /></a> ' .$ban. '</td>
				</tr>
				';
			}
			
			echo '
			</table>';
			echo page_i('liste-membre.php?page=', $page, $nb_page);
		}
		echo '
		</div>
	</div>
</div><br />
	';

include('pied.php'); // Pied
?>