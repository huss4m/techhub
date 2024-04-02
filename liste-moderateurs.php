<?php 
include ('base.php');
include ('tete.php');

echo '
	<div class="bloc2">
		<h3>Modérateurs </h3>
		<div class="texte">';
		
		$reponse = mysql_query('SELECT id, pseudo FROM membres WHERE acces=50');
echo'
			<b><font color="red">Voici les modérateurs et leurs forums:</font></b>
<div class="separate"></div>
			<ul>
			';
			 
			while($donnees = mysql_fetch_assoc($reponse))
			{
				echo '<li><a href="cdv-' . $donnees['pseudo'] . '.html">' . $donnees['pseudo'] . '</a></li>';
			}
			
			echo '</ul><br /></div>
                    </div>';
					
		include ('pied.php');
?>