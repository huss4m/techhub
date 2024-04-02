<?php
// On cr�e la session avant tout
session_start();

// On d�fini la configuration :
if(!isset($_GET['nbr_chiffres'])) 
{
    $nbr_chiffres = 4; // Nombre de chiffres qui formerons le nombre par d�faut
}
else 
{
    $nbr_chiffres = $_GET['nbr_chiffres']; // Si l'on met dans l'adresse un ?nbr_chiffres=?
}

// L�, on d�fini le header de la page pour la transformer en image
header ("Content-type: image/png");
// L�, on cr�e notre image
$_img = imagecreatefrompng('images/fond_verif_img.png');

// On d�fini maintenant les couleurs
// Couleur de fond :
$arriere_plan = imagecolorallocate($_img, 0, 0, 0); // Au cas o� on utiliserai pas d'image de fond, on utilise cette couleur l�.
// Autres couleurs :
$avant_plan = imagecolorallocate($_img, 255, 255, 0); // Couleur des chiffres

##### Ici on cr�e la variable qui contiendra le nombre al�atoire #####
$i = 0;
while($i < $nbr_chiffres) 
{
        $chiffre = mt_rand(0, 9); // On g�n�re le nombre al�atoire
        $chiffres[$i] = $chiffre;
        $i++;
}

$nombre = null;

// On explore le tableau $chiffres afin d'y afficher toutes les entr�es qu'y s'y trouvent
foreach ($chiffres as $caractere) 
{
        $nombre .= $caractere;
}

##### On as fini de cr�er le nombre al�atoire, on le rentre maintenant dans une variable de session #####
$_SESSION['aleat_nbr'] = $nombre;
// On d�truit les variables inutiles :
unset($chiffre);
unset($i);
unset($caractere);
unset($chiffres);

imagestring($_img, 5, 7, 2, $nombre, $avant_plan);

imagepng($_img); 
?>
