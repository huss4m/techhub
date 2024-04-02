<?php
include('base.php');

// Debut - Ajout d'un message à avertir
	if(isset($_POST['avertir']) && ctype_digit($_GET['idMessage']) && ctype_digit($_GET['idTopic']))
	{
		$motif = db_sql::escape($_POST['avertir']);
		
		$bdd->sql_query('INSERT INTO avertir VALUES("", "' . intval($_GET['idMessage']) . '", "' . intval($_GET['idTopic']) . '", "' . intval($_GET['page']) . '", "' . $motif . '")', false, true);
		
		$var = avert('Le message a bien été signalé.</b>');
	}
// Fin - Ajout d'un message à avertir
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>Avertir un Administrateur</title>
		<link rel="shortcut icon" type="image/jpg" href="favicon.gif" /> 
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
			body
			{
				background: url('images/popup_fond.gif');
				font-size: 0.75em;
				font-family: Arial;
			}
			
			.bloc2 h3
			{
				font-size: 1.10em;
				background: url('images/nojvcr/bloc2.png') repeat-x;
				color: #FFFFFF;
				padding-left: 20px;
				padding-top: 2px;
				font-family: Verdana;
				width: 510px;
				height: 18px;
				margin: 0px auto;
			}

			.bloc2 .texte
			{
				padding: 4px;
				background: #f8fafc;
				border: 1px solid #bddfef;
				width: 520px;
				margin: 0px auto;
			}
			
			label
			{
				display:block;
				width:75px;
				float:left;
			}
			
			.bloc2 label
			{
				font-weight: bold;
				margin: 2.5px;
			}
		</style>
	</head>
    <body>
	<?php
	// Debut - Autorisation d'avertir
		if(!isset($_SESSION['m']['id']))
		{
			avert('Vous devez être connecté pour pouvoir avertir un message.');
			exit;
		}
		
		if(isset($var))
		{
			echo $var;
			exit;
		}
		
		$reponse = $bdd->sql_query('SELECT * FROM avertir WHERE idMessage = ' . intval($_GET['idMessage']));
		$result = $bdd->sql_count($reponse);
		
		if($result > 0)
		{
			avert('Ce message a déjà été signalé !</b>');
			exit;
		}
	// Fin - Autorisation d'avertir
	
	// Debut - Affichage
		echo '
		<br />
		<div class="bloc2">
			<h3>Avertir un Message</h3>
			<div class="texte">
				<form method="post">
					<b>Pseudo :</b> ' . htmlentities($_SESSION['m']['pseudo']) . '<br /><br />
					Utilisez cette fonction si vous jugez utile de signaler ce message. Si vous pensez que le membre mérite un bann\'.
					<br /><br />
					<label for="avertir">Motif :</label> 
					<select name="avertir" id="avertir">
						<option value="1">Flood</option>
						<option value="2">Boost</option>
						<option value="3">Insulte</option>
						<option value="4">Contenu inapproprié/racisme/...</option>
						<option value="5">Autre</option>
					</select>
					<input type="submit" value="Avertir" />
				</form>
			</div>
		</div>
		';
	// Fin - Affichage
	?>
	</body>
</html>