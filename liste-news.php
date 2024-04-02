<?php
include('base.php');

include('tete.php');

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
	if(!isset($_SESSION['m']['acces']) || ($_SESSION['m']['acces'] != 100 && $_SESSION['m']['acces'] != 30))
	{
		echo '<b>Vous n\'êtes pas autorisé à voir les news.</b>';
		include('pied.php');
		exit;
	}
	
	// Droit accès newseur
	if(!isset($_SESSION['m']['pseudo']) AND ($_SESSION['m']['acces'] == 30 || $_SESSION['m']['acces'] >= 90))
	{
		echo 'Vous n\'avez pas les droits d\'administration.';
		include('pied.php');
		exit;
	}
// Fin - Autorisation d'acces à la page

// Debut - Mettre hors ligne
if(isset($_GET['hl']) AND ctype_digit($_GET['hl']))
{
	mysql_query('UPDATE news SET statut=\'0\' WHERE id=\'' . $_GET['hl'] . '\'');
	echo 'La news a bien été mise hors ligne, vous allez être redirigé dans 3 secondes.';
	include('pied.php');
	redirection('liste-news.php');
	exit;
}

if(isset($_GET['el']) AND ctype_digit($_GET['el']))
{
	mysql_query('UPDATE news SET statut=\'1\' WHERE id=\'' . $_GET['el'] . '\'');
	echo 'La news a bien été mise hors ligne, vous allez être redirigé dans 3 secondes.';
	include('pied.php');
	redirection('liste-news.php');
	exit;
}
// Fin - Mettre hors ligne

// Debut - Supprimer
if(isset($_GET['supprimerNews']) AND ctype_digit($_GET['supprimerNews']))
{
	mysql_query('DELETE FROM news WHERE id=\'' . intval($_GET['supprimerNews']) . '\'');
	mysql_query('DELETE FROM commentaires WHERE idNews=\'' . intval($_GET['supprimerNews']) . '\'');
	echo 'La news a bien été supprimé, vous allez être redirigé dans 3 secondes.';
	include('pied.php');
	redirection('liste-news.php');
	exit;
}
// Fin - Supprimer
	
// Debut - Affichage des news
	$reponse = mysql_query('SELECT * FROM news ORDER BY id DESC');

	echo '
	<a href="rediger-news.php">Rédiger une news</a> / <a href="news.php">Retour à l\'index</a><br /><br /><br />
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
			<td class="td"><a href="rediger-news.php?modifierNews=' . $donnees['id'] . '"><img src="images/editer.gif" /></a></td>
			<td class="td"><a href="liste-news.php?supprimerNews=' . $donnees['id'] . '" onclick="confirmation = confirm(\'Voulez-vous vraiment supprimer cette news ?\'); if(confirmation == false) { return false; }"><img src="message/supprimer.gif" /></a></td>
			<td class="td">
			';
				if($donnees['statut'] == true)
				{
					echo '<a href="liste-news.php?hl=' . $donnees['id'] . '" class="el">En ligne</a>';
				}
				else
				{
					echo '<a href="liste-news.php?el=' . $donnees['id'] . '" class="hl">Hrsligne</a>';
				}
			echo '
			</td>
		</tr>
		';
		$i++;
	}

	echo '
	</table></div>
	';	
// Fin - Affichage des news

if($_SESSION['m']['acces'] == 30)
{
	$_SESSION['m']['acces'] = 10;
}

include('pied.php');
?>