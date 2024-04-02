<?php
include('base.php');
include('tete.php');

//Maintenant, on affiche notre page normalement, le champ caché token en plus

// Debut - Autorisation d'acces à la page

	if(isset($_SESSION['m']['id']))
	{
		$reponse = mysql_query('SELECT * FROM newseur WHERE idPseudo=' . $_SESSION['m']['id']);
		$result = mysql_num_rows($reponse);
		
		if($result > 0)
		{
			$_SESSION['m']['acces'] = 30;
		}
	}
	
	// Si on a l'accès pour 
	if(!isset($_SESSION['m']['acces']) || ($_SESSION['m']['acces'] < 92 && $_SESSION['m']['acces'] != 30))
	{
		avert('<b>Vous n\'êtes pas autorisé à voir les news.</b>');
		include('pied.php');
		exit;
	}
	
	// Droit accès newseur
	if(!isset($_SESSION['m']['pseudo']) AND ($_SESSION['m']['acces'] != 30 || $_SESSION['m']['acces'] > 92))
	{
		avert('Vous n\'avez pas les droits d\'administration.');
		include('pied.php');
		exit;
	}
// Fin - Autorisation d'acces à la page

// Debut - Mettre hors ligne
if(isset($_GET['hln']) AND ctype_digit($_GET['hln']))
{
	mysql_query('UPDATE news SET statut=\'0\' WHERE id=\'' . $_GET['hln'] . '\'');
	avert('La news a bien été mise hors ligne, vous allez être redirigé dans 3 secondes.');
	include('pied.php');
	redirection('listes.php');
	exit;
}

if(isset($_GET['eln']) AND ctype_digit($_GET['eln']))
{
	mysql_query('UPDATE news SET statut=\'1\' WHERE id=\'' . $_GET['eln'] . '\'');
	avert('La news a bien été mise hors ligne, vous allez être redirigé dans 3 secondes.');
	include('pied.php');
	redirection('listes.php');
	exit;
}
// Fin - Mettre hors ligne

// Debut - Supprimer
if(isset($_GET['supprimerNews']) AND ctype_digit($_GET['supprimerNews']))
{
	mysql_query('DELETE FROM news WHERE id=\'' . intval($_GET['supprimerNews']) . '\'');
	mysql_query('DELETE FROM commentaires WHERE idNews=\'' . intval($_GET['supprimerNews']) . '\'');
	avert('La news a bien été supprimé, vous allez être redirigé dans 3 secondes.');
	include('pied.php');
	redirection('listes.php');
	exit;
}
// Fin - Supprimer
	




// Debut - Supprimer
if(isset($_GET['supprimerCours']) AND ctype_digit($_GET['supprimerCours']))
{
	mysql_query('DELETE FROM cours WHERE id=\'' . intval($_GET['supprimerCours']) . '\'');
	
	avert('La cours a bien été supprimé, vous allez être redirigé dans 3 secondes.');
	include('pied.php');
	redirection('listes.php');
	exit;
}
// Fin - Supprimer





// Fin - Supprimer

// Debut - Affichage des news
	$reponse = mysql_query('SELECT * FROM news ORDER BY id DESC');

	echo '
	<div class="bloc2">
		<h3>Liste des news</h3>
			<div class="listageTopic"><table class="size">
		<tr class="trHaut">
			<th class="col5"><b>Date</b></th>
			<th class="col3"><b>Auteur</b></th>
			<th width="261px">Titre</b></th>
			<th></th>
			<th></th>
			<th><b>Statut</b></th>
		</tr>
	';

	$i = 0;
	while($donnees = mysql_fetch_array($reponse))
	{
		if($i % 2 == 0)
		{
			$bloc = 'tr1';
		}	
		else
		{
			$bloc = 'tr2';	
		}

		echo '
		<tr class="' . $bloc . '">
			<td class="td">' . date('d/m/Y\ H\hi', $donnees['timestamp']) . '</td>
			<td class="td">' . stripslashes(nl2br(htmlspecialchars($donnees['auteur']))) . '</td>
			<td class="td">' . stripslashes(nl2br(htmlspecialchars($donnees['titre']))) . '</td>
			<td class="td"><a href="rediger.php?ad=news&amp;modifierNews=' . $donnees['id'] . '"><img src="images/edit.png" /></a></td>
			<td class="td"><a href="listes.php?supprimerNews=' . $donnees['id'] . '" onclick="confirmation = confirm(\'Voulez-vous vraiment supprimer cette news ?\'); if(confirmation == false) { return false; }"><img src="images/del.png" /></a></td>
			<td class="td">
			';
				if($donnees['statut'] == true)
				{
					echo '<a href="listes.php?hln=' . $donnees['id'] . '" class="el">En ligne</a>';
				}
				else
				{
					echo '<a href="listes.php?eln=' . $donnees['id'] . '" class="hl">Hrsligne</a>';
				}
			echo '
			</td>
		</tr>
		';
		$i++;
	}

	echo '
	</table><br />
	<a href="rediger.php?ad=news">Ajouter une news</a> / <a href="index.php">Retour à la liste des news</a><br />
			</div>

	</div>
<br />
<br />
	';	
// Fin - Affichage des news

	
// Debut - Affichage des cours
	$reponse = mysql_query('SELECT * FROM cours ORDER BY id DESC');

	echo '
	<div class="bloc2">
		<h3>Liste des cours</h3>
			<div class="listageTopic"><table class="size">
		<tr class="trHaut">
			<th class="col5"><b>Auteur</b></th>
			<th class="col3"><b>Titre</b></th>
			<th width="261px"></b></th>
			
			
			
		</tr>
	';

	$i = 0;
	while($donnees = mysql_fetch_array($reponse))
	{
		if($i % 2 == 0)
		{
			$bloc = 'tr1';
		}	
		else
		{
			$bloc = 'tr2';	
		}

		echo '
		<tr class="' . $bloc . '">
			<td class="td">' . stripslashes(nl2br(htmlspecialchars($donnees['auteur']))) . '</td>
			<td class="td">' . stripslashes(nl2br(htmlspecialchars($donnees['titre']))) . '</td>
			<td class="td"><a href="rediger.php?ad=cours&amp;modifierCours=' . $donnees['id'] . '"><img src="images/edit.png" /></a></td>
			<td class="td"><a href="listes.php?supprimerCours=' . $donnees['id'] . '" onclick="confirmation = confirm(\'Voulez-vous vraiment supprimer ce cours ?\'); if(confirmation == false) { return false; }"><img src="images/del.png" /></a></td>
		
			';
				
			echo '
			
		</tr>
		';
		$i++;
	}

	echo '
	</table>
<br />
	<a href="rediger.php?ad=cours">Ajouter un cours</a> / <a href="liste-cours.php">Retour à la liste des cours</a><br />
			</div>
	</div>
<br />
<br />
	';	
// Fin - Affichage des cours



if($_SESSION['m']['acces'] == 30)
{
	$_SESSION['m']['acces'] = 10;
}

include('pied.php');
?>
