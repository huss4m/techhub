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
	avert ('Vous devez être connecté.');
	exit;
}



if(isset($_GET['id']) AND ctype_digit($_GET['id']))
{
			$retour = mysql_query('SELECT * FROM projets WHERE id="' . $_GET['id'] . '"') ;
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
		<h3>Projet: '.$donnees['titre'].' </h3>
		<div class="texte">
			' . nl2br(bbCode(text_i($donnees['contenu']))).' <br><br>
			
		</div></div>
		
		<div class="bloc2">
		
			<h3>Membres du projet:</h3>
			<div class="texte">
			';
			
			$req = mysql_query('SELECT * from joindreProjet WHERE idProjet='.$_GET['id']);
			while($donnees2 = mysql_fetch_assoc($req)) {

				$req2 = mysql_query('SELECT pseudo FROM membres WHERE id = '.$donnees2['idPseudo']);
				$membresProjet = mysql_fetch_assoc($req2);
				echo '- '.$membresProjet['pseudo'].'<br>';
				
			}
			if($_SESSION['m']['pseudo'] == $donnees['leader']) {
				echo '<button class="bouton" onclick="window.location.href=\'ajouter.php?id='.$_GET['id'].'\';">Ajouter Membre</button>';
			}
			
			echo '<br /><br />
		
		<span style="float: left;"><b>Chef du projet: <font color="red">' . nl2br(text_i($donnees['leader'])) . '</font></b></span><br />
		</div>
		</div>
		
		<div class="bloc2">
		<h3>Dépot Git</h3>
		<div class="texte">
		<a href="'.$donnees['git'].'">'.$donnees['git'].'</a>';
		
		
		
		
		echo'</div></div>
			<br /><br />
			';
	}




include('pied.php');
?>
