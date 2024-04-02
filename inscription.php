<?php
include('base.php');

/* Fonction que je terminerais plus tard
if(FERM_INSCRIPTION == 'fermer')
{
	include('tete.php');
	avert('Inscription ferm&eacute;e');
	include('pied.php');
	exit;
}
*/

// Debut - Verification si pas connecté
	if(isset($_SESSION['m']['pseudo']))
	{
		include('tete.php');
		avert('Vous êtes déjà inscris.');
		include('pied.php');
		exit;
	}
// Fin - Verification si pas connecté

// Création de variable vide pour le formulaire
$pseudo = '';
$email = '';

// Debut - Ajouter membre
if($_GET['etape'] == 1 AND isset($_POST['pseudo']))
{
	/* if(FERM_INSCRIPTION != 'fermer') // Si les inscriptions sont fermer
	{ */
		$pseudal = $_POST['pseudo'];
		if(preg_match("#^[a-zA-Z0-9\[\]_-]{3,15}$#", $pseudal)) // Si le pseudo respecte les conditions
		{
			$reponse = mysql_query('SELECT COUNT(*) FROM membres WHERE pseudo REGEXP "^' . $pseudal .'$"');
			$result = mysql_result($reponse, 0);
			
			if($result == 0)
			{
				$_SESSION['m']['pseudal'] = $pseudal;
				header('Location: inscription.html');
				exit;
			}
			else
			{
				include('tete.php');
				avert('Le pseudo ' . $pseudal . ' existe d&eacute;j&agrave;.<br /><a href="inscription.html?etape=1">Retour</a>');
				include('pied.php');
				exit;
			}
			
		}
		else
		{
			include('tete.php');
			avert('Le pseudo est invalide.<br /><a href="inscription.html?etape=1">Retour</a>');
			include('pied.php');
			exit;
		}
	/* }
	else
	{
		include('tete.php');
		avert(bbCode("Les inscriptions sont fermé"));
		include('pied.php');
		exit;
	} */
}
	include('tete.php');
	if(!empty($_POST['pseudo']) && !isset($_GET['etape']))
	{
		// Sécurité des variables
		$pseudo = secure($_POST['pseudo']);
		$passe = secure($_POST['passe']);
		$passe2 = secure($_POST['passe2']);
		$email = secure($_POST['email']);
		$emailVu = secure($_POST['emailVu']);
		$confirm = secure($_POST['confirm']);
		$sexe = secure($_POST['sexe']);
		$age = secure($_POST['age']);
		$contact = secure($_POST['contact']);
		$presentation = secure($_POST['presentation']);
		$pays = secure($_POST['pays']);
		$avatar = secure($_POST['avatar']);
		$signature = secure($_POST['signature']);
		
		if(!empty($_POST['pseudo']) AND !empty($_POST['passe']) AND !empty($_POST['email'])) // Si les variables ne sont pas nul
		{
			/* if(FERM_INSCRIPTION != 'fermer') // Si les inscriptions sont fermer
			{ */
				if(preg_match("#^[a-zA-Z0-9\[\]_-]{3,15}$#", $_POST['pseudo'])) // Si le pseudo respecte les conditions
				{
					if(preg_match('#^[a-zA-Z0-9]{4,12}$#', $_POST['passe'])) // Si le passe respecte les conditions
					{
						if(preg_match('#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$#', $_POST['email'])) // Si l'adresse email est une adresse valide
						{
							// Vérification si l'email n'est pas déjà présente 5 fois
							$reponse = mysql_query('SELECT * FROM membres WHERE email="' . $email . '"') or die (mysql_error());
							$result = mysql_num_rows($reponse);
							
							if($result < 5)
							{
								$reponse = mysql_query('SELECT COUNT(*) FROM membres WHERE pseudo="' . $pseudo .'"');
								$result = mysql_result($reponse, 0);
								
								if($passe == $passe2) // Si les 2 passes rentrés sont les mêmes.
								{
									if($result == 0) // Si le pseudo n'est pas déjà choisi
									{
										if(!empty($_POST['charte'])) // Si la charte a bien été accepté
										{
											if(isset($_POST['verif_code']) AND !Empty($_POST['verif_code'])) // Le champ du code de confirmation a été remplis
											{
												if($_POST['verif_code'] == $_SESSION['aleat_nbr']) // Si le champ est égal au code généré par l'image
												{
													// Génération de la clef pour validé l'inscription
													$clef = sha1(microtime(NULL)*100000);
																	
													mysql_query("INSERT INTO membres VALUES('', '" . $pseudo . "', '" . md5($passe) . "', '" . $email . "', '" . $sexe . "', '" . $age . "', '" . $pays . "', '" . $contact . "', '" . $presentation . "', '" . $avatar . "', '10', '" . time() . "', '" . $clef . "', '1', '0', '" . $signature . "', '', '', '', '', '', '', '100', '" . $_SERVER['REMOTE_ADDR'] . "')") or die(mysql_error());
													echo '
														<div class="bloc2">
														  <h3><span>Etape 3/3 : Inscription r&eacute;ussi !</span></h3>
															<div class="texte">
																<font color="DodgerBlue"><strong>Mes identifiants</strong></font>
																<div class="separate"></div><br />
																<b>Votre pseudo : <font color="red">' . $pseudo . '</font><br />
																Votre mot de passe : <font color="red">' . $passe . '</font></b><br /><br />
																<div class="desavertissement"><center><b><a href="accueil.htm">Aller &agrave; l\'accueil</a></b></center></div>
															</div>
														</div>';
													$idPseudo = mysql_insert_id();
									
													// Envoi du mail
													$message = 'Bonjour, <br /><br />Merci à vous de vous être inscris sur SecretForum.<br /><br />Votre pseudo : ' . $pseudo . '<br />Votre mot de passe : ' . $passe . '<br /><br />Afin de valider votre compte, veuillez cliquer sur ce lien <a href="' . $adresseSite . 'inscription.php?pseudo=' . $idPseudo . '&clef=' . $clef . '">' . $adresseSite . 'inscription.php?pseudo=' . $idPseudo . '&clef=' . $clef . '</a><br /><br />Bonne continuation sur le site Secret Forum, passez d\'agréables moments.';
					 
													$message = '<html><head></head><body>' . $message . '</body></html>';
																				   
													$sujet = 'TechHub : Confirmation Inscription';
													$headers  = 'MIME-Version: 1.0' . "\r\n";
													$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
													$headers .= 'From: ' . $adresseSite . '';
											 
													mail($email, $sujet, $message, $headers);
													
													include('pied.php');
													exit;
												}
												else 
												{
													avert('Votre code de confirmation n\'est pas bon.');
												}
											}
											else 
											{
												avert('Votre code de confirmation est vide.');
											}
										}
										else
										{
											avert('Vous n\'avez pas accepté les conditions de la charte.');
										}
									}
									else
									{
										avert('Le pseudo est déjà choisi, veuillez en choisir un autre.');
									}
								}
								else
								{
									avert('Les 2 mots de passe que vous avez rentrés ne correspondent pas.');
								}
							}
							else
							{
								avert('Vous ne pouvez pas créer plus de 5 pseudo par adresse mail.');
							}
						}
						else
						{
							avert('L\'adresse email n\'est pas une adresse email valide.');
						}
					}
					else
					{
						avert('Le mot de passe n\'est pas un mot de passe valide.');
					}
				}
				else
				{
					avert('Le pseudo doit avoir entre 3 et 15 caractère.');
				}
			/* }
			else
			{
				avert(bbCode("Tu veux t'inscrire alors que les inscriptions sont fermer ? :onche:"));
			} */
		}
		else
		{
			avert('Vous n\'avez pas rempli tous les champs.');
		}
	}
// Fin - Ajouter Membre
	?>
<style type="text/css">
#weak, #medium
{
	border-right:solid 1px #DEDEDE;
}

#sm
{
	margin:0px;
	padding:0px;
	height:14px;
	font-family:Tahoma, Arial, sans-serif;
	font-size:9px;
}

#sm ul
{
	border:0px;
	margin:0px;
	padding:0px;
	list-style-type:none;
	text-align:center;
}

#sm ul li
{
	display:block;
	float:left;
	text-align:center;
	padding:0px 0px 0px 0px;
	margin:0px;
	height:14px;
}

.nrm
{
	width:84px;
	color:#adadad;
	text-align:center;
	padding:2px;
	background-color:#F1F1F1;
	display:block;
	vertical-align:middle;
}

.red
{
	width:84px;
	color:#FFFFFF;
	text-align:center;
	padding:2px;
	background-color:#FF6F6F;
	display:block;
	vertical-align:middle;
}

.yellow
{
	width:84px;
	color:#FFFFFF;
	text-align:center;
	padding:2px;
	background-color:#FDB14D;
	display:block;
	vertical-align:middle;
}

.green
{
	width:84px;
	color:#FFFFFF;
	text-align:center;
	padding:2px;
	background-color:#A0DA54;
	display:block;
	vertical-align:middle;
}
</style>
<script type="text/javascript">
function evalPwd(s)
{
	var cmpx = 0;
	
	if (s.length >= 6)
	{
		cmpx++;
		
		if (s.search("[A-Z]") != -1)
		{
			cmpx++;
		}
		
		if (s.search("[0-9]") != -1)
		{
			cmpx++;
		}
		
		if (s.length >= 8 || s.search("[\x20-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]") != -1)
		{
			cmpx++;
		}
	}
	
	if (cmpx == 0)
	{
		document.getElementById("weak").className = "nrm";
		document.getElementById("medium").className = "nrm";
		document.getElementById("strong").className = "nrm";
	}
	else if (cmpx == 1)
	{
		document.getElementById("weak").className = "red";
		document.getElementById("medium").className = "nrm";
		document.getElementById("strong").className = "nrm";
	}
	else if (cmpx == 2)
	{
		document.getElementById("weak").className = "yellow";
		document.getElementById("medium").className = "yellow";
		document.getElementById("strong").className = "nrm";
	}
	else
	{
		document.getElementById("weak").className = "green";
		document.getElementById("medium").className = "green";
		document.getElementById("strong").className = "green";
	}
}
</script>
<?php
if($_GET['etape'] == 1)
{
	echo'
	<div class="bloc2">
	  <h3>Etape 1/3 : Choix pseudo</h3>
	    <div class="texte">
		  <form method="post" action="inscription.php?etape=1">
			Inscription rapide, facile, gratuit. Le tout en moins d\'une minute seulement !<br /><br />
			<center><b><font color="red">* Choix du pseudo :</font></b> <input type="text" id="pseudo" name="pseudo" value="" /> <font color="grey" size="1"><b>(de 3 &agrave; 15 caract&egrave;res)</b></font></center><br />
			<div class="separate"></div><br />
			<center><input type="Submit" value="Etape suivante" class="bouton"/></center>
		  </form>
		</div>	
	</div>';
		
		include('pied.php');
		exit;
}
	if(!isset($_SESSION['m']['pseudal']))
	{
		$_SESSION['m']['pseudal'] = '';
	}
?>
<form method="post" action="inscription.php">	
<div class="desavertissement"><center><img src="images/gris/puce_liste_bleue.gif"> <a href="inscription.php?etape=1">Changer de pseudo</a></center></div><br />
	<div class="bloc2">
		<h3>Etape 2/3 : Informations Personnelles</h3>
		<div class="texte">
			<center>Les champs précédés d'une étoile <font color="red" size="4"><strong>*</strong></font> sont obligatoires.</center><br /><br />
			<font color="DodgerBlue"><strong>Mes identifiants</strong></font>
			<div class="separate"></div><br />
			<label for="pseudo"><font color="red">* Pseudo</font> :</label> <input readonly type="text" id="pseudo" name="pseudo" value="<?php echo $_SESSION['m']['pseudal']; ?>" /><br/>
			<label for="passe"><font color="red">* Mot de passe</font> :</label> <input id="passe" type="password" name="passe" onkeyup="evalPwd(this.value);" /><br />
			<div id="sm"><ul><li id="weak" class="nrm">Faible</li><li id="medium" class="nrm">Moyen</li><li id="strong" class="nrm">Fort</li></ul></div>
			<label for="passe2"><font color="red">* Confirmation</font> :</label> <input id="passe2" type="password" name="passe2" /><br/>
			<label for="email"><font color="red">* Adresse Email</font> :</label> <input id="email" type="text" name="email" value="<?php echo $email; ?>" /><br/><br />
			<font color="DodgerBlue"><strong>Mes informations</strong></font>
			<div class="separate"></div><br />
			<label for="sexe">Sexe :</label>
			<select id="sexe" name="sexe">
				<option value=""> - - - - - - - - - - - - - - - - - </option>
				<option value="m" <?php if($sexe == 'm') { echo 'selected="selected"'; } ?>>Masculin</option>
				<option value="f" <?php if($sexe == 'f') { echo 'selected="selected"'; } ?>>Feminin</option>
			</select>
			<br />
			<label>Age :</label>
			<select id="age" name="age" width="250px">
				<option value=""> - - - - - - - - - - - - - - - - - </option>
				<?php
				for($i = 1; $i <= 99; $i++)
				{
					echo '<option value="' . $i . '"'; if($age == $i) { echo 'selected="selected"'; } echo'>' . $i . '</option>';
				}
				?>
			</select>
			<br />
			<label>Pays :</label>
			<select id="pays" name="pays">
				<option value=""> - - - - - - - - - - - - - - - - - </option>
				<option value="France" <?php if($pays == 'France') { echo 'selected="selected"'; } ?>>France</option>
				<option value="Allemagne" <?php if($pays == 'Allemagne') { echo 'selected="selected"'; } ?>>Allemagne</option>
				<option value="Belgique" <?php if($pays == 'Belgique') { echo 'selected="selected"'; } ?>>Belgique</option>
				<option value="Suisse" <?php if($pays == 'Suisse') { echo 'selected="selected"'; } ?>>Suisse</option>
				<option value="Quebec" <?php if($pays == 'Quebec') { echo 'selected="selected"'; } ?>>Quebec</option>
				<option value="Canada" <?php if($pays == 'Canada') { echo 'selected="selected"'; } ?>>Canada</option>
				<option value="Luxembourg" <?php if($pays == 'Luxembourg') { echo 'selected="selected"'; } ?>>Luxembourg</option>
			</select>
			<br />
			
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
			<label for="pres">Présentation :<br /> <font color=red><span id="carac_reste_pres_1"></span></font> caractères.</label> <textarea id="pres" name="presentation" rows="7" cols="30" value="<?php echo $presentation; ?>"></textarea>
			<br />
			<label>Afficher votre email (CDV) :</label>
			<b>Non :</b> <input type=radio name="emailVu" value="0" /> <b>Oui :</b> <input type=radio name="emailVu" value="1" /><br /><br />
			<font color="DodgerBlue"><strong>Mon avatar</strong></font>
			<div class="separate"></div><br />
			<label>Lien de votre avatar :</label> <input type="text" name="avatar" value="<?php echo $avatar; ?>"/><br /><br />
			<font color="DodgerBlue"><strong>Ma signature</strong></font>
			<div class="separate"></div><br />
			<script type="text/javascript"> 
			maxlength_textarea('pres','carac_reste_pres_1',250);
			</script> 
			<label>Signature :</label> <br /><textarea name="signature" cols="60" rows="5"><?php echo $signature; ?></textarea><br /><br />
			<center><b><span style="color: red;">* J'ai pris connaissance et j'adhère à la</span> <a href="charte.html" target="_blank">charte d'utilisation des forums</a> <form action="" methed="post"><input type="checkbox" name="charte" onClick="ChangeStatut(this.form)" /></b></center>
			<center><b><span style="color: red;">* Code de confirmation :</span></b> <input type="text" name="verif_code" size="6" maxlength="6"/> <img src="verif_code_gen.php" alt="Code de vérification" /></center><br />
			<center><input name="validation" type="submit" value="Inscription" class="bouton" disabled /></center>
		</div>
	</div>
</form>
<script type="text/javascript">
function ChangeStatut(formulaire) {
if(formulaire.charte.checked == true) {formulaire.validation.disabled = false }
if(formulaire.charte.checked == false) {formulaire.validation.disabled = true }
}
</script>
<?php

include('pied.php');
?>