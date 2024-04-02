<?php
include('base.php');

// Vérifions que l'url n'a pas été modifié par le visiteur
if(!isset($_GET['id']) OR !ctype_digit($_GET['id']))
{
	include('tete.php');
	avert('Erreur 404 : Ce lien existe plus/pas ou alors est redirigé autre part.</b>');
	include('pied.php');
	exit;
}



include('tete.php');


if(!isset($_SESSION['m']['id'])) {
	avert ('Vous devez être connecté pour voir ce cours!');
	exit;
}

$req = mysql_query('SELECT * from inscriptionCours WHERE idPseudo='.$_SESSION['m']['id'].' AND idCours='.$_GET['id']);
$donnees = mysql_fetch_assoc($req);

if($donnees == NULL) {

	avert ('Vous devez vous inscrire au cours. <br><center><a href="liste-cours.php">Retour</a></center>');
	exit;	
}


if(isset($_GET['id']) AND ctype_digit($_GET['id']))
{
			$retour = mysql_query('SELECT * FROM cours WHERE id=' . $_GET['id'] . '') ;
			$donnees = mysql_fetch_assoc($retour);
			
				echo '
                    <script type="text/javascript">
					//<![CDATA[
                    <!--
                    function resize(imageposte)
                    {
                    var resizeLargeur = imageposte.offsetWidth ;
                    var resizeHauteur = imageposte.offsetHeight ;


                    // { Début - Redimensionnement si trop grand
                    if(resizeLargeur > 70)
                    {
                    imageposte.width = 70 ;
                    resizeLargeur -= 70 ;
                    }

                    if(resizeHauteur > 70)
                    {
                    imageposte.height = 70 ;
                    resizeHauteur -= 70 ;
                    }
                    // } Fin - Redimensionnement si trop grand


                    // { Début - Redimensionnement si écart plus grand que 150px
                    var differance = resizeLargeur - resizeHauteur ;
                    if(differance > 70 || differance < -70)
                    {
                    var largeurFinale = 100 ;
                    var hauteurFinale = 100 ;

                    if(differance > 0) // La largeur est plus grande que la hauteur
                    {
                    // Nb de fois que la largeur a été retranchée
                    var nbRetranche = Math.round(imageposte.width / 140);
                    hauteurFinale -= (25 * nbRetranche);
                    }
                    else // La hauteur est plus grande que la largeur
                    {
                    var nbRetranche = Math.round(imageposte.height / 140);
                    largeurFinale -= (25 * nbRetranche);
                    }

                    if(largeurFinale > 30 && hauteurFinale > 30)
                    {
                    imageposte.width = largeurFinale ;
                    imageposte.height = hauteurFinale ;
                    }
                    }
                    // } Fin - Redimensionnement si écart trop grand
                    }
                    // -->
                    //]]>
                    </script>
				';
	echo '
	<div class="desavertissement">
	<center><b>' . nl2br(text_i($donnees['titre'])) . '</b></center>
	</div><br />
	<div class="bloc2">
			<h3>Introduction</h3>
			<div class="texte">
			<span style="float: left;"><img src="' . nl2br(text_i($donnees['lien'])) . '" width="170px" height="88px"></span>
			<div class="readIntro">' . nl2br(bbCode(text_i($donnees['intro']))) . '<br /><br /><br /><br /><br /><br /></div>
			</div>
		</div><br />
		<div class="bloc2">
		<h3>Cours: '.$donnees['titre'].' </h3>
		<div class="texte">
			' . nl2br(bbCode(text_i($donnees['contenu']))) . '<br /><br />
		
		<span style="float: left;"><b>Ecrit par <font color="red">' . nl2br(text_i($donnees['auteur'])) . '</font></b></span><br />
		</div>
		</div>
			<br /><br />
			';
	}




include('pied.php');
?>
