<?php
include('base.php');

// Debut - Autorisation d'acces à la page

	if(isset($_SESSION['m']['id']))
	{
		$reponse = mysql_query('SELECT * FROM newseur WHERE idPseudo=' . $_SESSION['m']['id']);
		$result = mysql_num_rows($reponse);
		
		if($result > 0)
		{
			$_SESSION['m']['acces'] = 30;
		}
	}
	
	// Si on a l'accès pour 
	if(!isset($_SESSION['m']['acces']) || ($_SESSION['m']['acces'] < 92 && $_SESSION['m']['acces'] != 30))
	{
		include('tete.php');
		avert('<b>Vous n\'avez pas les droits d\'administration.</b>');
		include('pied.php');
		exit;
	}

// Fin - Autorisation d'acces à la page

		if(isset($_GET['ad']) && $_GET['ad'] == index)
		{
		include('tete.php');
echo'
<div class="bloc2">
		<h3>Espace de rédaction:</h3>
			<div class="texte">
<ul>
<li> <a href="rediger.php?ad=news" >Rediger une news</a></li>
<li> <a href="rediger.php?ad=cours" >Ajouter un cours</a></li>
</ul>
			</div>
</div>
';
		}
		if(isset($_GET['ad']) && $_GET['ad'] == news)
		{
include('tete.php');


// Déclaration des variables qui pourront être changés
$titre = '';
$auteur = '';
$lien = '';
$intro = '';
$news = '';

if(isset($_GET['modifierNews']) AND ctype_digit($_GET['modifierNews']))
{
	$reponse = mysql_query('SELECT * FROM news WHERE id=\'' . $_GET['modifierNews'] . '\'');
	$donnees = mysql_fetch_array($reponse);
	
	$titre = stripslashes($donnees['titre']);
	$auteur = stripslashes($donnees['auteur']);
	$lien = stripslashes($donnees['lien']);
	$intro = stripslashes($donnees['intro']);
	$news = stripslashes($donnees['news']);
}

//On va vérifier :
//Si le jeton est présent dans la session et dans le formulaire
if(isset($_SESSION['token_news']) && isset($_SESSION['rediger_news_token_time']) && isset($_POST['token_news']))
{
	//Si le jeton de la session correspond à celui du formulaire
	if($_SESSION['token_news'] == $_POST['token_news'])
	{
		//On stocke le timestamp qu'il était il y a 15 minutes
		$timestamp_ancien = time() - (15*60);
		//Si le jeton n'est pas expiré
		if($_SESSION['rediger_news_token_time'] >= $timestamp_ancien)
		{
				if(isset($_POST) AND !empty($_POST))
				{	
				// Securisation des variables
				$titre = secure($_POST['titre']);
				$auteur = secure($_POST['auteur']);
				$lien = secure($_POST['lien']);
				$intro = secure($_POST['intro']);
				$news = secure($_POST['news']);
					if(isset($_GET['modifierNews']) AND ctype_digit($_GET['modifierNews']))
					{
					mysql_query('UPDATE news SET titre=\'' . $titre . '\', auteur=\'' . $auteur . '\', lien=\'' . $lien . '\', intro=\'' . $intro . '\', news=\'' . $news . '\' WHERE id=\'' . $_GET['modifierNews'] . '\'');
					echo '<b>La news a bien été modifié !</b>';
					redirection('listes.php');
					exit;
					}
					else
					{
					mysql_query('INSERT INTO news VALUE("", "' . $titre . '", "' . $auteur . '", "' . $lien . '", "' . $intro . '", "' . $news . '", "' . time() . '", "1")');
					echo '<b>La news a bien été envoyée !</b>';
					redirection('listes.php');
					exit;
					}
				}
			
		}
	}
}

//On génére un jeton totalement unique (c'est capital :D)
$token_news = md5(uniqid(rand(), true));
//Et on le stocke
$_SESSION['token_news'] = $token_news;
//On enregistre aussi le timestamp correspondant au moment de la création du token
$_SESSION['rediger_news_token_time'] = time();
?>
<body>
<div class="bloc2" class="redigerNews">
	<form method="post">
		<h3>Rédiger une News</h3> 
		<div class="texte">
			<label for="titre">Titre :</label> <input type="text" name="titre" value="<?php echo stripslashes(nl2br(htmlspecialchars($titre))); ?>" /><br />
			<label for="auteur">Auteur :</label> <input type="text" name="auteur" value="<?php echo stripslashes(nl2br(htmlspecialchars($auteur))); ?>" /><br />
			<label for="lien">Lien Image :</label> <input type="text" name="lien" value="images/defaut/no_image.png<?php echo stripslashes(nl2br(htmlspecialchars($lien))); ?>" /><br />
			<br />
			<label for="intro">Intro de la News :</label><br><textarea id="intro" name="intro" cols="50" rows="10"><?php echo $intro; ?></textarea><br />
			<label></label><script type="text/JavaScript">
			<!--
				displaylimit("","intro",200)
			// -->
			</script>
			<input type="hidden" name="token_news" id="token_news" value="<?php echo $token_news; ?>"/>
			<br /><br />
			<b>News Complète :</b><br /><textarea name="news" cols="60" rows="15" id="message"><?php echo $news; ?></textarea>
			<br />
			<center><input type="submit" value="Envoyer" /></center>
		</form>
	</div>
</div>	

<?php
		}

if(isset($_GET['ad']) && $_GET['ad'] == cours)
		{
include('tete.php');


// D�claration des variables qui pourront �tre chang�s
$titre = '';
$auteur = '';
$image = '';
$cours = '';
$intro = '';

if(isset($_GET['modifierCours']) AND ctype_digit($_GET['modifierCours']))
{
	$reponse = mysql_query('SELECT * FROM cours WHERE id=\'' . $_GET['modifierCours'] . '\'');
	$donnees = mysql_fetch_array($reponse);
	
	$titre = stripslashes($donnees['titre']);
	$auteur = stripslashes($donnees['auteur']);
	$image = stripslashes($donnees['image']);
	$intro = stripslashes($donnees['intro']);
	$contenu = stripslashes($donnees['contenu']);
}

//On va v�rifier :
//Si le jeton est pr�sent dans la session et dans le formulaire
if(isset($_SESSION['token_news']) && isset($_SESSION['rediger_news_token_time']) && isset($_POST['token_news']))
{
	//Si le jeton de la session correspond � celui du formulaire
	if($_SESSION['token_news'] == $_POST['token_news'])
	{
		//On stocke le timestamp qu'il �tait il y a 15 minutes
		$timestamp_ancien = time() - (15*60);
		//Si le jeton n'est pas expir�
		if($_SESSION['rediger_news_token_time'] >= $timestamp_ancien)
		{
				if(isset($_POST) AND !empty($_POST))
				{	
				// Securisation des variables
				$titre = secure($_POST['titre']);
				$auteur = secure($_POST['auteur']);
				$image = secure($_POST['image']);
				$intro = secure($_POST['intro']);
				$contenu = secure($_POST['contenu']);
					if(isset($_GET['modifierCours']) AND ctype_digit($_GET['modifierCours']))
					{
					mysql_query('UPDATE cours SET titre=\'' . $titre . '\', contenu=\'' . $contenu . '\', auteur=\'' . $auteur . '\', lien=\'' . $image . '\', intro=\'' . $intro . '\' WHERE id=\'' . $_GET['modifierCours'] . '\'');
					echo '<b>Le cours a bien été modifié !</b>';
					redirection('listes.php');
					exit;
					}
					else
					{
					mysql_query('INSERT INTO cours VALUE("", "' . $titre . '", "' . $contenu . '", "' . $auteur . '", "' . $image . '", "' . $intro . '")');
					echo '<b>Le cours a bien été envoyé !</b>';
					redirection('listes.php');
					exit;
					}
				}
			
		}
	}
}

//On g�n�re un jeton totalement unique (c'est capital :D)
$token_news = md5(uniqid(rand(), true));
//Et on le stocke
$_SESSION['token_news'] = $token_news;
//On enregistre aussi le timestamp correspondant au moment de la cr�ation du token
$_SESSION['rediger_news_token_time'] = time();
?>
<body>
<div class="bloc2" class="redigerNews">
	<form method="post">
		<h3>Rédiger un Cours</h3> 
		<div class="texte">
			<label for="titre">Titre du Cours :</label> <input type="text" name="titre" value="<?php echo stripslashes(nl2br(htmlspecialchars($titre))); ?>" /><br />
			<label for="auteur">Auteur :</label> <input type="text" name="auteur" value="<?php echo stripslashes(nl2br(htmlspecialchars($auteur))); ?>" /><br />
			
			<label for="lien">Image illustrative :</label> <input type="text" name="image" value="images/defaut/no_image.png<?php echo stripslashes(nl2br(htmlspecialchars($image))); ?>" /><br />
			<br />
			
			<label for="auteur">Introduction du cours :</label> <br><textarea type="text" name="intro" id="message"><?php echo stripslashes(nl2br(htmlspecialchars($intro))); ?></textarea><br />
			
			
			<input type="hidden" name="token_news" id="token_news" value="<?php echo $token_news; ?>"/>
			<br /><br />
			<b>Cours :</b><br /><textarea name="contenu" cols="60" rows="15" id="message"><?php echo $contenu; ?></textarea>
			<br />
			<center><input type="submit" value="Envoyer" /></center>
		</form>
	</div>
</div>	
<?php
		}
?>
<?php
include('pied.php');
?>
