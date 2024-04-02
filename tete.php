<?php

$url = $_SERVER['REQUEST_URI'];
//Forçage de l'utf8 via php
header('Content-type: text/html; charset=UTF-8'); 
	
if(stristr($url, 'tete.php') || stristr($url, 'tete.html') || stristr($url, 'tete.htm'))
{
	exit("Accès interdit");
}

// Début si bannis IP
$reponse = mysql_query('SELECT * FROM bannIp WHERE ip="' . $_SERVER['REMOTE_ADDR'] . '"') OR die(mysql_error());
$result = mysql_num_rows($reponse);

if((int) $result != 0)
{
	setcookie('lol', 1, (time() + 365*24*3600));
	echo 'Vous avez t banni du site.';
	exit;
}

if(isset($_COOKIE['lol']) && $_COOKIE['lol'] == 1)
{
	echo 'Vous avez ete banni du site.';
	exit;
}




?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title><?php echo(TITRE_FORUM); ?></title>
		<link rel="shortcut icon" type="image/png" href="favicon.png" /> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/ColorPicker.css" media="all"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="css/prism.css" />
		<script src="js/prism.js"></script>
		<link rel="stylesheet" media="screen" type="text/css" href="css/noir.css" />
		<script type="text/javascript" src="js/favoris.js"></script>
		<!-- <script type="text/javascript" src="js/styleswitcher.js"></script> -->
		<script type="text/javascript" src="js.js"></script>
				<script type="text/javascript" src="js/CP_Class.js"></script>
		<script type="text/javascript">
window.onload = function()
{
 fctLoad();
}
window.onscroll = function()
{
 fctShow();
}
window.onresize = function()
{
 fctShow();
}
</script>
	</head>
    <body>



<?php
echo'
 
   <div id="contenu_corps">
	 <div id="banniere">
		<img src="images/banniere.png"></img>
	 </div>
	   <div class="separate"></div>';

	   // Début - menu déroulant
		if(isset($_SESSION['m']['pseudo']))
		{
			// On compte le nombre de messages non lus et dont le destinataire est le membre actuellement connecté
			$nbr_non_vus = mysql_unbuffered_query("SELECT COUNT(*) AS nbre FROM mp WHERE  destinataire= '" . secure($_SESSION['m']['pseudo']) . "' AND vu='0' AND (efface='0' OR efface='2')") or die(mysql_error());

			// On en fait un array
			$dd = mysql_fetch_assoc($nbr_non_vus);
						
			if($dd['nbre'] > 0)
			{
				$dbut = '<span style="color: red;">';
				$dfin = '</span>';
			}
			
			if($dd['nbre'] > 0)
			{
				$nbMp = '<span style="color: red;">' . $dd['nbre'] . ' MP</span>';
			}
			$menu = '
           <a href="#">Membres ' . $nbMp . '</a>
           <ul>
			<li><a href="option.htm"">Mon Compte</a></li>
			<li><a href="cdv-' . $_SESSION['m']['pseudo'] . '.htm"" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">Mon Profil</a></li>
			<li><a href="mp.php">' . $dbut . 'Messagerie (' . $dd['nbre'] . ')' . $dfin . '</a></li>
			<li><a href="creer-projet.php">Créer un Projet</a></li>
			<li><a href="mes-projets.php">Mes Projets</a></li>
			<li><a href="liste-membre.php">Liste Membres</a></li>
           </ul>
			';
		}
		
   echo'
    <div class="bandeau" >
        <ul id="menu">
          <li>
           <a href="#">Le Site</a>
           <ul>
			<li><a href="accueil.htm">Accueil</a></li>
			<li><a href="liste-forums.htm">Forums</a></li>
			<li><a href="charte.htm">Charte</a></li>
			<li><a href="faq.php">FAQ</a></li>
			<li><a href="mailform.htm">Le Staff</a></li>
			<li><a href="stats.htm">Statistiques</a></li>
           </ul>
          </li>
		  
          <li>
           <a href="#">Article</a>
           <ul>
			<li><a href="index.htm">News</a></li>
			<li><a href="liste-cours.php">Cours</a></li>
			<li><a href="liste-projet.php">Projets</a></li>
           </ul>
          </li>
		  
          <li>
           ' . $menu . '
          </li>';
				
		if(isset($_SESSION['m']['pseudo']))
		{
			if($_SESSION['m']['acces'] == 100)
			{
			 echo'
			<li>
			  <a href="#">Admin</a>
			   <ul>
				 <li><a href="panel.htm?accueil">Panel</a></li>
				 <li><a href="rediger.htm?ad=index">Rediger</a></li>
			     <li><a href="listes.htm">Liste</a></li>
			   </ul>
			  </li>';
			}
		}

		if(isset($_SESSION['m']['id']))
		{
			$reponse = $bdd->sql_query('SELECT * FROM newseur WHERE idPseudo=' . $_SESSION['m']['id']);
			$result = $bdd->sql_count($reponse);
					
			if($result > 0)
			{
			 echo'
			<li>
			  <a href="#">Newseur</a>
			   <ul>
				 <li><a href="rediger.htm?ad=index">Rediger</a></li>
			     <li><a href="listes.htm">Liste</a></li>
			   </ul>
			  </li>';
			}
		}

		if(isset($_SESSION['m']['pseudo']))
		{
			if($_SESSION['m']['acces'] == 98)
			{
			 echo'
			<li>
			  <a href="#">Codeur</a>
			   <ul>
				 <li><a href="panel.htm?accueil">Panel</a></li>
				 <li><a href="rediger.htm?ad=index">Rediger</a></li>
			     <li><a href="listes.htm">Liste</a></li>
			   </ul>
			  </li>';
			}
		}
		
		if(isset($_SESSION['m']['pseudo']))
		{
			if($_SESSION['m']['acces'] == 95)
			{
			 echo'
			<li>
			  <a href="#">Directeur</a>
			   <ul>
				 <li><a href="panel.htm?accueil">Panel</a></li>
				 <li><a href="rediger.htm?ad=index">Rediger</a></li>
			     <li><a href="listes.htm">Liste</a></li>
			   </ul>
			  </li>';
			}
		}
		
		if(isset($_SESSION['m']['pseudo']))
		{
			if($_SESSION['m']['acces'] == 90)
			{
			 echo'
			<li>
			  <a href="#">Super Modo</a>
			   <ul>
				 <li><a href="rediger.htm?ad=index">Rediger</a></li>
			     <li><a href="listes.htm">Liste</a></li>
			   </ul>
			  </li>';
			}
		}

		if(isset($_SESSION['m']['acces']))
		{
			if($_SESSION['m']['acces'] == 10) // Si on est membre, on ne vois rien de tout cela
			{

				if(isset($_SESSION['m']['id'])) // Si on est connecter sur une session membre et que notre pseudo est dans la table moderateurs, alors on affiche le lien	
				{
					$reponse = mysql_query('SELECT * FROM moderateurs WHERE idPseudo=' . $_SESSION['m']['id']); // On regarde si le pseudo est dans la table moderateurs
					$result = mysql_num_rows($reponse);
						
					if($result > 0) // S'il est bien dans la table, on affiche le lien
					{
					echo'
					<li>
						<a href="#">Modo</a>
						<ul>
							<li><a href="moderation.php">Connexion</a></li>
						</ul>
					</li>
					';
					}
				}

				if(isset($_SESSION['m']['id']))	// Si on est connecter sur une session membre et que notre pseudo est dans la table admin, alors on affiche le lien
				{
					$reponse = mysql_query('SELECT * FROM admin WHERE idPseudo=' . $_SESSION['m']['id']); // On regarde si le pseudo est dans la table admin
					$result = mysql_num_rows($reponse);
				
					if($result > 0) // S'il est bien dans la table, on affiche le lien
					{
						echo '
					<li>
						<a href="#">Admin</a>
							<ul>
							<li><a href="moderation.php">Connexion</a></li>
							</ul>
					</li>
						';				
					}
				}
				
				if(isset($_SESSION['m']['id']))	// Si on est connecter sur une session membre et que notre pseudo est dans la table supermodo, alors on affiche le lien
				{
					$reponse = mysql_query('SELECT * FROM supermodo WHERE idPseudo=' . $_SESSION['m']['id']); // On regarde si le pseudo est dans la table supermodo
					$result = mysql_num_rows($reponse);
				
					if($result > 0) // S'il est bien dans la table, on affiche le lien
					{
						echo '
					<li>
						<a href="#">Super Modo</a>
							<ul>
							<li><a href="moderation.php">Connexion</a></li>
							</ul>
					</li>
						';				
					}
				}
				
				if(isset($_SESSION['m']['id']))	// Si on est connecter sur une session membre et que notre pseudo est dans la table directeur, alors on affiche le lien
				{
					$reponse = mysql_query('SELECT * FROM directeur WHERE idPseudo=' . $_SESSION['m']['id']); // On regarde si le pseudo est dans la table directeur
					$result = mysql_num_rows($reponse);
				
					if($result > 0) // S'il est bien dans la table, on affiche le lien
					{
						echo '
					<li>
						<a href="#">Directeur</a>
							<ul>
							<li><a href="moderation.php">Connexion</a></li>
							</ul>
					</li>
						';				
					}
				}

				if(isset($_SESSION['m']['id']))	// Si on est connecter sur une session membre et que notre pseudo est dans la table codeur, alors on affiche le lien
				{
					$reponse = mysql_query('SELECT * FROM codeur WHERE idPseudo=' . $_SESSION['m']['id']); // On regarde si le pseudo est dans la table codeur
					$result = mysql_num_rows($reponse);
				
					if($result > 0) // S'il est bien dans la table, on affiche le lien
					{
						echo '
					<li>
						<a href="#">Codeur</a>
							<ul>
							<li><a href="moderation.php">Connexion</a></li>
							</ul>
					</li>
						';				
					}
				}
			}
		}

		if(isset($_SESSION['m']['pseudo']))
		{
			 echo'
			  <li>
			   <a href="deconnexion.htm"">D&eacute;connexion</a>
			  </li>';
		}
		else
		{
			 echo'
			  <li>
			   <a href="connexion.htm">Connexion</a>
			  </li>
			  
			  <li>
			   <a href="inscription.htm?etape=1">Inscription</a>
			  </li>';
		}
    echo'
	</ul>
	<span style="float: right;">' . $skin . '</span>
	 </div>
	  <br /><br />
		';
        // Fin - menu déroulant
?>
		
		</b></center>

                <div class="contenu_2_H">
				
                        <div class="contenu_H">
								
						<span style="float: right;" id="menudroite">
						
						<?php
							// Début - Bloc dernière news
							echo'
								<div class="bloc4">
									<h3>
										<img src="images/imgs/news.png"/> Derni&egrave;re News
									</h3>
									<div class="texte">';
									$nombreDeNews = 1; // Nombre de News a afficher
									$retour = mysql_query('SELECT COUNT(*) AS nb_News FROM news WHERE statut=\'1\'');
									$donnees = mysql_fetch_array($retour);
									$totalDesNews = $donnees['nb_News'];
									$premiereNewsAafficher = '0' * $nombreDeNews;
									$reponse = mysql_query('SELECT * FROM news WHERE statut=\'1\' ORDER BY id DESC LIMIT ' . $premiereNewsAafficher . ', ' . $nombreDeNews);
									$i = 0;
									while($donnees = mysql_fetch_array($reponse))
									{
										$mots_complets = $donnees['intro']; // Affichage de l'introduction
										$nb_mots = 4; // Nombre de mot
										$mot_courts = debutchaine($mots_complets, $nb_mots);
										$titres_complets = $donnees['titre']; // Affichage du Titre
										$nb_titres = 1; // Nombre de mot
										$titre_courts = debutchaine($titres_complets, $nb_titres);
										
										echo'
										<center><font color="green"><b>' . nl2br(text_i($donnees['titre'])) . '</b></font></center>
										<div class="separate"></div>
										<img style="float: left;" src="' . nl2br(text_i($donnees['lien'])) . '" width="48px" height="48px"> 
										<span style="float: center; margin-left: 10px;">' . nl2br(bbCode(text_i($mot_courts))) . '</span>
										<br /><br /><span style="float: center; margin-left: 10px;"><img src="images/puce_base.gif"> <a href="news.php?id=' . $donnees['id'] . '">Lire la suite</a></span>
										<div class="separate"></div>
										<b>Ecrit par <font color="red">' . nl2br(text_i($donnees['auteur'])) . '</font></b><span style="float: right;"><b>Le ' . date('d/m/Y\\', $donnees['timestamp']) . '</b></span>
										</div>
										</div><br />
										';
										$i++;
									}

						if($totalDesNews == 0)
						{
							 echo'
							 <i>Aucune News pour le moment.</i></div></div><br />';
						}
						// Fin - Bloc dernière news
						?>
				
			<div class="bloc4">
				<?php
				$htroa = '<img src="images/imgs/forum.png"/> Forums principaux';
				echo'<h3>' . $htroa . '</h3>
				<div class="texte">
					<span id="forumsL">';
						$reponse = mysql_unbuffered_query('SELECT id, titre FROM forum WHERE statut=\'0\' ORDER BY titre ASC');
						
						while($donnees = mysql_fetch_assoc($reponse))
						{
							echo'<ul><li> <a href="' . have_url($donnees['id'], 1 , FORUM_URL, $donnees['titre']) .'">' . text_i($donnees['titre']) . '</a></li></ul>';
						}			
					echo'
                          <br />
					  
				</div>
				
			</div>
			<br />';
				$requete = mysql_query('
				SELECT 
				connectes.idPseudo AS `idDuPseudo`, 
				connectes.acces AS `acces`,
				membres.id AS `idMembre`, 
				membres.pseudo AS `pseudo`
				FROM connectes 
				INNER JOIN membres 
				ON membres.id = connectes.idPseudo
				ORDER BY pseudo
				');
				$reponse = mysql_query("SELECT COUNT(*) AS nbVisiteurs FROM connectes WHERE idPseudo='0'");
				$donnees = mysql_fetch_assoc($reponse);
				$nbVisiteurs = $donnees['nbVisiteurs'];
											
				$reponse2 = mysql_query("SELECT COUNT(*) AS nbMembresCo FROM connectes WHERE idPseudo > 0");
				$donnees2 = mysql_fetch_assoc($reponse2);
				$nbMembresCo = $donnees2['nbMembresCo'];	
			echo'
			 <div class="bloc4">
                <h3>
                   <img src="images/imgs/user.png"/> Membres connectés (' . $nbMembresCo . ')
                </h3>
                  <div class="texte">';
								
							$i = 0;
							while($info = mysql_fetch_assoc($requete))
							{
								if($i == $nbMembresCo - 1)
								{
									$virgule = '';
								}
								else
								{
									$virgule = ', ';
								}

								if($info['acces'] == 100) // Admin
								{
									$pseudo = ' <strong>' . ($COLP->afficher(100, $info['pseudo'])) . '</strong>';
								}
								elseif($info['acces'] == 95) // Directeur
								{
									$pseudo = ' <strong>' . ($COLP->afficher(95, $info['pseudo'])) . '</strong>';
								}
								elseif($info['acces'] == 90) // Super modo
								{
									$pseudo = ' <strong>' . ($COLP->afficher(90, $info['pseudo'])) . '</strong>';
								}
								elseif($info['acces'] == 50) // Modérateur
								{
									$pseudo = ' <strong>' . ($COLP->afficher(50, $info['pseudo'])) . '</strong>';
								}
								elseif($info['acces'] == 45) // Pubbeur
								{
									$pseudo = ' <strong>' . ($COLP->afficher(45, $info['pseudo'])) . '</strong>';
								}
								elseif($info['acces'] == 98) // Codeur
								{
									$pseudo = ' <strong>' . ($COLP->afficher(98, $info['pseudo'])) . '</strong>';
								}
								else // Membre
								{
									$pseudo = ' <strong>' . ($COLP->afficher(10, $info['pseudo'])) . '</strong>';
								}
								echo '<a href="cdv-' . $info['pseudo'] . '.htm"" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $pseudo . '</a>'.$virgule;
								$i++;
							}
					
				if($nbVisiteurs > 1)
				{
					$s = 's';
				}
				else
				{
					$s = '';
				}
				if($nbVisiteurs > 0)
				{
					echo ' et ' . $nbVisiteurs . ' visiteur'. $s;
				}
				
				if(isset($donnees['nbMembresCo']) AND $donnees['nbMembresCo'] > 10)
				{
                                    echo '<br /><img src="images/defaut/puce_base.gif" alt="Puce base des connectés" /> <a href="connecte.htm"">Voir la suite</a>(' . $donnees['nb'] . ' membre'.$s.' connecté'.$s.')';
				}
				?>
				</div>
			</div>
			<br />
			<?php
			include('annonce.php');
															
?><br />

</div>

</span> 

		<div class="intCorps"> 
		<?php 
			
			$temps = microtime(true);			?>	