<?php 
include('base.php');
include('tete.php');
include('array.php');

// Debut - Autorisation d'accès à la page
	

	$reponse = mysql_query('SELECT acces FROM membres WHERE id=' . intval($_GET['id']));
	$donnees = mysql_fetch_assoc($reponse);

	if($donnees['acces'] == 100 && $_SESSION['m']['id'] != 1)
	{
		avert('<b>Vous ne pouvez pas modifier les infos d\'un Admin.</b>');
		include('pied.php');
		exit;
	}
	
	if($donnees['acces'] == 98 && $_SESSION['m']['id'] != 1)
	{
		avert('<b>Vous ne pouvez pas modifier les infos d\'un Codeur.</b>');
		include('pied.php');
		exit;
	}
	
	if($donnees['acces'] == 95 && $_SESSION['m']['id'] != 1)
	{
		avert('<b>Vous ne pouvez pas modifier les infos d\'un Directeur.</b>');
		include('pied.php');
		exit;
	}

	if(!isset($_SESSION['m']['pseudo']))
	{
		avert('<b>Vous n\'Ãªtes pas connectÃ©.</b>');
		include('pied.php');
		exit;
	}
// Fin - Autorisation d'accès à la page

// ~ On récupère le nombre d'argent que le membre prossède
$requete = mysql_query('SELECT argent FROM membres WHERE id = ' . $_SESSION['m']['id']);
$shop = mysql_fetch_assoc($requete);
$argent = $shop['argent'];

// Debut - Modification Infos Obligatoire
	if(isset($_POST['passe'], $_POST['passe2'], $_POST['email']))
	{
		// Sécurité des variables
		$passe = secure($_POST['passe']);
		$passe2 = secure($_POST['passe2']);
		$email = secure($_POST['email']);
		$ancienpass = secure($_POST['ancienpass']);
			
		if($_POST['passe'] != NULL AND $_POST['email'] != NULL) // Si les variables ne sont pas nul
		{
			if(preg_match('#^[a-zA-Z0-9]{4,12}$#', $_POST['passe'])) // Si le passe respecte les conditions
			{
				if(preg_match('#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$#', $_POST['email'])) // Si l'adresse email est une adresse valide
				{
					// Vérification si l'email n'est pas déjà présente 2 fois
					$reponse = mysql_query('SELECT * FROM membres WHERE email="' . $email . '"') or die (mysql_error());
					$result = mysql_num_rows($reponse);
					
					if($result < 6)
					{
						if($passe == $passe2) // Si les 2 passes rentrés sont les mêmes.
						{
							$ancienpass = md5($ancienpass);
							$secufish = mysql_query('SELECT passe FROM membres WHERE id=' . $_SESSION['m']['id']);
							$finishfish = mysql_fetch_assoc($secufish);
							if($ancienpass == $finishfish['passe'])
							{
								mysql_query('
								UPDATE membres SET 
								passe=\'' . md5($passe) . '\', 
								email=\'' . $email . '\'
								WHERE id=' . $_SESSION['m']['id'] . '
								');
							
								avert('<b>Votre pass a bien été modifié !</b>');
							}
							else
							{
								avert('<b>L\'ancien pass ne correspond pas...</b>');
							}
						}
						else
						{
							avert('<b>Les 2 mots de passe que vous avez rentrés ne correspondent pas.</b>');
						}
					}
					else
					{
						avert('<b>Une adresse email peut contenir que 5 pseudos !</b>');
					}
				}
				else
				{
					avert('<b>L\'adresse email n\'est pas une adresse email valide.</b>');
				}
			}
			else
			{
				avert('<b>Le mot de passe n\'est pas valide.</b>');
			}
		}
		else
		{	
			avert('<b>Vous n\'avez pas rempli tous les champs.</b>');
		}
	}
// Fin - Modification Infos Obligatoires


// Debut - Modification Infos Complèmentaires
	if (isset($_POST['sexe']) OR isset($_POST['age']) OR isset($_POST['presentation']) OR isset($_POST['contact'])) {
		$sexe = secure($_POST['sexe']);
		$age = secure($_POST['age']);
		$pays = secure($_POST['pays']);
		$contact = secure($_POST['contact']);
		$pres = secure($_POST['presentation']);
		
		mysql_query("
		UPDATE membres SET
		sexe='" . $sexe . "',
		age='" . $age . "',
		pays='" . $pays . "',
		contact='" . $contact . "',
		presentation='" . $pres . "'
		WHERE id='" . $_SESSION['m']['id'] . "'
		") or die(mysql_error());
		echo '<b>Vos informations ont bien été apportés !</b><br /><br />';
	}
	
	if(isset($_POST['signature']))
	{
		$signature = secure($_POST['signature']);
		
		mysql_query("
		UPDATE membres SET
		signature='" . $signature . "'
		WHERE id='" . $_SESSION['m']['id'] . "'
		");
		avert('Votre signature a bien &eacute;t&eacute; modifi&eacute; !');
	}
// Fin - Modification Infos Complémentaires


// Debut - Modification Avatar
	if(isset($_POST['avatar']))
	{
		if(!preg_match('#deconnexion#', $_POST['avatar'])) // Si il n'y a pas le lien de déconnexion
		{
			if(substr($_POST['avatar'],-4) == ".jpg" OR substr($_POST['avatar'],-5) == ".jpeg" OR substr($_POST['avatar'],-4) == ".gif" OR substr($_POST['avatar'],-4) == ".png") // On vérifie que ce sont bien des images
			{
				$avatar = secure($_POST['avatar']);
				mysql_query("
				UPDATE membres SET
				avatar='" . $avatar . "'
				WHERE id='" . $_SESSION['m']['id'] . "'
				");
				avert('<b>Vos informations ont bien été apportés !</b>');
			}
			else
			{
				avert('Vous ne pouvez ajouter que des images');
			}
		}
		else
		{
			avert(bbCode("Vous ne pouvez pas mettre le lien de deconnexion"));
		}
	}
// Fin - Modification Avatar

	$reponse = mysql_query('SELECT avatar FROM membres WHERE id=' . $_SESSION['m']['id'] . '');
	$donnees = mysql_fetch_assoc($reponse);
	$avatar = $donnees['avatar'];
	if(!preg_match("#^http://[^'\" ]+\.(jpg|jpeg|gif|png)$#i", $avatar))
	{
		$avatar = 'http://techhub.alwaysdata.net/images/noavatar.png';
	}	
	$affichAvatar = '<span style="float: left; padding-left: 5px; padding-right: 20px;"><a href="' . text_i($avatar) . '" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="' . text_i($avatar) . '" alt="" border="0" height="75" width="75" /></a><br /><a href="cdv-' . $_SESSION['m']['pseudo'] . '.htm" onclick="window.open(this,\'profil\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=520,height=570\');return false;"><img src="images/message/bt_forum_profil.gif" border="0" /> Profil CDV</a></span>';
	
	$requete = mysql_query('SELECT COUNT(*) AS nbMessages FROM message WHERE idPseudo=\'' . $_SESSION['m']['id'] . '\'');
	$infos = mysql_fetch_assoc($requete);
	$posts = $infos['nbMessages'];
	$messages = stripslashes(number_format($infos['nbMessages'], 0, '', '.'));
	$nbPosts = mysql_result($requete, 0);
	$affichMessage = '<b><font color="green">' . $messages . '</font> post&eacute;s sur les forums</b>';
	
	$puce_base = '<img src="images/defaut/puce_base.gif">';
	$puce2 ='<img src="images/puce_forum.png">';
	$puce3 ='<img src="images/puce_forum2.png">';
	
// Debut - Affichage bloc Sommaire
	echo '
		<div class="bloc2">
			<h3>Gestion de votre compte</h3>
			<div class="texte">
			    ' . $affichAvatar . '<b>Bonjour ' . $_SESSION['m']['pseudo'] . ', et bienvenue sur votre page de gestion de compte. <br />
				Vous pouvez ici quand vous le voulez modifier vos informations personnels et publics. 	<br /><br />
				' . $puce2 . ' Vous avez <font color="green">' . $argent . ' ' . NOM_ARGENT . '</font> ' . $puce3 . '</b><br /><br /><span style="float: right;">' . $puce2 . ' ' . $affichMessage . ' ' . $puce3 . '</span><br /><br />
		</div>
	</div>
<br />
	<div class="bloc2">
		<h3>Information personnelle et CDV</h3>
		<div class="texte">
				' . $puce_base . ' <a href="option-1.html">Gestion Pass et Email</a><br />
				' . $puce_base . ' <a href="option-2.html">Gestion de la CDV</a><br />
				' . $puce_base . ' <a href="option-6.html">Présonnalisation de la CDV</a><br />
		</div>
	</div>
<br />
	<div class="bloc2">
		<h3>Avatar et Signature</h3>
		<div class="texte">
				' . $puce_base . ' <a href="option-3.html">Gestion de votre avatar</a><br />
				' . $puce_base . ' <a href="option-5.html">Gestion de votre signature</a>
		</div>
	</div><br /><br />
	';
// Fin - Affichage Bloc Sommaire


// Debut - Affichage Infos Obligatoires
	$reponse = mysql_query('SELECT email FROM membres WHERE id=' . $_SESSION['m']['id']);
	$donnees = mysql_fetch_assoc($reponse);
	
	if(isset($_GET['page']) && $_GET['page'] == 1)
	{
		echo '
		<br />
		<div class="bloc2">
			<div class="texte">
				<form method="post">
				Ces informations sont obligatoires, mais vous pourrez toutes les modifier par la suite si bon vous semble.<br />
				La casse (différence entre les majuscules et minuscules) est prise en compte pour le mot de passe.<br /><br />
				<label for="pseudo">Pseudo</label> <input readonly id="pseudo" type="text" name="pseudo" value="' . $_SESSION['m']['pseudo'] . '" /><br />
				<label for="ancienpass">Ancien pass :</label> <input id="ancienpass" type="password" name="ancienpass" /><br/>
				<label for="passe">Nouveau pass :</label> <input id="passe" type="password" name="passe" /><br/>
				<label for="passe2">Confirmation new pass :</label> <input id="passe2" type="password" name="passe2" /><br/>
				<label for="email">Adresse e-mail :</label> <input id="email" type="text" name="email" value="' . $donnees['email'] . '" /><br/>
				<center><input border="0" type="image" value="submit" src="images/bouton/valider.gif" class="inputBouton" /></center>
				</form>
			</div>
		</div>
		';
	}
// Fin - Affichage Infos Obligatoires

// Debut - Affichage Infos Complémentaires
	if(isset($_GET['page']) && $_GET['page'] == 2)
	{
		$reponse = mysql_query('SELECT sexe, age, pays, presentation, contact FROM membres WHERE id=' . $_SESSION['m']['id']);
		$donnees = mysql_fetch_assoc($reponse);
		
		echo '
		<br />
		<div class="bloc2">
			<div class="texte">
				<form method="post">
				Ces informations ne sont pas obligatoire et pourront être changés après votre inscription.<br /><br />
				<label for="sexe">Sexe :</label>
				<select id="sexe" name="sexe">
					<option value=""> - - - - - - - - - - - - - - - - - </option>
					<option value="m"'; if($donnees['sexe'] == 'm') { echo 'selected="selected"'; } echo '>Masculin</option>
					<option value="f"'; if($donnees['sexe'] == 'f') { echo 'selected="selected"'; } echo '>Feminin</option>
				</select>
				<br />
				<label>Age :</label>
				<select id="age" name="age" width="250px">
					<option value=""> - - - - - - - - - - - - - - - - - </option>
					';
					for($i = 1; $i <= 99; $i++)
					{
						echo '<option value="' . $i . '"'; if($donnees['age'] == $i){ echo 'selected="selected"'; } echo'>' . $i . '</option>';
					}
					echo '
				</select>
				<br />
				<label>Pays :</label>
				<select id="pays" name="pays">
					<option value=""> - - - - - - - - - - - - - - - - - </option>
					<option value="France"'; if($donnees['pays'] == 'France') { echo 'selected="selected"'; } echo '>France</option>
					<option value="Allemagne">Allemagne</option>
					<option value="Belgique">Belgique</option>
					<option value="Suisse">Suisse</option>
					<option value="Quebec">Quebec</option>
					<option value="Canada">Canada</option>
				</select>
				<br />
				<br />
				<label for="contact">Contact :</label> <input id="contact" type="text" name="contact" value="' . $donnees['contact'] . '"/><br />
				<label for="pres">Présentation :</label> <textarea id="pres" name="presentation" rows="7" cols="30">' . stripslashes($donnees['presentation']) . '</textarea>
				<br />
				<label>Afficher smileys titre topic :</label>
				Non : <input type="radio" name="Smileydanslestitres" value="0" '; if($donnees['Smileydanslestitres'] == 0) { echo 'checked'; } echo '/> Oui : <input type=radio name="Smileydanslestitres" value="1" '; if($donnees['Smileydanslestitres'] == 1) { echo 'checked'; } echo '/>
				<br />
				<center><input border="0" type="image" value="submit" src="images/valider.gif" class="inputBouton" /></center>
				
				<a type="bouton" color="red" href="#">test</a>
				</form>
				
			</div>
		</div>
		';
	}
// Fin - Affichage Infos Complémentaires


// Debut - Affichage avatar
	if(isset($_GET['page']) && $_GET['page'] == 3)
	{
		$reponse = mysql_query('SELECT avatar FROM membres WHERE id=' . $_SESSION['m']['id']);
		$donnees = mysql_fetch_assoc($reponse);
		
		echo '
		<br />
		<div class="bloc2">
			<div class="texte">
				Il vou faut mettre un lien direct d\'image pour que votre avatar s\'affiche, le mieux est de l\'uploader sur <a href="http://www.noelshack.com">NoelShack</a> et de mettre le lien récupéré ici.<br /><br />
				<form method="post">
					 <b>Avatar :</b> <input type="text" name="avatar" />
					 <input type="submit" name="submit" value="Envoyer" />
				</form>
			</div>
		</div>
		';
	}
// Fin - Affichage avatar
?>
			<script type="text/javascript"> 
			function maxlength_textarea(id, crid, max)
			{
      		  var txtarea = document.getElementById(id);
     		   document.getElementById(crid).innerHTML=max-txtarea.value.length;
     		   txtarea.onkeypress=function(){eval('v_maxlength("'+id+'","'+crid+'",'+max+');')};
     		   txtarea.onblur=function(){eval('v_maxlength("'+id+'","'+crid+'",'+max+');')};
     		   txtarea.onkeyup=function(){eval('v_maxlength("'+id+'","'+crid+'",'+max+');')};
    		    txtarea.onkeydown=function(){eval('v_maxlength("'+id+'","'+crid+'",'+max+');')};
    		}
			function v_maxlength(id, crid, max)
			{
     		   var txtarea = document.getElementById(id);
     		   var crreste = document.getElementById(crid);
     		   var len = txtarea.value.length;
        		if(len>max)
      		    {
					txtarea.value=txtarea.value.substr(0,max);
				}
        		len = txtarea.value.length;
				crreste.innerHTML=max-len;
			}
			</script>
<?php
if(isset($_GET['page']) && $_GET['page'] == 5)
{
		$reponse = mysql_query('SELECT signature FROM membres WHERE id=' . $_SESSION['m']['id']);
		$donnees = mysql_fetch_assoc($reponse);
	echo '
	<br />
	<div class="bloc2">
		<div class="texte">
		<form method="post">
			<label for="sign"><b>Signature : <font color="red"><span id="carac_reste_pres_1"></span></font> caractères.</b></label> <br /><textarea id="sign" name="signature" cols="60" rows="5">' . stripslashes($donnees['signature']) . '</textarea>
			'; ?> <script type="text/javascript">
			maxlength_textarea('sign','carac_reste_pres_1',250);
			</script> <?php echo'
			<center><input border="0" type="image" value="submit" src="images/bouton/valider.gif" class="inputBouton" /></center>
			</form>
		</div>
	</div>
	<br />
	';
}

if(isset($_GET['page']) && $_GET['page'] == 6)
{

if(isset($post["fondcdv"]) && isset($post["f_color"])) {
$exist = $bdd->sql_query("select count(*) as `nb` from `cdvperso` where `membres_id`={$DNS["id"][0]}",true,false);
if($exist["nb"] > 0) {
if(empty($post["fondcdv"])) { $bdd->sql_query("update `cdvperso` set `url`='' where `membres_id`={$DNS["id"][0]}",false,true); $erreur = "Votre fond d'écran a bien été enlevé";}
elseif(!empty($post["fondcdv"]) && preg_match("#^http://[^'\" ]+\.(jpg|jpeg|gif|png)$#i", $post["fondcdv"])) { $bdd->sql_query("update `cdvperso` set `url`='".db_sql::escape($post["fondcdv"])."' where `membres_id`={$DNS["id"][0]}",false,true); $erreur = "Votre fond d'écran a bien été remplacé"; } else { $erreur = "L'adresse entrée n'est pas valide"; }
if(isset($post["repeter"])) { $bdd->sql_query("update `cdvperso` set `repeat`='1' where `membres_id`={$DNS["id"][0]}",false,true); }
else { $bdd->sql_query("update `cdvperso` set `repeat`='0' where `membres_id`={$DNS["id"][0]}",false,true); }
if(preg_match('#^\#([A-F0-9]{3,7})$#iU', $post["f_color"])) { $bdd->sql_query("update `cdvperso` set `color`='".db_sql::escape($_post["f_color"])."' where `membres_id`={$DNS["id"][0]}",false,true);  $erreur.="<br />Votre couleur de fond a bien été remplacée";}
else {$bdd->sql_query("update `cdvperso` set `color`='' where `membres_id`={$DNS["id"][0]}",false,true); $erreur.="<br />Votre couleur de fond a bien été enlevée"; }

}
else {
$repeat = ($post["repeter"]) ? "1":"0";
$DNS[] = array ("id_post" => array ( "id" => "", "membre" => intval($_SESSION['m']['id']), "color" => db_sql::escape($post["f_color"]), "url" => db_sql::escape($post["fondcdv"])) );
extract($DNS[0]["id_post"]);
if(!preg_match('#^\#([A-F0-9]{3,7})$#iU', $post["f_color"])) { $erreur="La couleur n'est pas une couleur valide"; }
if(!isset($erreur)) { $bdd->sql_query("INSERT INTO cdvperso VALUE('', '{$membre}', '{$color}', '{$url}', '{$repeat}')",false,true); $erreur = "Votre fond d'écran a bien été mis";}
}
if(isset($erreur)) { avert($erreur); }
}
	echo '
	<br />
	<div class="bloc2">
		<div class="texte">
				Il vous faut mettre un lien direct d\'image pour que votre fond d\'ecran de CDV s\'affiche, le mieux est de l\'uploader sur <a href="http://www.noelshack.com">NoelShack</a> et de mettre le lien récupéré ici.<br /><br />
				<form method="post" name="objForm">
					 <b>Fond ( laissez vide pour désactiver ) :</b> <input type="text" name="fondcdv" /><br />
					 <input type="radio" name="repeter" /> Répeter le fond d\'écran<br />
					 <b> Couleur hexadécimale du fond ( laissez vide pour désactiver ): </b><input type="text" size="10" name="f_color" value="" maxlength="7">
<img src="http://www.famfamfam.com/lab/icons/silk/icons/color_wheel.png" onClick="fctShow(document.objForm.f_color);" style="cursor:pointer;"><br />
					 <input type="submit" name="submit" value="Envoyer" />
					 
				</form>
		</div>
	</div>
	<br />
	';
}

include('pied.php');
?>