<?php
include('base.php');
include('tete.php');



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
			
										
										/*$targetDirectory = "projets/"; // Specify the directory where you want to store the uploaded files
								$targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);
								$uploadOk = 1;
								$zipFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

								// Check if the file is a .zip file
								if($zipFileType != "zip") {
									echo "Seul les fichiers .zip sont autorisés.";
									$uploadOk = 0;
								}

								// Check if the file already exists
								if (file_exists($targetFile)) {
									echo "Fichier existe déjà.";
									$uploadOk = 0;
								}

								// Check file size
								if ($_FILES["fileToUpload"]["size"] > 500000) {
									echo "Fichier trop lourd. (5MB max)";
									$uploadOk = 0;
								}

								// If $uploadOk is set to 0, it means there was an error
								if ($uploadOk == 0) {
									echo "Sorry, your file was not uploaded.";
								} else {
									// Move the uploaded file to the desired location
									if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
										echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
										echo "File path: " . $targetFile; // Display the file path
									} else {
										echo "Sorry, there was an error uploading your file.";
									}
								}*/
							
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
				// Securisation des variables
				$titre = secure($_POST['titre']);
				$leader = $_SESSION['m']['pseudo'];
				$lien = secure($_POST['image']);
				$intro = secure($_POST['intro']);
				$contenu = secure($_POST['contenu']);
				$git = secure($_POST['git']);
				//$file= $targetFile;
					
					
					mysql_unbuffered_query('INSERT INTO projets VALUES("", "' . $titre . '", "' . $contenu . '", "' . $leader . '", "' . $intro . '", "' . $lien . '", "' . $git . '", "'.$file.'")');
					
					$dernierId = mysql_insert_id();
					mysql_unbuffered_query('INSERT INTO joindreProjet VALUES("", "'. $dernierId .'", "'.$_SESSION['m']['id'].'")');
					echo '<b>Le projet a été créé!</b>';
					redirection('mes-projets.php');
					exit;
					
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


?>

<div class="bloc2" class="redigerNews">
	<form method="post">
		<h3>Créer un projet</h3> 
		<div class="texte">
			<label for="titre">Titre du Projet :</label> <input type="text" name="titre" value="<?php echo stripslashes(nl2br(htmlspecialchars($titre))); ?>" /><br />
			
			
			<label for="lien">Image illustrative :</label> <input type="text" name="image" value="images/defaut/no_image.png<?php echo stripslashes(nl2br(htmlspecialchars($image))); ?>" /><br />
			<label for="git">Dépôt git:</label> <input type="text" name="git" value="<?php echo stripslashes(nl2br(htmlspecialchars($git))); ?>" /><br /><br><br>
			<label for="auteur">Introduction du projet :</label> <br><textarea type="text" name="intro" id="message"><?php echo stripslashes(nl2br(htmlspecialchars($intro))); ?></textarea><br />
			
			
			<input type="hidden" name="token_news" id="token_news" value="<?php echo $token_news; ?>"/>
			<br /><br />
			<b>Détails du projet :</b><br /><textarea name="contenu" cols="60" rows="15" id="message"><?php echo $contenu; ?></textarea>
			<br />
			<?php // <b>Fichiers (.zip): </b><input type="file" name="fileToUpload" id="fileToUpload"><br>?>
			<center><input type="submit" class="bouton" value="Créer" /></center>
		</form>
	</div>
</div>	


<?php
include('pied.php');
?>