<?php
class Rss {
//Flux RSS By Little-Mac
var $_mime = "application/rss+xml";
public $type;
public $array_type = array(
							"message" => "messages",
							"news" => "articles",
							"topic" => "topics"
						   );
public $title;
public $link;
public $db;
public function __set($var, $value) { $this->$var=$value; }
public function rss_open($file) {
return fopen($file, "w+"); }
public function start() {
$stone = $this->array_type;
$fide = "<rss version=\"2.0\"><channel>\n";
$fide .= "<title>Flux RSS de ".$this->title."</title>\n";
$fide .= "<link>".$this->link."</link>\n";
$fide .= "<description>Soyez au courant des derniers ".$stone[$this->type]." du site</description>\n";
return $fide; }
public function rss_each() {
$connect = $this->db;
$contenu="";
$rand = $this->type;
$reponse=$connect->query("SELECT * FROM $rand ORDER BY id DESC");
switch($rand) {
case "topic":
$i=1;
while($donnees=$reponse->fetch()) {
$contenu .= "<item>\n";
$contenu .= "<title>".htmlspecialchars($donnees['titre'], ENT_COMPAT, 'UTF-8')." | Par: ".htmlspecialchars($donnees['pseudo'], ENT_COMPAT, 'UTF-8')." | Nombre de réponses: ".htmlspecialchars($donnees['nbMessage'], ENT_COMPAT, 'UTF-8')."</title>\n";
$contenu .= "<link>".$this->link."/topic.php?id=".$donnees['id']."</link>\n";
$contenu .= "<description>";
$basix=$connect->query("SELECT * FROM message WHERE statut=1 AND idTopic='".mysql_real_escape_string($donnees['id'])."'");
$base=$basix->fetch();
$contenu .= $base["message"];
$contenu .= "</description></item>\n";
if($i > 35) { break; }
$i++;
}
$basix->closeCursor();
break;
case "news":
$i=1;
while($donnees=$reponse->fetch()) {
$contenu .= "<item>\n";
$contenu .= "<title>".htmlspecialchars($donnees['titre'], ENT_COMPAT, 'UTF-8')." | Par: ".$donnees['auteur']."</title>\n";
$contenu .= "<link>".$this->link."/news-".$donnees['id'].".html</link>\n";
$contenu .= "<description>";
$contenu .= substr(htmlspecialchars($donnees['news'], ENT_COMPAT, 'UTF-8'),0,182)."...";
$contenu .= "</description></item>\n";
if($i > 35) { break; }
$i++;
}
break;
case "message":
$i=1;
while($donnees=$reponse->fetch()) {
$contenu .= "<item>\n";
$contenu .= "<title>Message posté dans le topic \"".htmlspecialchars($donnees['TitreTopic'], ENT_COMPAT, 'UTF-8')."\" Par: ".$donnees['pseudo']."</title>\n";
$contenu .= "<link>".$this->link."/topic.php?id=".$donnees['idTopic']."#message_".$donnees['id']."</link>\n";
$contenu .= "<description>";
$contenu .= substr(htmlspecialchars($donnees['message'], ENT_COMPAT, 'UTF-8'),0,182)."...";
$contenu .= "</description></item>\n";
if($i > 35) { break; }
$i++; }
break;
} return $contenu; }
public function verif_type() {
if(in_array($this->type, $this->array_type)) return true;
else return false; }

 
public function afficher() {
$fopen = $this->rss_open(dirname(dirname(__FILE__))."/rss-".$this->type.".xml");
$debut = $this->start();
$debut .= $this->rss_each();
$debut .= "</channel>\n</rss>";
fwrite($fopen, $debut);
}
}
?>