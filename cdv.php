<?php
include('base.php');

// CDV entièrement coder by Cinox

$rep = mysql_fetch_array(mysql_query('SELECT id FROM membres WHERE pseudo = "' . secure($_GET['ps']). '"'));
$sd = $rep['id'];

$idPseudo = $sd;

// Vérification si l'adresse n'a pas été modifié manuellement
if(!ctype_digit($idPseudo))
{
	echo '<p class="alerte" style="font-size: 120%;">Ce profil n\'existe pas.</p>';
	exit;
}

// Vérifions si le pseudo existe bien
$reponse = mysql_query('SELECT * FROM membres WHERE id = ' . intval($idPseudo));
$result = mysql_num_rows($reponse);

if($result == 0)
{
	$var = '<p class="alerte" style="font-size: 120%;">Ce profil n\'existe pas.</p>';
}

$reponse = mysql_query('SELECT * FROM membres WHERE id = ' . $idPseudo);
$donnees = mysql_fetch_assoc($reponse);

// Début - Modification de la description
if(isset($_POST['modifDescr']))
{
	$descr = secure($_POST['modifDescr']);
	mysql_query('UPDATE membres SET presentation="' . $descr . '" WHERE id="' . $_SESSION['m']['id'] . '"');
	header('Location: cdv-' . $donnees['pseudo'] . '.htm');
}
// Fin - Modification de la description

// Début - Affichage de la description
if($idPseudo == $_SESSION['m']['id'])
{
	$url = $_SERVER['REQUEST_URI'];
	
	if(stristr($url, 'cdv-' . $donnees['pseudo'] . '.php?modifierDescription') || stristr($url, 'cdv-' . $donnees['pseudo'] . '.html?modifierDescription') || stristr($url, 'cdv-' . $donnees['pseudo'] . '.htm?modifierDescription'))
	{
		if(isset($donnees['presentation']) AND !empty($donnees['presentation']))
		{
			$affichDescr = stripslashes('<form method="post"><textarea id="modifDescr" name="modifDescr" rows="3" cols="30">' . stripslashes($donnees['presentation']) . '</textarea>
			<br /><span style="margin-left: 25px;"><input border="0" type="image" value="submit" src="images/cdv/valider.png"> <a href="cdv-' . $donnees['pseudo'] . '.htm"><img src="images/cdv/annuler.png"></a></span></form>');
		}
		else
		{
			$affichDescr = stripslashes('<form method="post"><textarea id="modifDescr" name="modifDescr" rows="3" cols="30">Non renseign&eacute;</textarea>
			<br /><span style="margin-left: 25px;"><input border="0" type="image" value="submit" src="images/cdv/valider.png"> <a href="cdv-' . $donnees['pseudo'] . '.htm"><img src="images/cdv/annuler.png"></a></span></form>');
		}
	}
	else
	{
		if(isset($donnees['presentation']) AND !empty($donnees['presentation']))
		{
			$affichDescr = bbCode(nl2br(text_i($donnees['presentation'])));
		}
		else
		{
			$affichDescr ='Non renseign&eacute;';
		}
	}
}
else
{
	if(isset($donnees['presentation']) AND !empty($donnees['presentation']))
	{
		$affichDescr = bbCode(nl2br(text_i($donnees['presentation'])));
	}
	else
	{
		$affichDescr ='Non renseign&eacute;';
	}
}
// Fin - Affichage de la description

// Début - Editer la description
if(isset($_SESSION['m']['id']) && $idPseudo == $_SESSION['m']['id'])
{
	$editDescr = '<a href="cdv-' . $donnees['pseudo'] . '.htm?modifierDescription"><img src="images/message/editer.gif" alt="Modifier votre description" title="Modifier votre description"></a>';
}
else
{
	$editDescr = '';
}
// Fin - Editer la description



echo'
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" SYSTEM "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head>
	<title>Profil de ' . text_i($donnees['pseudo']) . '</title>
	<link href="css/cdv.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/accordion.js"></script>
</head>
	<body'.$barre.'>';
	
		// Debut - Cdv non existante/Cdv banni
		if($donnees['acces'] == 1)
		{
			echo '
			<div class="alerte centrer" style="font-size: 120%;">Ce pseudo est banni.</div>
			';
			if(isset($_SESSION['m']['acces']) AND $_SESSION['m']['acces'] == 100)
			{
				echo '<a href="cdv-' . $_GET['id'] . '-' . $_GET['id'] . '.html">Débannir ce membre</a>';
				$reponse = mysql_query('
				SELECT message.message AS `messageA` 
				FROM message 
				INNER JOIN bann ON message.id = bann.idMessage
				WHERE bann.idPseudo=' . $_GET['id'] . '
				');
				$donnees = mysql_fetch_assoc($reponse);	
				echo '<br /><br />
				Message pour lequel ce membre a été banni :<br />' . nl2br(text_i($donnees['messageA']));
			}
			exit;
		}

		if(isset($var))
		{
			echo $var;
			exit;
		}
		// Fin - Cdv non existante/Cdv banni
		
		// Début - Bloc
		echo'
		<div id="forme">
		';
		
			// Début - Affichage du Pseudo en fonction du sexe
			echo'
			<div class="titre" id="sexe_' . text_i($donnees['sexe']) . '">
				Profil de ' . text_i($donnees['pseudo']) . '
			</div>';
			// Fin - Affichage du Pseudo en fonction du sexe
			
			// Début - Affichage du tableau
			echo'
			<table cellspacing="0">
			';
				// Debut - Age
				echo '
				<tr>
					<td><b>Age</b></td>
				';
				if(isset($donnees['age']) AND !empty($donnees['age']))
				{
					echo'<td>' . text_i($donnees['age']) . ' ans</td>';
				}
				else
				{
					echo'<td>Non renseign&eacute;</td>';
				}
				echo'
				</tr>
				';
				// Fin - Age
				
				// Debut - Pays
				echo '
				<tr>
					<td><b>Pays</b></td>
				';
				if(isset($donnees['pays']) AND !empty($donnees['pays']))
				{
					echo'<td>' . text_i($donnees['pays']) .  '</td>';
				}
				else
				{
					echo'<td>Non renseign&eacute;</td>';
				}
				echo'
				</tr>
				';
				// Fin - Pays
				
				// Début - Ancienneté
				$membredepuis = ceil((time() - $donnees['timestamp']) / 60 / 60 / 24);
				$membredepuis = number_format($membredepuis, 0, '', '.');
									
				if($membredepuis <= 1)
				{
					$s = '';
				}
				else
				{
					$s = 's';
				}
					echo'
					<tr>
						<td><b>Anciennet&eacute;</b></td>
						<td>' . text_i($membredepuis) . ' jour' . $s . '</td>
					</tr>
					';
				// Fin - Ancienneté
				
				// Début - Dernier passage
				$requete = mysql_query('SELECT MAX(message.timestamp) AS `timestampDernierMessage` FROM message WHERE idPseudo=' . intval($idPseudo));
				$infos = mysql_fetch_assoc($requete);
				
				$derniere = $infos['timestampDernierMessage'];
				$dernier = date('d\/m\/Y', $derniere);
				
				if($derniere == 0)
				{
					$dernier = 'Jamais';
				}
					echo'
					<tr>
						<td><b>Dernier passage</b></td>
						<td>' . text_i($dernier) . '</td>
					</tr>
					';
				// Fin - Dernier passage
				
				// Début - Adresse mail
				echo'
				<tr>
					<td><b>Adresse Mail</b></td>
				';

				if(isset($donnees['msn']) AND !empty($donnees['msn']))
				{
					echo'
					<td>
						<a href="mailto:' . text_i($donnees['msn']) . '" title="' . text_i($donnees['msn']) . '">' . mb_substr(text_i($donnees['msn']), 0, 13) . '[...]</a>
					</td>
					';
				}
				else
				{
					echo'
					<td>
						Non renseign&eacute;
					</td>
					';
				}
				// Fin - Adresse mail
				
				// Début - Message postés
				$aysql = mysql_query('SELECT COUNT(*) FROM message WHERE idPseudo = ' . intval($idPseudo));
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
				
				$affichPost = text_i($posts);
					echo'
					<tr>
						<td><b>Messages post&eacute;s</b></td>
						<td>' . $affichPost . '</td>
					</tr>
					';
				// Fin - Message postés
			echo'
			</table>
			';
			// Fin - Affichage du tableau
			
			// Début - Avatar
			echo'
			<div id="avatar">
			<div class="titre2"><div id="img"><img src="images/cdv/map.png"></div> <div id="align"><span style="margin-left: -15;">Avatar</span></div></div>
				<center>
			';
			if(isset($donnees['avatar']) AND !preg_match("#^( *)$#", $donnees['avatar']))
			{
				echo'
				<a href="' . text_i($donnees['avatar']) . '"><img src="' . text_i($donnees['avatar']) . '" height="80" width="80"></a>
				';
			}
			else
			{
				echo'
				<img src="images/noavatar.png" height="80" width="80">
				';
			}
			echo'
			</center>
			</div>
			';
			// Fin - Avatar
			
						
				// Début - Information
				echo'
				<div id="contenue">
					<div class="titre2"><div id="img"><img src="images/cdv/world.png"></div> <div id="align">Information</div></div>
					<b>
				';
					// Début - points
					$query = mysql_query('SELECT points FROM membres WHERE id = ' . intval($idPseudo));
					$shop = mysql_fetch_assoc($query);
					$argentMembre = text_i(number_format($shop['argent'], 0, '', '.'));
					echo'
						<img src="images/cdv/argent.png" > Points : ' . $argentMembre . ' ' . NOM_ARGENT . '
					';
					// Fin - points
					
					// Début - Grade
					if($donnees['acces'] == 100) // Admin
					{
						$grade = ' <strong>' . ($COLP->afficher(100, 'Administrateur')) . '</strong>';
					}
					elseif($donnees['acces'] == 95) // Directeur
					{
						$grade = ' <strong>' . ($COLP->afficher(95, 'Directeur')) . '</strong>';
					}
					elseif($donnees['acces'] == 90) // Super modo
					{
						$grade = ' <strong>' . ($COLP->afficher(90, 'Super Modo')) . '</strong>';
					}
					elseif($donnees['acces'] == 50) // Modérateur
					{
						$grade = ' <strong>' . ($COLP->afficher(50, 'Mod&eacute;rateur')) . '</strong>';
					}
					elseif($donnees['acces'] == 98) // Codeur
					{
						$grade = ' <strong>' . ($COLP->afficher(98, 'Codeur')) . '</strong>';
					}
					else // Membre
					{
						$grade = ' <strong>' . ($COLP->afficher(10, 'Membre')) . '</strong>';
					}
					echo'
					<br /><img src="images/cdv/admin.png"> Grade : <i>' . $grade . '</i>
					';
					// Fin - Grade
					
					// Début - Rang
					$rang = rang($posts,10);
					echo'
					<br /><img src="images/cdv/rang.png"> Rang : <i>' . $rang . '</i> 
					';
					// Fin - Rang
				echo'
				</b>
				</div>
				';
				// Fin - Information
	
			
			
			// Début - Présentation
				echo'
				<div id="contenue">
					<div class="titre2"><div id="img"><img src="images/cdv/user.png"></div> <div id="align">Description ' . $editDescr . '</div></div>
					' . $affichDescr . '
				</div>
				';
			// Fin - Présentation
		echo'
		</div>
		';
		// Fin - Bloc
	
echo'
	</body>
</html>';