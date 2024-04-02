<?php
include('base.php');

if(isset($_GET['id']) && $_GET['id'] != NULL) {
	
mysql_query('INSERT INTO inscriptionCours VALUES ("", "'.$_GET['id'].'", "'.$_SESSION['m']['id'].'")');

}
header('Location: liste-cours.php');
?>