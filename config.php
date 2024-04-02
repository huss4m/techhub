<?php
function secure($chaine) // sécuriser
	{
		return mysql_real_escape_string($chaine);
	}

	function redirection($url)
	{
		echo "<script type=\"text/javascript\">\n"
		. "<!--\n"
		. "\n"
		. "function redirect() {\n"
		. "window.location='" . html_entity_decode($url) . "'\n"
		. "}\n"
		. "setTimeout('redirect()','0000');\n"
		. "\n"
		. "// -->\n"
		. "</script>\n";
	}

/*  { Debut - Fonction des liens */
		
	define('TOPIC_URL', 1); // Constante pour classe Lnk
	define('FORUM_URL', 2); // Constante pour classe Lnk
	
	function titre_url_parse($mot) // Transformer les URL
	{
		$mot = preg_replace('#[^a-z0-9_-]#i', '-', $mot);
		$mot = preg_replace('#[-]{2,}#', '-', $mot);
		$mot = preg_replace('#^-#', '', $mot);
		$mot = preg_replace('#-$#', '', $mot);
		$mot = strtolower($mot);
		return $mot;
	}
	
	function have_url($ID, $page, $MODE, $titre = NULL) // Changer les URL
	{
		
		if(ctype_digit($ID)) // Voir si l'id est valide
		{
			if($MODE == 1) // Topic 
			{
				$TYPE = 'topic';
			
			}
			elseif($MODE == 2) // Forum
			{
				$TYPE = 'forum';
			}
			else
			{
				return 'erreur.php?erreur=2';
			}
			
				if(!empty($titre))
				{
					return $TYPE . '-' . $ID . '-' . $page . '-' . titre_url_parse($titre) . '.htm';
				}
				else
				{
					return 'erreur.php?erreur=3';
				}
				
			
		
		}
		else
		{
			return 'erreur.php?erreur=1';
		}
		
		
	}

/*	} Fin - Fonction des liens */

	function debutchaine($chaine, $nbmots) 
	{
		$chaine = preg_replace('!<br.*>!iU', "", $chaine);
		$chaine = strip_tags($chaine);
		$chaine = preg_replace('/\s\s+/', ' ', $chaine);
		$tab = explode(" ",$chaine);
		if (count($tab) <= $nbmots)
		{
			$affiche = $chaine;
		}
		else
		{
			$affiche = "$tab[0]"; 
			for ($i=1; $i<$nbmots; $i++) 
			{ 
				$affiche .= " $tab[$i]"; 
			}
		}
		$affiche .= '...';
		return $affiche;
	}

	function text_i($str)
	{
		return htmlspecialchars(stripslashes($str));
	}
	
	function ddate($i)
	{
		
		if(empty($i))
		{
			$i = time();
		}
		
		if(!ctype_digit($i))
		{
			return 'false';
		}
		
		$timestamp = $i;
		// Tableau des mois en français
		$mois_fr = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
		
		list($jour, $mois, $annee) = explode('/', date('d/n/Y', $timestamp));
		
		$dd = $jour . ' ' . $mois_fr[$mois - 1] . ' ' . $annee . ' à ' . date('H\:i\:s', $timestamp);
		
		return $dd;
	}
	
	
	function ndate($i)
	{
		
		if(empty($i))
		{
			$i = time();
		}
		
		if(!ctype_digit($i))
		{
			return 'false';
		}
		
		$timestamp = $i;
		
		list($jour, $mois) = explode('/', date('d/m', $timestamp));
		
		$dd = $jour . '/' . $mois . ' ';
		
		return $dd;
	}
	
	function page($LIEN, $page, $nbPagesPossibles, $TYPE = NULL)
	{
		if($nbPagesPossibles > 1 OR !$nbPagesPossibles) // S'il y a plus d'une page
		{
			$affichage = '<div class="page">' ;
			
			$preums = $page ;
			
			while($preums % 30 != 0)
			{
				$preums--;
			}
			
			$affichage .= '<div class="pageBas">' ;
			
			// On commence par calculer la première page qu'on affiche
			$pageDebut = $page - 5 ;
			
			if($pageDebut < 1) // Si on tombe dans les négatifs, alors on se remet sur le droit chemin
			{
				$pageDebut = 1 ;
			}
			
			if($nbPagesPossibles - $page < 5 AND ($nbPagesPossibles - 9 > 1)) // Si on arrive vers la fin, on fait bien attention à tout de même afficher 10 pages
			{
				$pageDebut = $nbPagesPossibles - 9 ;
			}
			
			// { Début - Affichage des pages
				if($pageDebut != 1) // Si on affiche pas la première page dans la boucle, on l'ajoute quand même
				{
					$affichage .= '<a href="' . $LIEN . '-1.html">1</a>' ;
					
					if($pageDebut != 2) // Si on est sur la deuxième page, on affiche pas le "..."
					{
						$pageRetro = $page - 10 ; // On calcule la page que renverront les "..."
						
						if($pageRetro < 2) // Il ne faut pas que la page rétro soit 1, sinon c'est inutile, donc on fixe une limite minimum de 2
						{
							$pageRetro = 2 ;
						}
						
						$affichage .= ' <a href="' . $LIEN . '-' . $pageRetro . '.html">...</a> ' ;
					}
					else
					{
						$affichage .= ' | ' ;
					}
				}
				
				$i = $pageDebut ;
				$n = 0 ;
				while($n < 10 AND $i <= $nbPagesPossibles)
				{
					if($page == $i)
					{
						$affichage .= '<a href="' . $LIEN . '-' . $i . '.html" class="pageActuelle">' . $i . '</a>' ;
					}
					else
					{
						$affichage .= '<a href="' . $LIEN . '-' . $i . '.html">' . $i . '</a>' ;
					}
					
					if(($n + 1 < 10) AND ($i != $nbPagesPossibles))
					{
						$affichage .= ' | ' ;
					}
					
					$i++;
					$n++;
				}
				
				$i--;
				
				if($i != $nbPagesPossibles) // Si on est pas arrivé sur la dernière page, on l'affiche tout de même
				{
					if($i + 1 != $nbPagesPossibles) // Si on est sur l'avant dernière page, on affiche pas les "..."
					{
						$pageRetro = $page + 10 ;
						
						if($pageRetro >= $nbPagesPossibles)
						{
							$pageRetro = $nbPagesPossibles - 1 ;
						}
						
						$affichage .= ' <a href="' . $LIEN . '-' . $pageRetro . '.html">...</a>' ;
					}
					else
					{
						$affichage .= ' |' ;
					}
					
					$affichage .= ' <a href="' . $LIEN . '-' . $nbPagesPossibles . '.html">' . $nbPagesPossibles . '</a>' ;
				}
			// } Fin - Affichage des pages
			
			$affichage .= '</div>' ;
			
			$affichage .= '</div>' ;
		}
		
		return $affichage;
	}
	
	// { Début - Récupération de l'adresse exacte du site
		$explode = explode('/', $_SERVER['PHP_SELF']);
		$nb = count($explode);
		unset($explode[$nb - 1]);
		$PHP_SELF = implode('/', $explode);
		$adresseSite = str_replace(' ', '%20', 'http://' . $_SERVER['HTTP_HOST'] . $PHP_SELF . '/') ;
	// } Fin - Récupération de l'adresse exacte du site

	
	// Fonction avertissement (Modif, erreur, ...)
	function avert($texte)
	{
		echo '
		<div class="avertissement">
		' . $texte . '
		</div>
		';
	}
	
$cfg = dirname(__FILE__).'/lib/db';
$dossier = opendir($cfg);
while($fichier = readdir($dossier)){
    if(is_file($cfg.'/'.$fichier) && $fichier !='/' && $fichier !='.' && $fichier != '..' && $fichier <> "factory.php"){
		require_once $cfg.'/'.$fichier;
    }
}
closedir($dossier);
$bdd = new db_sql();
$bdd->db = $db;
$DNS = array(
"id" => array ( intval($_SESSION['m']['id']) ),
"pseudo" => array ( db_sql::escape($_SESSION['m']['pseudo']) ),
"acces" => array ( intval($_SESSION['m']['acces']) ),
"getid" => array ( intval($_GET['id']) )
);

	// Smileys
	$GLOBALS['smileys'] = array(':mario:' => 'mario', ':)' => '=)', ':o))' => '=o))', ':hap:' => 'hap', ':content:' => 'content', ':cool:' => 'cool', ':rire2:' => 'rire2', ':sournois:' => 'sournois', ':gni:' => 'gni', ':merci:' => 'merci', ':rechercher:' => 'rechercher', ':gne:' => 'gne', ':snif2:' => 'snif2', ':ouch:' => 'ouch', ':ouch2:' => 'ouch2', ':nonnon:' => 'nonnon', ':non2:' => 'non2', ':non:' => 'non', ':nah:' => 'nah', ':hum:' => 'hum', ':bravo:' => 'bravo', ':svp:' => 'svp', ':hello:' => 'hello', ':lol:' => 'lol', ':gba:' => 'gba', ':mac:' => 'mac', ':pacg:' => 'pacg', ':pacd:' => 'pacd', ':fier:' => 'fier', ':malade:' => 'malade', ':ange:' => 'ange', ':desole:' => 'desole', ':sors:' => 'sors', ':up:' => 'up', ':dpdr:' => 'dpdr', ':cd:' => 'cd', ':globe:' => 'globe', ':question:' => 'question', ':mort:' => 'mort', ':sleep:' => 'sleep', ':honte:' => 'honte', ':monoeil:' => 'monoeil', ':diable:' => 'diable', ':spoiler:' => 'spoiler', ':bye:' => 'bye', ':hs:' => 'hs', ':banzai:' => 'banzai', ':bave:' => 'bave', ':xd:' => 'xd', ':(' => '=(', ':-D' => '=-D', ':d)' => '=d)', ':g)' => '=g)', ':p)' => '=p)', ':salut:' => 'salut', ':fete:' => 'fete', ':noel:' => 'noel', ':rire:' => 'rire', ':-p' => '=-p', ':fou:' => 'fou', ':coeur:' => 'coeur', ':rouge:' => 'rouge', ':oui:' => 'oui', ':dehors:' => 'dehors', ':peur:' => 'peur', ':ok:' => 'ok', ':sarcastic:' => 'sarcastic', ':doute:' => 'doute', ':snif:' => 'snif', ':fouet:' => 'fouet', ':sortie:' => 'sortie', ':vague:' => '=s', ':jbsd:' => 'jbsd', ':-)))' => '=-)))', ':-((' => '=-((');	
	
	// Smileys supplémentaires
	$dl = $bdd->sql_query('SELECT code, lien FROM smileys');
	
	while($ligne = $bdd->sql_fetch($dl))
	{
		
		$GLOBALS['smileys'][$ligne['code']] = $ligne['lien'];
		
		
	}
	
	function nojs($txt) // interdiction de html, mais supression du javascript, comme rickrolled ou connerie du genre
	{
		$txt = preg_replace('#javascript:([^ \n\r]*)#i', '', $txt);
		$txt = preg_replace('#alert("(.+)")#iU', '', $txt);
		return $txt;
	}
	
	function dict($txt)
	{
		global $bdd;
		$sql = $bdd->sql_query('SELECT mot FROM mots');
		
		while($m = $bdd->sql_fetch($sql))
		{
			
			$MOT = preg_quote($m['mot']);
			
			if(preg_match('#' . $MOT . '#i', $txt))
			{
				return false;
			}
			
			
		}
		
		return true;
		
	}
function prismCodeColor($text) {

  $bbcodeTags = [
    '/\[code\](.*?)\[\/code\]/is' => '<pre><code class="language-none">$1</code></pre>',
    '/\[code=(.*?)\](.*?)\[\/code\]/is' => '<pre><code class="language-$1">$2</code></pre>',
  ];


  foreach ($bbcodeTags as $pattern => $replacement) {
    $text = preg_replace($pattern, $replacement, $text);
  }

  return $text;
}
	function bbCode($t, $SMILEY = 0) // remplace les balises BBCode par des balises HTML
	{
		$t = nojs($t);
		
		if(!$SMILEY)
		{
			// balise [code] avec coloration
			$t = prismCodeColor($t);
			
			
			// gras
			$t = preg_replace('#\[b\](.+?)\[/b\]#is', '<b>$1</b>', $t);
		   
			// italique
			$t = preg_replace('#\[i\](.+?)\[/i\]#is', '<i>$1</i>', $t);
		   
			// soulignement
			$t = preg_replace('#\[u\](.+?)\[/u\]#is', '<u>$1</u>', $t);
			
			// citer
			while(preg_match('#\[citer\](.+?)\[/citer\]#isU', $t))
			{
				$t = preg_replace('#\[citer\](.+?)\[/citer\]#isU', '<div class="citer">$1</div>', $t);
			}
			
			// centre
			$t = preg_replace('#\[center\](.+?)\[/center\]#is', '<center>$1</center>', $t);
			
			// puissance
			$t = preg_replace('#\[sup\](.+?)\[/sup\]#is', '<sup>$1</sup>', $t);
			
			// indice
			$t = preg_replace('#\[sub\](.+?)\[/sub\]#is', '<sub>$1</sub>', $t);
			
			// barre
			$t = preg_replace('#\[s\](.+?)\[/s\]#is', '<s>$1</s>', $t);

			
			// couleur
			$t=str_replace("[/color]", "</span>", $t);
			$regCouleur="\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]";
			$t=ereg_replace($regCouleur, "<span style=\"color: \\1\">", $t);
			
			// Cacher du texte
			$t = preg_replace('#\[cacher\](.+?)\[/cacher\]#is', '<div style="margin:20px; margin-top:5px"><div class="spoilertexte"><input class="button2 btnlite" type="button" value="Lire" style="text-align:center;width:100px;margin:0px;padding:0px;" onclick="if (this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'\'; this.innerText = \'\'; this.value = \'Cacher\'; } else { this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display = \'none\'; this.innerText = \'\'; this.value = \'Montrer\'; }" /></div><div class="spoiler"><div style="display: none;">$1</div></div></div>', $t);
		
			// Faire bouton
			$t = preg_replace('#\[bouton\](.+?)\[/bouton\]#is', '<input type="button" value="$1" />', $t);
			
			// Faire une infobulle
			$t = preg_replace('#\[info\](.+?),(.+?)\[/info\]#is', '<acronym title="$2" style="font-weight: bold;">$1</acronym>', $t);
			
			// hr
			$t = preg_replace('#\[hr\]#is', '<hr />', $t);
			
			// Alignemment justifié
			$t = preg_replace('#\[justifier\](.+?)\[/justifier\]#is', '<div align="justify">$1</div>', $t);

			// deplacer
			$t = preg_replace('#\[bas\](.+?)\[/bas\]#is', '<div style="height: 20px;"><marquee direction="down">$1</marquee></div>', $t);
			$t = preg_replace('#\[haut\](.+?)\[/haut\]#is', '<div style="height: 20px;"><marquee direction="up">$1</marquee></div>', $t);
			$t = preg_replace('#\[droite\](.+?)\[/droite\]#is', '<marquee direction="right">$1</marquee>', $t);
			$t = preg_replace('#\[gauche\](.+?)\[/gauche\]#is', '<marquee direction="left">$1</marquee>', $t);
			
			// clignoter
			$t = preg_replace('#\[clignote\](.+?)\[/clignote\]#is', '<blink>$1</blink>', $t);

			// taille
			$t = preg_replace('`\[taille=(10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25)\](.+)\[/taille\]`isU', '<span style="font-size:$1px">$2</span>', $t);
		}
		
		foreach($GLOBALS['smileys'] as $code => $chemin)
		{
			$t = str_replace($code, '<img src="../smileys/' . $chemin . '.gif" class="smiley" />', $t);
		}
			
		$t = preg_replace('#:-\(#', '<img src="../smileys/=-(.gif" alt=":-(" class="smiley" />', $t) ;
		$t = preg_replace('#:-\)#', '<img src="../smileys/=-).gif" alt=":-)" class="smiley" />', $t) ;
		$t = str_replace('<img src="../smileys/=-).gif" alt=":-)" class="smiley" />))" class="smiley" />', '<img src="../smileys/=-))).gif" alt=":-)))" class="smiley" />', $t) ; // :-)))
		$t = str_replace('<img src="../smileys/=-(.gif" alt=":-(" class="smiley" />(" class="smiley" />', '<img src="../smileys/=-((.gif" alt=":-((" class="smiley" />', $t) ; // :-((
		
		if($SMILEY)
		{
			return $t;
		}
		
		// Lien et adresse e-mail
		$callbackLien = create_function('$array', '
				
				$LIEN = $array[0] ;
					
				$nb = mb_strlen($LIEN);
					
				if(preg_match(\'#^http#i\', $LIEN)) // Lien
				{
					$LIEN = \'<a href="\' . $array[0] . \'" target="_blank">\' ;
				}
				else // Email
				{
					$LIEN = \'<a href="mailto:\' . $array[0] . \'">\' ;
				}
					
				if($nb < 61)
				{
					$LIEN .= $array[0];
				}
				else
				{
					$LIEN .= mb_substr($array[0], 0, 20) . \'[...]\' . mb_substr($array[0], ($nb - 20), 20) ;
				}
					
				$LIEN .= \'</a>\' ;
					
				return $LIEN;
			') ;
			
			$t = preg_replace('#\[img\] ?<a href="(http://[^ \n\r\'"]+\.(jpg|png|gif|jpeg))([^\n\r]+)\[/img\]</a>#isU', '<center>' . $REGEXR. '</center>', $t);
			
			$t = preg_replace_callback('#http://[^ \n\r\'"]+#i', $callbackLien, $t);
			$t = preg_replace_callback('#[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}#', $callbackLien, $t);
	   
			// lien
   $regLienSimple="\[url\] ?([^\[]*) ?\[/url\]";
   $regLienEtendu="\[url ?=([^\[]*) ?] ?([^]]*) ?\[/url\]";
   if (ereg($regLienSimple, $t)) $t=ereg_replace($regLienSimple, "<a href=\"\\1\">\\1</a>", $t);
   else $t=ereg_replace($regLienEtendu, "<a href=\"\\1\" target=\"_blank\">\\2</a>", $t);
		return $t;
	}
	
	function rang($nbpost, $acces)
	{
		$posts = array(
		50 => '<strong>Nouveau</strong>', 
		100 => '<strong>Initié</strong>',
		200 => '<strong>Habitu&eacute;</strong>',
		500 => '<strong>Membre confirm&eacute;</strong>',
		750 => '<strong>Actif</strong>',
		1000 => '<strong>Super Actif</strong>',
		1500 => '<strong>Hyper Actif</strong>',
		3000 => '<strong>Roi</strong>',
		5000  => '<strong>Empereur</strong>',
		'max' =>  '<strong>Dieu</strong>',
		'modo' => '<strong class="moderateur" style="color: blue;">Mod�rateur</strong>',
		'admin' => '<strong class="admin" style="color: red;">Administrateur</strong>',
		'supermodo' => '<strong class="moderateur" style="color: darkorange;">Super Modo</strong>',
		'directeur' => '<strong class="moderateur" style="color: darkred;">Directeur</strong>',
		'codeur' => '<strong class="moderateur" style="color: green;">Codeur</strong>');
	
		if($acces == 50)
		{
			return $posts['modo'];
		}
		elseif($acces == 100)
		{
			return $posts['admin'];
		}
		elseif($acces == 90)
		{
			return $posts['supermodo'];
		}
		elseif($acces == 95)
		{
			return $posts['directeur'];
		}
		elseif($acces == 98)
		{
			return $posts['codeur'];
		}
		else
		{
			foreach($posts as $nb => $rang)
			{
				if($nb > $nbpost OR $nb == 'max')
				{
					return $rang;
				}
			}
		}
	}
	
	class Color
	{
		var $colora = '#cc0000';
		var $colorm = '#000000';
		var $colormo = 'blue';
		var $colormod = 'darkorange';
		var $colordir = 'grey';
		var $colorod = 'green';
		
		public function setColor($membre, $modo, $admin, $supermodo, $directeur, $codeur)
		{
			if(!empty($membre))
			{
				$this->colorm = $membre;
			}
			if(!empty($modo))
			{
				$this->colormo = $modo;
			}
			if(!empty($admin))
			{
				$this->colora = $admin;
			}
			if(!empty($supermodo))
			{
				$this->colormod = $supermodo;
			}
			if(!empty($directeur))
			{
				$this->colordir = $directeur;
			}
			if(!empty($codeur))
			{
				$this->colorod = $codeur;
			}
		}
		
		public function afficher($acces, $pseudo)
		{
			if($acces == 100)
			{
				return '<span style="color: ' .($this->colora) . ';">' . $pseudo . '</span>';
			}
			elseif($acces == 50)
			{
				return '<span style="color: ' .($this->colormo) . ';">' . $pseudo . '</span>';
			}
			elseif($acces == 90)
			{
				return '<span style="color: ' .($this->colormod) . ';">' . $pseudo . '</span>';
			}
			elseif($acces == 95)
			{
				return '<span style="color: ' .($this->colordir) . ';">' . $pseudo . '</span>';
			}
			elseif($acces == 98)
			{
				return '<span style="color: ' .($this->colorod) . ';">' . $pseudo . '</span>';
			}
			else
			{
				return '<span style="color: ' . ($this->colorm) . ';">' .  $pseudo . '</span>';
			}
		}
	}
	
function page_i($LIEN, $page, $nbPagesPossibles)
{
if($nbPagesPossibles > 1 OR !$nbPagesPossibles) // S'il y a plus d'une page
{
$affichage = '<div class="page">' ;

$preums = $page ;

while($preums % 30 != 0)
{
$preums--;
}

$affichage .= '<div class="pageBas">' ;

// On commence par calculer la première page qu'on affiche
$pageDebut = $page - 5 ;

if($pageDebut < 1) // Si on tombe dans les négatifs, alors on se remet sur le droit chemin
{
$pageDebut = 1 ;
}

if($nbPagesPossibles - $page < 5 AND ($nbPagesPossibles - 9 > 1)) // Si on arrive vers la fin, on fait bien attention � tout de même afficher 10 pages
{
$pageDebut = $nbPagesPossibles - 9 ;
}

// { Début - Affichage des pages
if($pageDebut != 1) // Si on affiche pas la première page dans la boucle, on l'ajoute quand même
{
$affichage .= '<a href="' . $LIEN . '">1</a>' ;

if($pageDebut != 2) // Si on est sur la deuxième page, on affiche pas le "..."
{
$pageRetro = $page - 10 ; // On calcule la page que renverront les "..."

if($pageRetro < 2) // Il ne faut pas que la page rétro soit 1, sinon c'est inutile, donc on fixe une limite minimum de 2
{
$pageRetro = 2 ;
}

$affichage .= ' <a href="' . $LIEN . '' . $pageRetro . '">...</a> ' ;
}
else
{
$affichage .= ' | ' ;
}
}

$i = $pageDebut ;
$n = 0 ;
while($n < 10 AND $i <= $nbPagesPossibles)
{
if($page == $i)
{
$affichage .= '<a href="' . $LIEN . '' . $i . '" class="pageActuelle">' . $i . '</a>' ;
}
else
{
$affichage .= '<a href="' . $LIEN . '' . $i . '">' . $i . '</a>' ;
}

if(($n + 1 < 10) AND ($i != $nbPagesPossibles))
{
$affichage .= ' | ' ;
}

$i++;
$n++;
}

$i--;

if($i != $nbPagesPossibles) // Si on est pas arrivé sur la dernière page, on l'affiche tout de même
{
if($i + 1 != $nbPagesPossibles) // Si on est sur l'avant dernière page, on affiche pas les "..."
{
$pageRetro = $page + 10 ;

if($pageRetro >= $nbPagesPossibles)
{
$pageRetro = $nbPagesPossibles - 1 ;
}

$affichage .= ' <a href="' . $LIEN . '' . $pageRetro . '">...</a>' ;
}
else
{
$affichage .= ' |' ;
}

$affichage .= ' <a href="' . $LIEN . '' . $nbPagesPossibles . '">' . $nbPagesPossibles . '</a>' ;
}
// } Fin - Affichage des pages

$affichage .= '</div>' ;

$affichage .= '</div>' ;
}

return $affichage;
}
//Fonction pour g�rer des chaines al�atoire
function random($car) {
$string = "";
$chaine = "abcdefghijklmnpqrstuvwxy";
srand((double)microtime()*1000000);
for($i=0; $i<$car; $i++) {
$string .= $chaine[rand()%strlen($chaine)];
}
return $string;
}
// Fin - Déclaration des Fonctions
?>