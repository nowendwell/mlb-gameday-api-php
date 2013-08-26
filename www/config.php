<?php
date_default_timezone_set('America/Los_Angeles');
if ($_GET["team"]){
	$team = $_GET["team"];
} else if ($_COOKIE["teamName"] != ""){
	$team = $_COOKIE["teamName"];
} else {
	$team = "Giants";
}
//$team = ($_GET["team"])?$_GET["team"]:'Giants';

// Evaluate date
$date = ($_GET["date"])?$_GET["date"]:date("m/d/Y");
$fulldate = date("m/d/Y",strtotime($date));
$fulldate = explode("/",$fulldate);
$year = (!empty($fulldate[2]))?$fulldate[2]:date("Y");
$month = (!empty($fulldate[0]))?$fulldate[0]:date("m");
$day = (!empty($fulldate[1]))?$fulldate[1]:date("d");

$date = "year_".$year."/month_".$month."/day_".$day;

$url = "http://gd2.mlb.com/components/game/mlb/".$date."/scoreboard.xml";
?>