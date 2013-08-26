<?php
include "config.php";
include "get.php";
$file = (!isset($file))?$_GET["url"]:$file;
if ($_GET["root"]){
$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$file;
} else {
$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
}
$json = XmlToJson::Parse($url);
echo "<pre>";
if ($_GET["get"] == "pitches"){
	$json = json_decode($json,true);
	//$json = $json["atbat"];
	print_r($json);
} else if ($_GET["get"] == "baserunners"){
	$json = json_decode($json, true);
} else if ($_GET["get"] == "score"){
	$json = json_decode($json, true);
	echo json_encode($json['score']['@attributes']);
} else {
	ksort($json);
	echo $json;
}
echo "</pre>";
?>