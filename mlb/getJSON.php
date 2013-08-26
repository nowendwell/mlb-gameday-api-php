<?php
include "get.php";
$file = $_GET["url"];
$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;

echo "<pre>";
if ($_GET["get"] == "pitches"){
	$json = json_decode(file_get_contents($url),true);
	print_r($json);
} else if ($_GET["get"] == "baserunners"){
	$json = json_decode(file_get_contents($url), true);
} else {
	//echo file_get_contents($url);
}
echo "</pre>";
?>