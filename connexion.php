<?php
include('base.php');

// Vérifions qu'il ne soit pas déjà connecté
if(isset($_SESSION['m']['pseudo']))
{
	include('tete.php');
	avert('Vous êtes déjà connecté');
	include('pied.php');
	exit;
}

// Debut - Traitement formulaire de connexion
	if(!empty($_POST['pseudo']) AND !empty($_POST['passe']))
	{
		// Debut - Vérification nb de tentative pour l'antibrute force.
			$existence_ft = ''; // On initialise $existence_ft

			// Si le fichier existe, on le lit
			if(file_exists('antibrute/' . $_POST['pseudo'] . '.tmp'))
			{
				// On ouvre le fichier
				$fichier_tentatives = fopen('antibrute/' . $_POST['pseudo'] . '.tmp', 'r+');

				// On récupère son contenu dans la variable $infos_tentatives
				$contenu_tentatives = fgets($fichier_tentatives);

				// On découpe le contenu du fichier pour récupérer les informations
				$infos_tentatives = explode(';', $contenu_tentatives);

				// Si la date du fichier est celle d'aujourd'hui, on récupère le nombre de tentatives
				if($infos_tentatives[0] == date('d/m/Y'))
				{
					$tentatives = $infos_tentatives[1];
				}
				
				// Si la date du fichier est dépassée, on met le nombre de tentatives à 0 et $existence_ft à 2
				else
				{
					$existence_ft = 2;
					$tentatives = 0; // On met la variable $tentatives à 0
				}
			}
			// Si le fichier n'existe pas encore, on met la variable $existence_ft à 1 et on met les $tentatives à 0
			else
			{
				$existence_ft = 1;
				$tentatives = 0;
			}
		// Fin - Vérification nb de tentatives pour l'anti brute force

		// Debut - Savoir si on peut se connecter
			if($tentatives < 20)
			{
				// Sécurité des variables
				$pseudo = secure($_POST['pseudo']);
				$passe = secure($_POST['passe']);
				
				// Pour vérifier si le pseudo existe
				$reponse = mysql_query('SELECT COUNT(*) AS nbEntrees FROM membres WHERE pseudo="' . $pseudo . '"');
				$donnees = mysql_fetch_array($reponse);
				$nbEntrees = $donnees['nbEntrees'];
				
				if($nbEntrees > 0)
				{
					// Le pseudo existe on récupère son id, pseudo, passe et accès
					$reponse = mysql_query('SELECT id, pseudo, passe, acces, valide FROM membres WHERE pseudo="' . $pseudo . '"');
					$donnees = mysql_fetch_array($reponse);
					
					if($donnees['valide'] != 0)
					{
						if($donnees['acces'] != 1)
						{
							if(md5($passe) == secure($donnees['passe']))
							{
								// Le membre est connecté, on crée les variables SESSION
								$_SESSION['m']['id'] = secure($donnees['id']);
								$_SESSION['m']['pseudo'] = secure($pseudo);
								$_SESSION['m']['pseudo_mp'] = secure($donnees['pseudo']);
								$_SESSION['m']['acces'] = 10;
								$_SESSION['m']['attente'] = secure($donnees['acces']);

								header('Location: accueil.htm');
								exit;
							}
							else
							{
								// Si le fichier n'existe pas encore, on le créé
								if($existence_ft == 1)
								{
									$creation_fichier = fopen('antibrute/'.$donnees['pseudo'].'.tmp', 'a+'); // On créé le fichier puis on l'ouvre
									fputs($creation_fichier, date('d/m/Y').';1'); // On écrit à l'intérieur la date du jour et on met le nombre de tentatives à 1
									fclose($creation_fichier); // On referme
								}
								// Si la date n'est plus a jour
								elseif($existence_ft == 2)
								{
									fseek($fichier_tentatives, 0); // On remet le curseur au début du fichier
									fputs($fichier_tentatives, date('d/m/Y').';1 '); // On met à jour le contenu du fichier (date du jour;1 tentatives)
								}
								else
								{
									// Si la variable $tentatives est sur le point de passer à 30, on en informe l'administrateur du site
									if($tentatives == 100)
									{
										$email_administrateur = 'kartmille@hotmail.com';
				 
										$sujet_notification = '[Site] Un compte membre à atteint son quota';
				 
										$message_notification = 'Un des comptes a atteint le quota de mauvais mots de passe journalier :';
										$message_notification .= $donnees['pseudo'].' - '.$_SERVER['REMOTE_ADDR'].' - '.gethostbyaddr($_SERVER['REMOTE_ADDR']);
				 
										mail($email_administrateur, $sujet_notification, $message_notification);
									}

									fseek($fichier_tentatives, 11); // On place le curseur juste devant le nombre de tentatives
									fputs($fichier_tentatives, $tentatives + 1); // On ajoute 1 au nombre de tentatives
								}
								include('tete.php');
								avert('Vous n\'avez pas entré le bon pseudo et/ou mot de passe, veuillez réassayer.');
							}
						}
						else
						{
							include('tete.php');
							avert('Votre pseudo a été banni.');
						}
					}
					else
					{
						include('tete.php');
						avert('Ce pseudo n\'a pas été encore validé !');
					}
				}
				else
				{
					include('tete.php');
					avert('Ce pseudo n\'existe pas dans la BDD.');
				}
			}
			// S'il y a déjà eu 30 tentatives dans la journée, on affiche un message d'erreur
			else
			{
				include('tete.php');
				avert('Trop de tentatives d\'authentification aujourd\'hui.');
			}
		// Fin - Savoir si on peut se connecter

		// Si on a ouvert un fichier, on le referme (eh oui, il ne faut pas l'oublier)
		if($existence_ft != 1)
		{
		fclose($fichier_tentatives);
		}
	}
	else
	{
		include('tete.php');
	}
// Fin - Traitement formulaire de connexion
?>
<div class="bloc2">
	<h3>Connexion</h3>
	<div class="texte">
		<form method="post" >
			<label for="pseudo">Pseudo :</label> <input type="text" name="pseudo"/> <img src="images/puce_liste_orange.gif"> <a href="inscription.php?etape=1">Pas encore inscrits ?</a><br/>
			<label for="passe">Mot de passe :</label> <input type="password" name="passe"/> <img src="images/puce_liste_orange.gif"> <a href="oublie-mdp.htm">Mot de passe oublié ?</a><br/><br/>
			<br />
			<center><input type="submit" value="Connexion" class="bouton" /></center>
		</form>
	</div>
</div>
<?php
include('pied.php');
?>
