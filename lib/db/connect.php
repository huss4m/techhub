<?php
$info = parse_ini_file(dirname(__FILE__)."/../../ini_config/sql.ini");
extract($info);
require dirname(__FILE__) ."/factory.php";
global $db;
if($system=="pqsql")
$db = PDOFactory::getPgsql($host, $user, $pass, $data);
else $db = PDOFactory::getMysql($host, $user, $pass, $data);

mysql_connect($host, $user, $pass) or die("ID MySql Eronées");;
mysql_select_db($data);
mysql_unbuffered_query('SET NAMES UTF8');
?>