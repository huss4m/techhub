<?php
		
		include_once('base.php');
	include_once('sql.php');
		$nom = '';
		$f = '';
		$query = '';
		$fofo = 0;
		
		if(isset($_GET['forum']) AND ctype_digit($_GET['forum']))
		{
			$query = mysql_query('SELECT titre, id FROM forum WHERE id = ' .  $_GET['forum']);
			$forums = mysql_fetch_array($query);
			
			if(is_array($forums))
			{
				$nom = htmlspecialchars(stripslashes($forums['titre']));
				$f = 'du forum : ' . $nom;
				$numf = $forums['id'];
				$fofo = 1;
			}
		
		}
		
include('tete.php');

// Nombre de Topics
	$sweets = '';
	if($fofo)
	{
		$sweets = ' WHERE idForum = ' . $numf ;
	}
$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS nbTopic FROM topic' . $sweets);
$donnees = mysql_fetch_assoc($reponse);
$nbTopic = $donnees['nbTopic'];

	// Nombre de Messages 
	$query = 'SELECT COUNT(*) AS nbMessage FROM message';
	
	
		$query = 'SELECT COUNT(message.id) AS nbMessage, topic.idForum  FROM message INNER JOIN topic ON topic.id = message.idTopic WHERE topic.idForum = ' . $numf ;
	
	
	$reponse = mysql_unbuffered_query($query);
	$donnees = mysql_fetch_assoc($reponse);
	$nbMessage1 = $donnees['nbMessage'];
	$nbMessage = $nbMessage1;

	if(!$fofo)
	{
		// Nombre de Commentaires 
		$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS nbCom FROM commentaires');
		$donnees = mysql_fetch_assoc($reponse);
		$nbCom = $donnees['nbCom'];
		
		// Nombre de forum
		$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS forumNb FROM forum');
		$donnees = mysql_fetch_assoc($reponse);
		$nbForum = $donnees['forumNb'];


		// Nombre de Mp
		$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS Mp FROM mp  ');
		$donnees = mysql_fetch_assoc($reponse);
		$Mp = $donnees['Mp'];

		// Nombre de Chat
		$reponse = mysql_unbuffered_query('SELECT COUNT(*) AS CHAT FROM chat  ');
		$donnees = mysql_fetch_assoc($reponse);
		$Chat = $donnees['CHAT'];

		// Mesage total
		$nbMessage = $nbComs + $nbMessage + $Mp + $Chat;


		// Moyenne d'age
		$reponse = mysql_unbuffered_query('SELECT ROUND(AVG(age), 2) AS nb FROM membres WHERE age > 0');
		$donnees = mysql_fetch_assoc($reponse);
		$mAge = $donnees['nb'];
	}
echo '

<div class="bloc2">
	<h3>Statistiques des forums</h3>
	<div class="texte">
		<table style="margin:auto; border: none; padding: 5px;">
			' . (!$fofo ? '<br />
				<td>Nombre de Forums</td>
				<td>' . $nbForum . '</td>
			</tr>' : '') . '
			<tr>
				<td>Nombre de Topics</td>
				<td>' . $nbTopic . '</td>
			</tr>
			<tr>
				<td>Nombre de Messages</td>
				<td>' . $nbMessage . '</td>

			Les membres se sont échangé ' . $Mp  . ' MPs<br />
			La moyenne d\'&acirc;ge des forumeurs est de : ' . $mAge . ' ans.<br/>
			'; 
			
			echo'</center>
						</table>
					</div>
				</div>
				<br />
				';
				

?>
	<div class="bloc2" > 
		<h3>10 derniers membres inscrits</h3> 
		<div class="texte"> 
			<?php


	
					$i = 1;
			$reponse = mysql_query('SELECT * FROM membres ORDER BY id DESC');
			
			while($donnees = mysql_fetch_assoc($reponse))
			{
				echo '<img src="/images/fleched.gif">  <a href=../profil/profil-' . $donnees['pseudo'] . '.htm>' . $donnees['pseudo'] . '</a> le ' . date('d/m/Y\ \à\ H\:i\:s', $donnees['timestamp']) . '<br/>';
			
				
				
				$i++;
				
				
				if($i == 11) break;
			}
					?>
					

					
		</div> 
	</div> <p class="hautpage" style="float: right"><img src="images/defaut/puce_hautdepage.gif"> <a href="#">Retour haut de page</a></p>
	<br /> 

<?php
$reponse = mysql_unbuffered_query('
SELECT membres.pseudo, COUNT(message.idPseudo) AS nb
FROM membres
INNER JOIN message ON membres.id = message.idPseudo
GROUP BY pseudo
ORDER BY nb DESC
');
echo '

<font color="DodgerBlue"><strong>Top 15 des plus actifs</strong></font><br /><div class="separate"></div>
	<div class="table">
			<div class="tr">
				<div class="th tdbord cell2">Place</div>
				<div class="th tdbord cell1">Pseudo</div>
				<div class="th tdbord cell3">Nb de Messages</div>
			</div>
			';

			$i = 1;
			while($donnees = mysql_fetch_assoc($reponse))
			{
				echo '
				<div class="tr">
					<div class="td tdbord cell2">' . $i . '</div>
					<div class="td tdbord cell1"><center><a href="../profil/profil-' . $donnees['pseudo'] . '.htm" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a></center></div>
					<div class="td tdbord cell3">' . $donnees['nb'] . '</div><br />
				</div>
				';
				
				$i++;
				
				if($i == 16) break;
			}
			
			echo '
		</div><p class="hautpage" style="float: right"><img src="images/defaut/puce_hautdepage.gif"> <a href="#">Retour haut de page</a></p>
';

$reponse = mysql_unbuffered_query('
SELECT topic.titre, COUNT(message.id) AS nb
FROM topic
INNER JOIN message ON topic.id = message.idTopic
GROUP BY topic.titre
ORDER BY nb DESC
');

echo '
<br />
<font color="DodgerBlue"><strong>Les 15 gros topics</strong></font><br /><div class="separate"></div>
	
			<div class="table">
			<div class="tr">
				<div class="th tdbord cell2">Place</div>
				<div class="th tdbord cell1">Titre du topic</div>
				<div class="th tdbord cell3">Nb de Messages</div>
			</div>
			';
			
			$i = 1;
			while($donnees = mysql_fetch_assoc($reponse))
			{
				echo '
				<div class="tr">
					<div class="td tdbord cell2">' . $i . '</div>
					<div class="td tdbord cell1"><center><a href="' . have_url($donnees['idDuTopic'] , $nb_page2 , TOPIC_URL, $titre) . '">' . stripslashes($donnees['titre']) . '</a></center></div>
					<div class="td tdbord cell3">' . $donnees['nb'] . '</div>
				</div>
				';
				
				$i++;
				
				if($i == 16) break;
			}
			
		echo '
		
</div><p class="hautpage" style="float: right"><img src="images/defaut/puce_hautdepage.gif"> <a href="#">Retour haut de page</a></p>
';


	
$reponse = mysql_unbuffered_query('
SELECT membres.id AS `idPseudo`, membres.pseudo, COUNT(topic.idPseudo) AS nb
FROM membres
INNER JOIN topic ON membres.id = topic.idPseudo
GROUP BY pseudo
ORDER BY nb DESC
');

echo '<br />
<font color="DodgerBlue"><strong>Top 15 des plus actifs</strong></font><br /><div class="separate"></div>
	<div class="table">
	<div class="tr">
				<div class="th tdbord cell2">Place</div>
				<div class="th tdbord cell1">Pseudo</div>
				<div class="th tdbord cell3">Nb de Topics</div>
			</div>
			';
$i = 1;
			while($donnees = mysql_fetch_assoc($reponse))
			{
						
				echo '
				<div class="tr">
					<div class="td tdbord cell2">' . $i . '</div>
					<div class="td tdbord cell1"><center><a href="../profil/profil-' . $donnees['pseudo'] . '.html" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;">' . $donnees['pseudo'] . '</a></center></div>
					<div class="td tdbord cell3">' . $donnees['nb'] . '</div>
				</div>
				';
				
				$i++;
				
				if($i == 16) break;
			}
			echo'</div><p class="hautpage" style="float: right"><img src="images/defaut/puce_hautdepage.gif"> <a href="#">Retour haut de page</a></p>';
				

	if($numf)
	{
		$fof = ' - <a href="forum/forum-' . $numf. '.html">Retour sur le forum</a>';
	}
	
	
echo '
                                    <ul>';
					

					$reponse = mysql_unbuffered_query('SELECT id, titre, timestamp FROM forum WHERE statut=\'0\' ORDER BY titre ASC');
										
					while($donnees = mysql_fetch_assoc($reponse))
					{
										
						if($donnees['timestamp'] >= $_SESSION['tmp_derniere_visite']) 
						{
							// S'il y a un nouveau message sur le forum
							if(isset($_SESSION['forum_lu'.$donnees['id']]) && $_SESSION['forum_lu'.$donnees['id']] > $donnees['timestamp'])
							{
							echo '
							<li><a href="forum/stats.php?forum=' . $donnees['id'] . '">Stats Forum ' . $donnees['titre'] . '</a></li>
							';
							}
							else 
							{
								echo '
								<li><a href="forum/stats.php?forum=' . $donnees['id'] . '">Stats Forum ' . $donnees['titre'] . '--</a></li>
								';
							}
						}
						else
						{
						echo '
						<li><a href="forum/stats.php?forum=' . $donnees['id'] . '">Stats Forum ' . $donnees['titre'] . '</a></li>
						';
						}
					}
					echo '
                                    </ul>
                                   ';
?>
<br />

	
	
				<?php
$sql = 'SELECT * FROM topic ORDER BY id DESC LIMIT 10';
$req = mysql_query($sql) or die ('ERREUR SQL !
'.$sql.'
'.mysql_error());

	echo'<br />
	<font color="DodgerBlue"><strong>Les 10 derniers topics</strong></font><br /><div class="separate"></div>
	
	<div class="table">
	<div class="tr">
				<div class="th tdbord cell2">Pseudo</div>
				<div class="th tdbord cell1">Nom du topic</div>
				<div class="th tdbord cell3">N°</div>
			</div>
			';
$i = 1;
			while($data = mysql_fetch_array($req))
			{
				
				$id = $data['id'];
$idForum = $data['idForum'];
$pseudo = $data['pseudo'];
$Pseudo = $data['Pseudo'];
				
				echo '
				<div class="tr">
					<div class="td tdbord cell2"><a href="../profil/profil-' . $Pseudo . '.html"  onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=770,height=570\');return false;">' .$pseudo.'</a></div>
					<div class="td tdbord cell1"><center><img src="images/fleched.gif">  <a href="topic-'.$id.'-'.$idForum.'.html">' . htmlspecialchars(stripslashes($data['titre'])) . '</a></center></div>
					<div class="td tdbord cell3">' . $i . '</div>
				</div>
				';
				
				$i++;
			}
			echo'</div><p class="hautpage" style="float: right"><img src="images/defaut/puce_hautdepage.gif"> <a href="#">Retour haut de page</a></p><br>';
			
	include('pied.php');
	?>
	
	
	