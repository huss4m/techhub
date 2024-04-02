<?php
include('base.php');

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>• Apercu de votre message ! •</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" media="screen" type="text/css" name="Design" href="css/message.css" />
	</head>
     <body style="background:white; padding: 10px;">
	';
		if(isset($_POST['message']))
		{
			if(!empty($_POST['message']))
			{		
				
				echo'<div class="message" id="message_' . $donnees['id'] . '">
					<div class="table">
						<div class="tr">
							<div class="msg2">
								<div class="td gauche">
									<div class="pseudo">
										' . $pseudo  . ' <a href="cdv-' . $donnees['pseudo'] . '.html" title="Voir le profil" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/message/bt_forum_profil.gif" width="11" height="12"></a>
									</div>
									<div class="img">
										' . $affichAva . '
									</div>
									<div class="nbPost">
										' . $imgI . ' ' . $citer . ' ' . $editer . ' <a href="mp.php?action=ecrire&amp;pseudo=' . $donnees['idPseudo'] . '"><img src="images/mp.png"></a><br />
										' . ($COLP->afficher($donnees['acces'], $grade)) . '<br />
										' . $affichJour . '
										' . $affichPost . '<br />
										' . $affichArgent . '
										' . $Sip . '
									</div>
								</div>
								<div class="td droite">
									<div class="date">
									
									</div>
									<div class="fonctions">
									<b>' . $armev5 . ' ' . $supprimer_message  . ' ' . $avertir . ' Post&eacute; ' . $mobile .  ' le ';
									
									$timestamp = $donnees['timestamp'];
									
									// Tableau des mois en français
									$mois_fr = array('', 'janvier', 'f&eacute;vrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'd&eacute;cembre');
									
									// Tableau de la date
									// $jour_modif = array('', '1er', 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
									
									// On extrait la date du jour
									list($jour, $mois, $annee) = explode('/', date('d/n/Y', $timestamp));
									echo $jour . ' ' . $mois_fr[$mois] . ' ' . $annee; 
									echo ' &agrave; ';
									echo date('H\:i\:s', $timestamp);
										
									echo '
									' . $kick . '' .  $bann . ' ' .  $bannIp . '</b> <span style="float: right;">' . $affichRang . '</span>
									<div class="post" width="480">
									' . nl2br(bbCode(stripslashes(htmlspecialchars($_POST['message'])))) . '
									<div class="lienPermanent">
									<br /><br /></div>
								  </div>
								</div>
							</div>
						 </div>
					 </div>
				   </div>
				</div>';
				
				
			}
			else
			{
				echo'Le message est vide';
			}
		}
		
		echo '
	</body>
</html>	 
';
?>