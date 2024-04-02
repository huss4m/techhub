<?php
include('base.php');
include('tete.php');
 
// Debut - Autorisation d'acces de page
	if(!isset($_SESSION['m']['id'])) // Si il est connecté
	{
		avert(bbCode("Vous n'êtes pas connecté."));
		include('pied.php');
		exit;
	}

	if(isset($_SESSION['m']['acces']) && ($_SESSION['m']['acces'] > 10 OR $_SESSION['m']['plop'] == 1)) // Si il est déjà connecté à la modération
	{
		avert(bbCode("Vous êtes d&eacute;j&agrave; connecté."));
		include('pied.php');
		exit;
	}
	
// Fin - Autorisation d'acces de page

// Debut - Traitement du Formulaire
	if(isset($_POST['pseudo']))
	{
		if(isset($_SESSION['m']['attente']) && $_SESSION['m']['attente'] == 100) // Si il est Admin
		{
			$reponse = mysql_query('SELECT * FROM admin WHERE idPseudo=' . $_SESSION['m']['id']);
			$result = mysql_num_rows($reponse);
			
			if($result > 0)
			{
				$reponse = mysql_query('
				SELECT membres.passe AS `passeN`, membres.pseudo, membres.email, admin.passe AS `passeM`
				FROM membres
				INNER JOIN admin ON admin.idPseudo = membres.id
				WHERE membres.id = '.$_SESSION['m']['id'].' AND admin.idPseudo = ' . $_SESSION['m']['id'] . '
				');
				$donnees = mysql_fetch_assoc($reponse);
				
				if(strtolower($donnees['pseudo']) == strtolower($_POST['pseudo']))
				{
					if($donnees['passeM'] == $_POST['passeM'])
					{
						$_SESSION['m']['acces'] = 100;
						$_SESSION['m']['plop'] = 1;
					}
					else
					{
						avert('Infos incorrects !');
					}
				}
				else
				{
					avert('Infos incorrects !');
				}
			}
		}
		elseif(isset($_SESSION['m']['attente']) && $_SESSION['m']['attente'] == 95) // Si il est Directeur
		{
			$reponse = mysql_query('SELECT * FROM directeur WHERE idPseudo=' . $_SESSION['m']['id']);
			$result = mysql_num_rows($reponse);
			
			if($result > 0)
			{
				$reponse = mysql_query('
				SELECT membres.passe AS `passeN`, membres.pseudo, membres.email, directeur.passe AS `passeM`
				FROM membres
				INNER JOIN directeur ON directeur.idPseudo = membres.id
				WHERE membres.id = '.$_SESSION['m']['id'].' AND directeur.idPseudo = ' . $_SESSION['m']['id'] . '
				');
				$donnees = mysql_fetch_assoc($reponse);
				
				if(strtolower($donnees['pseudo']) == strtolower($_POST['pseudo']))
				{
					if($donnees['passeM'] == $_POST['passeM'])
					{
						$_SESSION['m']['acces'] = 95;
						$_SESSION['m']['plop'] = 1;
					}
					else
					{
						avert('Infos incorrects !');
					}
				}
				else
				{
					avert('Infos incorrects !');
				}
			}
		}
		elseif(isset($_SESSION['m']['attente']) && $_SESSION['m']['attente'] == 98) // Si il est Codeur
		{
			$reponse = mysql_query('SELECT * FROM codeur WHERE idPseudo=' . $_SESSION['m']['id']);
			$result = mysql_num_rows($reponse);
			
			if($result > 0)
			{
				$reponse = mysql_query('
				SELECT membres.passe AS `passeN`, membres.pseudo, membres.email, codeur.passe AS `passeM`
				FROM membres
				INNER JOIN codeur ON codeur.idPseudo = membres.id
				WHERE membres.id = '.$_SESSION['m']['id'].' AND codeur.idPseudo = ' . $_SESSION['m']['id'] . '
				');
				$donnees = mysql_fetch_assoc($reponse);
				
				if(strtolower($donnees['pseudo']) == strtolower($_POST['pseudo']))
				{
					if($donnees['passeM'] == $_POST['passeM'])
					{
						$_SESSION['m']['acces'] = 98;
						$_SESSION['m']['plop'] = 1;
					}
					else
					{
						avert('Infos incorrects !');
					}
				}
				else
				{
					avert('Infos incorrects !');
				}
			}
		}
		elseif(isset($_SESSION['m']['attente']) && $_SESSION['m']['attente'] == 90) // Si il est Super Modo
		{
			$reponse = mysql_query('SELECT * FROM supermodo WHERE idPseudo=' . intval($_SESSION['m']['id']));
			$result = mysql_num_rows($reponse);
			
			if($result > 0)
			{
				$reponse = mysql_query('
				SELECT membres.passe AS `passeN`, membres.pseudo, membres.email, supermodo.passe AS `passeM`
				FROM membres
				INNER JOIN supermodo ON supermodo.idPseudo = membres.id
				WHERE membres.id = '.$_SESSION['m']['id'].' AND supermodo.idPseudo = ' . $_SESSION['m']['id'] . '
				');
				$donnees = mysql_fetch_assoc($reponse);
				
				if(strtolower($donnees['pseudo']) == strtolower($_POST['pseudo']))
				{
					if($donnees['passeM'] == $_POST['passeM'])
					{
						$_SESSION['m']['acces'] = 98;
						$_SESSION['m']['plop'] = 1;
					}
					else
					{
						avert('Infos incorrects !');
					}
				}
				else
				{
					avert('Infos incorrects !');
				}
			}
		}
		else // Sinon il est modo :noel:
		{
			$reponse = mysql_query('SELECT * FROM moderateurs WHERE idPseudo=' . $_SESSION['m']['id']);
			$result = mysql_num_rows($reponse);
			
			if($result > 0)
			{
				$reponse = mysql_query('
				SELECT membres.passe AS `passeN`, membres.pseudo, membres.email, moderateurs.idForum, moderateurs.passe AS `passeM`
				FROM membres
				INNER JOIN moderateurs ON moderateurs.idPseudo = membres.id
				WHERE membres.id = '.$_SESSION['m']['id'].'
				');
				
				while($donnees = mysql_fetch_assoc($reponse))
				{
					if(strtolower($donnees['pseudo']) == strtolower($_POST['pseudo']))
					{
						if($donnees['passeM'] == $_POST['passeM'])
						{
							$_SESSION['m']['moderateur'][$donnees['idForum']] = 1 ;
							$_SESSION['m']['plop'] = 1;
						}
						else
						{
							avert('Infos incorrects !');
						}
					}
					else
					{
						avert('Infos incorrects !');
					}
				}
			}
		}
	}
	
	if(isset($_SESSION['m']['plop'])) // Si il est bien admin, codeur, directeur, etc.. :hap:
	{
		echo(bbCode("<div class='desavertissement'>Connexion r&eacute;ussi ! :bave:</div>"));
		include('pied.php');
		redirection('accueil.htm');
	}
// Fin - Traitement du Formulaire

echo'
<div class="bloc2">
	<h3>Connexion à l\'interface de modération</h3>
	<div class="texte">
		<form method="post">
			<center><b>Veuillez remplir les champs suivants pour accéder à l\'interface de modération des forums.</b></center><br /><br />
				<label>Pseudo :</label> <input readonly type="text" name="pseudo" value="' . $_SESSION['m']['pseudo'] . '" /><br />
				<label>Passe de Modération :</label> <input type="password" name="passeM" /><br />
			<center><input type="submit" value="Valider" class="bouton" /></center>
		</form>
	</div>
</div>
';

include('pied.php');
?>