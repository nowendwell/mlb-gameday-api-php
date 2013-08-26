<?php
include "functions.php";
$url = "http://gd2.mlb.com/components/game/mlb/".$date."/scoreboard.xml";
$json = json_decode(XmlToJson::Parse($url), true);
$team_search = ArraySearchRecursive($team,$json);
if (is_numeric($team_search[1])){$json = $json[$team_search[0]][$team_search[1]];}else{$json = $json[$team_search[0]];}
$gid = (!empty($_GET["gid"]))?"gid_".$_GET["gid"]:"gid_". $json['game']['@attributes']['id'];
if (isset($_GET["game"])){
	$explode = explode("_", "gid_". $json['game']['@attributes']['id']);
	unset($explode[6]);
	$gid = implode("_",$explode)."_".$_GET["game"];
}

$url = "http://gd2.mlb.com/components/game/mlb/".$date."/".$gid."/linescore.json";
$json = json_decode(file_get_contents($url),true);
if ($_GET["show"]){echo "<pre>";@ksort($json['data']['game']);print_r($json);echo "</pre>";}
?>