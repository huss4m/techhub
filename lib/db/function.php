<?php
class db_sql {
public $db, $prefix;
function __set($var, $val) { $this->$var = $val; }
function __construct() { $info = parse_ini_file(dirname(__FILE__)."/../../ini_config/sql.ini"); $this->prefix = $info["prefix"]; }
public function sql_query($query, $fetch = false, $exec = false, $secure = false, $array = null) {
		$var = "";
		$return = "";
		$query = str_replace("%p", $this->prefix, $query);
		if($exec <> false) { return $this->db->exec($query); }
		if($fetch <> false && !$secure) { $var = $this->db->query($query); return $var->fetch(); }
		if($secure <> false && $fetch && !empty($array) && is_array($array)) { $var = $this->db->prepare($query); $var->execute($array); return $var->fetch(); }
		if($secure <> false && $exec && !empty($array) && is_array($array)) { $var = $this->db->prepare($query); return $var->execute($array); }
		if(!$fetch && !$secure && !$exec) { return $this->db->query($query); }
	}
public static function escape($string) { return mysql_real_escape_string($string); }
public function sql_fetch($query) { return $query->fetch(); }
public function sql_count($query) { return $query->rowCount(); }
}
?>