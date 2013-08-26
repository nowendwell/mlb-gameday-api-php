<?php
include "config.php";
class XmlToJson {
	public function Parse ($url) {
		$fileContents= file_get_contents(utf8_encode($url));
		$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
		//$fileContents = trim(str_replace('"', "'", $fileContents));
		$simpleXml = simplexml_load_string($fileContents, null, LIBXML_NOCDATA);
		$json = json_encode($simpleXml);
		return $json;
	}
}

function ArraySearchRecursive($Needle,$Haystack,$NeedleKey="",$Strict=false,$Path=array()) {
  if(!is_array($Haystack)){return false;}
  foreach($Haystack as $Key => $Val) {
    if(is_array($Val)&& $SubPath=ArraySearchRecursive($Needle,$Val,$NeedleKey,$Strict,$Path)) {
      $Path=array_merge($Path,Array($Key),$SubPath);
      return $Path;
    } elseif((!$Strict&&$Val==$Needle&&$Key==(strlen($NeedleKey)>0?$NeedleKey:$Key))||($Strict&&$Val===$Needle&&$Key==(strlen($NeedleKey)>0?$NeedleKey:$Key))){
      $Path[]=$Key;
      return $Path;
    }
  }
  return false;
}
function url_exists($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}

function getScoreboard(){
	include "config.php";
	$file = "master_scoreboard.json";
	$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$file;
	$scoreboard = json_decode(file_get_contents($url),true);
	if ($_GET["show"]){echo '<pre style="text-align:left"';print_r($scoreboard);echo"</pre>";}
	echo "<ul class='visible-desktop' id='scoreboard'>";
	foreach($scoreboard['data']['games']['game'] as $game){
		//echo "<pre>";print_r($game);echo "</pre>";
		if (!empty($game['status']['inning_state'])){
			//$details = substr($game['status']['inning_state'],0,3)." ".$game['status']['inning'];
			echo $game['runners_on_base']['status'];
			switch($game['runners_on_base']['status']){
				case "0":
				$position = "background: url('small_bases.png') repeat scroll 0 -3px transparent;";
				break;
				case "1":
				$position = "background: url('small_bases.png') repeat scroll 0 169px transparent;";
				break;
				case "2":
				$position = "background: url('small_bases.png') repeat scroll 0 -121px transparent;";
				break;
			}
			$details = '
				<table><tr><td><div style="'.$position.';display: block;text-align:center;height: 24px;margin-left: -2px;margin-top: -5px;position: relative;width: 30px;"></div></td><td>'.substr($game['status']['inning_state'],0,3)." ".$game['status']['inning'].'</td></tr></table>';
		} else if ($game['status']['status'] == "Final"){
			$details = 'Final';
		} else {
			//$details = $game['time']." ".$game['ampm'];
			$details = $game['time'];
		}
		$game_date = ($_GET["date"])?$_GET["date"]:$month."/".$day."/".$year;
		echo '<li><table style="width:100%"><tr><td><a href="'.$_SERVER['PHP_SELF'].'?team='.$game['away_team_name'].'&date='.$game_date.'&gid='.$game['gameday'].'">'.$game['away_name_abbrev'].'</a></td><td style="text-align:right">'.$game['linescore']['r']['away'].'</td></tr><tr><td><a href="'.$_SERVER['PHP_SELF'].'?team='.$game['home_team_name'].'&date='.$game_date.'&gid='.$game['gameday'].'">'.$game['home_name_abbrev'].'</a></td><td style="text-align:right">'.$game['linescore']['r']['home'].'</td></tr><tr><td colspan="2" style="text-align:center">'.$details.'</td></tr></table></li>';
	}
	echo "</ul>";
}

function sortByOrder($a, $b) {
	return $a['@attributes']['bat_order'] - $b['@attributes']['bat_order'];
}

function getBatterById($id){
	global $year, $month, $day, $gid;
	$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/batters/".$id.".xml";
	return json_decode(XmlToJson::Parse($url),true);
}
?>