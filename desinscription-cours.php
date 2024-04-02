<?php
include('base.php');

if(isset($_GET['id']) && $_GET['id'] != NULL) {
	
mysql_query('DELETE FROM inscriptionCours WHERE idCours="'.$_GET['id'].'" AND idPseudo="'.$_SESSION['m']['id'].'"');

}
header('Location: liste-cours.php');
?>