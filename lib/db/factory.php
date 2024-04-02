<?php
class PDOFactory {
 public static function getMysql($host, $user, $pass, $data) { $db = new PDO("mysql:host=".$host.";dbname=".$data, $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);           
        return $db; } public static function getPgsql($host, $user, $pass, $data)
        { $db = new PDO("pgsql:host=".$host.";dbname=".$data, $user, $pass);
          $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          return $db; } }
?>