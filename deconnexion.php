<?php
include('base.php');

if(!isset($_SESSION['m']['acces']))
{
	// si pas connecté
	include('tete.php');
	avert('Vous n\'êtes pas connecté !');
	include('pied.php');
	exit;
	
}

session_destroy();
$_SESSION = array();
header('Location: accueil.htm');
exit;
?>
