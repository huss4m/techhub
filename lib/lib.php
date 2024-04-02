<?php
function lib($file, $once = FALSE) {
$path = (strpos(dirname(__FILE__), "lib") !== false) ? dirname(__FILE__) : "/lib";
switch($once) {
case TRUE:
require_once $path."/$file.php";
break;
default:
require $path."/$file.php";
}
}
?>