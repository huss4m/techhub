<?php
include('base.php');
 
// Debut - Perdre ses accès
	if(isset($_GET['perdreAcces'], $_SESSION['m']['acces']) AND ctype_digit($_GET['perdreAcces']))
	{
		$_SESSION['m']['acces'] = 10;
		
		$requete = mysql_query('SELECT COUNT(*) AS nbEntrees FROM moderateurs WHERE idPseudo="' . $_SESSION['m']['id'] . '"');
		$infos = mysql_fetch_assoc($requete);
		
		if($infos['nbEntrees'] > 0)
		{
			$requete = mysql_query('SELECT * FROM moderateurs WHERE idPseudo="' . $_SESSION['m']['id'] . '"');
			
			while($infos = mysql_fetch_assoc($requete))
			{
				$_SESSION['m']['moderateur'][$infos['idForum']] = 0;
			}
		}
		header('Location: ' . $_SERVER['HTTP_REFERER']); 
		exit;
	}
// Fin - Perdre ses accès

include('tete.php');

// Debut - Listage de forums par catégorie
	$reponse = mysql_query('SELECT COUNT(*) AS nbCat FROM categories'); // On calcule le nombre de Catégories
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['nbCat'] == 0)
	{
		avert('Pas de forums pour le moment.');
		include('pied.php');
		exit;
	}

	// On liste les forums
	$reponse = mysql_query('SELECT * FROM categories ORDER BY id ASC');

	$i = 0;
	while($donnees = mysql_fetch_assoc($reponse))
	{
                if($donnees['image'] != '')
                {
                $imageforum = '<img src="' . stripslashes(nl2br(htmlspecialchars($donnees['image']))) . '" alt="Images ' . stripslashes(nl2br(htmlspecialchars($donnees['image']))) . '" />';
                $forumoui = '<a href="forum-' . $infos['id'] . '-1.html">' . htmlspecialchars(stripslashes($infos['titre'])) . '';
                }
                else
                {
                $imageforum = '';
                $forumoui = '<a href="forum-' . $infos['id'] . '-1.html">' . htmlspecialchars(stripslashes($infos['titre'])) . '';
		        }
		echo '
		<div class="bloc2">
			<h3>' . text_i($donnees['nom']) . '</h3>
			<div class="texte">
			';
			
			// /!\ Requete dans une boucle très mauvais si beaucoup de forum /!\
			if(isset($_SESSION['m']['acces'], $_SESSION['m']['id']))
			{		
				$reponse2 = mysql_query('SELECT * FROM moderateurs WHERE idPseudo=' . $_SESSION['m']['id'] . '');
				$result = mysql_num_rows($reponse2);
					
				if((int) $result != 0)
				{
					$retour = mysql_query('SELECT * FROM forum WHERE idCat=\'' . $donnees['id'] . '\' ORDER BY titre ASC');
				}
				elseif($_SESSION['m']['acces'] == 100)
				{
					$retour = mysql_query('SELECT COUNT(*) AS nbFofo FROM forum WHERE idCat=\'' . $donnees['id'] . '\'');
					$infos = mysql_fetch_assoc($retour);
					$nbFofo = $infos['nbFofo'];
					$retour = mysql_query('SELECT * FROM forum WHERE idCat=\'' . $donnees['id'] . '\' ORDER BY titre ASC');
				}
				else
				{
					$retour = mysql_query('SELECT COUNT(*) AS nbFofo FROM forum WHERE idCat=\'' . $donnees['id'] . '\' AND statut=\'0\'');
					$infos = mysql_fetch_assoc($retour);
					$nbFofo = $infos['nbFofo'];
					$retour = mysql_query('SELECT * FROM forum WHERE idCat=\'' . $donnees['id'] . '\' AND statut=\'0\' ORDER BY titre ASC');
				}
			}
			else
			{
				$retour = mysql_query('SELECT COUNT(*) AS nbFofo FROM forum WHERE idCat=\'' . $donnees['id'] . '\' AND statut=\'0\'');
				$infos = mysql_fetch_assoc($retour);
				$nbFofo = $infos['nbFofo'];
				$retour = mysql_query('SELECT * FROM forum WHERE idCat=\'' . $donnees['id'] . '\' AND statut=\'0\' ORDER BY titre ASC');
			}

			
			$ii = 0; // On compte le nombre de fois qu'on liste les forums pour la séparation
			while($infos = mysql_fetch_assoc($retour))
			{	
				echo '
				<img src="images/puce_base.gif" /> <a href="' . have_url($infos['id'], 1 , FORUM_URL, $infos['titre']) . '">' . htmlspecialchars(stripslashes($infos['titre'])) . ' ' . $imageforum . '</a><br />
				<div class="decalage">' . htmlspecialchars(stripslashes($infos['dpt'])) . '</div>
				';
				
				if($ii < $nbFofo - 1) // Eviter de mettre une séparation en dessous du nom du dernier forum
				{
					echo '<div class="separate"></div>';
				}
				
				$ii++;
			}
			
			echo '
			</div>
		</div>
		<br />
		';
		
		$i++;
	}
// Fin - Listage de forums par catégorie

include('pied.php');
?>