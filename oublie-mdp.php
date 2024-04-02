<?php
include('base.php');

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>' . TITRE_FORUM . ' : Oublie Password</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" media="screen" type="text/css" href="css/bleu.css" />
		<script type="text/javascript" src="js.js"></script>
	</head>
    <body class="apercu">
	';

// Vérification de l'existence de la variable ; on vérifie aussi qu'elle n'est pas vide
if((isset($_POST['email'])) && (!(empty($_POST['email']))))
{
   $mail = htmlspecialchars($_POST['email'], ENT_QUOTES); // on sécurise la variable avant
   //On compte le nombre d'entrée(s) dans la table où le champ "mail" vaut $mail
   $nombremail = mysql_result(mysql_query("SELECT COUNT(*) FROM membres WHERE email = '".$mail."'"), 0);
 
   if ($nombremail!= 0)
   {
 
      $tablettre = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
 
      mt_srand((float) microtime()*10000);
 
 
        $a = mt_rand(0, 9);
        $b = mt_rand(0, 9);
        $c = mt_rand(0, 9);
        $d = mt_rand(0, 9);
        $e = mt_rand(0, 9);
        
        $f = mt_rand(0, 26);
        $g = mt_rand(0, 26);
        $h = mt_rand(0, 26);
        $i = mt_rand(0, 26);
        $j = mt_rand(0, 26);
 
        $motaleatoire =  $a.$tablettre[$f].$b.$tablettre[$g].$c.$tablettre[$h].$d.$tablettre[$i].$e.$tablettre[$j];
 
 echo '
 <br />
<div>
    <div id="bloc3">
   <h3>ENVOI DE VOTRE MOT DE PASSE</h3>
   <div class="texte">
        <center>Un mail de vérification vient de vous être envoyé.</center><br /><center>Consultez votre boîte mail, des explications vous seront fournies.</center></b>
	</form> 
   </div>
   </div>
    <br />
   <center><img src="images/croixrouge.gif"> <a href="javascript:window.close()"">Fermer la fenêtre</a></center>
   ';
 
        $message = '<h5>Bonjour !<br>Vous avez demandé à redéfinir votre mot de passe. Veuillez cliquer sur le lien de vérification ci-dessous afin qu\'un nouveau mot de passe soit défini.<br><br></h5><a>' . $adresseSite . 'verif-mdp.php?e='.$mail.'&v='.$motaleatoire.'</a><br><br><br><h6>Ce mail a été envoyé automatiquement, veuillez ne pas y répondre.<br>Si ce mail vous a été envoyé alors que vous n\'en avez pas fait la demande, ne vous inquiétez pas, personne à part vous ne pourra redéfinir votre mot de passe.</h6>';
 
        $message = '<html><head></head><body>' . $message . '</body></html>';
                                       
        $sujet = TITRE_FORUM . ' : Mail de vérification';
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $adresseSite . '';
 
        mail($mail, $sujet, $message, $headers);
 
        mysql_query('UPDATE membres SET verif="' . $motaleatoire . '" WHERE email="' . $mail .'"') OR DIE (mysql_error());
   }
   else
   {
   ?>
   <br />
  		<div>
    <div class="bloc2">
   <h3>ENVOI DE VOTRE MOT DE PASSE</h3>
   <div class="texte">
   <br />
        <center>Cette adresse email n'est pas enregistrer sur le site.</center>
		<br />

   </form>
   </div>
   </div>
   <br />
   <center><img src="images/moderation/croixrouge.gif"> <a href="javascript:window.close()"">Fermer la fenêtre</a></center>
   <?php
   }
 
}
else
{
   ?>
   <br />
   <div class="bloc2">
   <h3>ENVOI DE VOTRE MOT DE PASSE</h3>
   <div class="texte">
   <center>Veuillez saisir votre email pour redéfinir votre mot de passe.</center><br />
   <form method="post" action="oublie-mdp.php">
   <center><b>Email :</b> <input type="text" name="email"></center></p>
   					<p><center><input type="submit" value="R&eacute;cup&eacute;rer mon mot de passe" /></center></p><br />
   </form>
   </div>
   </div>
   <br />
   <center><img src="images/croixrouge.gif"> <a href="javascript:window.close()">Fermer la fenêtre</a></center>
   <?php

}





?>