<?php
session_start();

if ($_POST["favorite"]){
	setcookie ("teamName", "", time() - 3600);
	setcookie("teamName", $_POST["favorite"]);
	exit();
}
include 'full.php';
@ksort($json['data']['game']);

$file = "gameday_Syn.xml";
$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
if (url_exists($url)){$lineups = json_decode(XmlToJson::Parse($url),true);}

$file = "game_events.json";
$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
if (url_exists($url)){$plays = file_get_contents($url);}
?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title><?=$team?> Scoreboard - <?php echo ($_GET["date"])?date("l, F jS, Y",strtotime($_GET["date"])):$month."/".$day."/".$year; ?></title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
<script type="text/javascript" src="sort.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	var real_time = false;
	$('#team').change(function(){
		$('#team_form').submit();
	});
	if (!Modernizr.inputtypes.date) {
    	$('#datepicker').datepicker({changeMonth: true,changeYear: true,showButtonPanel: true});
	}
	
	$('#datepicker').change(function(){
		$('#date').submit();
	});
	$('.pop').popover();
	$('#myTab a').click(function (e) {
    	e.preventDefault();
    	$(this).tab('show');
    });
    $('.tip').tooltip();
	$('[data-spy="scroll"]').each(function () {
		var $spy = $(this).scrollspy('refresh')
    });
    $('#favorite').click(function(){
    	if ($(this).children().hasClass('favorite') == false){
    		$(this).children().removeClass('icon-white').addClass('favorite');
			$.ajax({
				type: "POST",
				url: "index.php",
				data: { favorite: "<?=$team?>" }
			}).done(function( msg ) {
				//location.reload();
			});
		}
	});
	if (real_time == true){
	$.get("getXML.php?url=plays.xml",function(data){$('#plays').html(data);});
	//$.get("getJSON.php?url=linescore.json",function(data){$('#linescore').html(data);});
	$(function() {
		window.isActive = true;
		$(window).focus(function() { this.isActive = true; });
		$(window).blur(function() { this.isActive = false; });
	});

	setInterval(function(){if(window.isActive == true){$.get("getXML.php?url=plays.xml",function(data){
	var json = $.parseJSON(data);
	$('#plays').html(data);
	jQuery.each( json.atbat.p, function( key, value ) {
		jQuery.each(value, function(key1, value1){
			console.log("key", "Pitch"+value1.id, "value", value1.des);
			//console.log( "key", key1, "value", value1 );
		});
		
		//$('#plays').append( key+ "=> "+ value + "<br />" );
	});
	//$('#plays').html(json.atbat);
	});}},10000);
	//setInterval(function(){if(window.isActive == true){$.get("getJSON.php?url=linescore.json",function(data){$('#linescore').html(data);});}},30000);
	}
});
</script>
<link href="style.css" rel="stylesheet" />
</head>
<body class="well" data-spy="scroll" data-target="#nav" data-offset="0">

<!-- Navigation Box Begin -->
<ul class="nav nav-list affix visible-desktop" id="nav">
  <li><a href="#scoreboard"><i class="icon-chevron-right"></i> Scoreboard</a></li>
  <li><a href="#main"><i class="icon-chevron-right"></i> Overview</a></li>
  <?php if (!empty($lineups) && $lineups['data']['game']['game-status']['@attributes']['status'] == "Pre-Game"){ ?><li><a href="#lineups"><i class="icon-chevron-right"></i> Lineups</a></li><?php } ?>
  <?php if ($json["data"]['game']['status'] == "In Progress"){ ?><li><a href="#plays"><i class="icon-chevron-right"></i> Plays</a></li><?php } ?>
  <?php if (!empty($plays)){ ?><li><a href="#events"><i class="icon-chevron-right"></i> Game Events</a></li><?php } ?>
</ul>
<!-- Navigation Box End -->

<div class="container" id="scoreboard">
<?php getScoreboard(); ?>
<br /><span class="clearfix"></span>

<!--  Team & Date Begin -->
<form action="index.php" method="get" id="team_form" class="pull-left form-inline">
	<div class="input-append">
	<select name="team" id="team" class="span2">
		<option<?=($team == "Angels")?' selected="selected"':""?>>Angels</option>
		<option<?=($team == "Astros")?' selected="selected"':""?>>Astros</option>
		<option<?=($team == "Athletics")?' selected="selected"':""?>>Athletics</option>
		<option<?=($team == "Blue Jays")?' selected="selected"':""?>>Blue Jays</option>
		<option<?=($team == "Braves")?' selected="selected"':""?>>Braves</option>
		<option<?=($team == "Brewers")?' selected="selected"':""?>>Brewers</option>
		<option<?=($team == "Cardinals")?' selected="selected"':""?>>Cardinals</option>
		<option<?=($team == "Cubs")?' selected="selected"':""?>>Cubs</option>
		<option<?=($team == "Diamondbacks")?' selected="selected"':""?>>Diamondbacks</option>
		<option<?=($team == "Dodgers")?' selected="selected"':""?>>Dodgers</option>
		<option<?=($team == "Giants" || empty($team))?' selected="selected"':""?>>Giants</option>
		<option<?=($team == "Indians")?' selected="selected"':""?>>Indians</option>
		<option<?=($team == "Mariners")?' selected="selected"':""?>>Mariners</option>
		<option<?=($team == "Marlins")?' selected="selected"':""?>>Marlins</option>
		<option<?=($team == "Mets")?' selected="selected"':""?>>Mets</option>
		<option<?=($team == "Nationals")?' selected="selected"':""?>>Nationals</option>
		<option<?=($team == "Orioles")?' selected="selected"':""?>>Orioles</option>
		<option<?=($team == "Padres")?' selected="selected"':""?>>Padres</option>
		<option<?=($team == "Phillies")?' selected="selected"':""?>>Phillies</option>
		<option<?=($team == "Pirates")?' selected="selected"':""?>>Pirates</option>
		<option<?=($team == "Rangers")?' selected="selected"':""?>>Rangers</option>
		<option<?=($team == "Rays")?' selected="selected"':""?>>Rays</option>
		<option<?=($team == "Red Sox")?' selected="selected"':""?>>Red Sox</option>
		<option<?=($team == "Reds")?' selected="selected"':""?>>Reds</option>
		<option<?=($team == "Rockies")?' selected="selected"':""?>>Rockies</option>
		<option<?=($team == "Royals")?' selected="selected"':""?>>Royals</option>
		<option<?=($team == "Tigers")?' selected="selected"':""?>>Tigers</option>
		<option<?=($team == "Twins")?' selected="selected"':""?>>Twins</option>
		<option<?=($team == "White Sox")?' selected="selected"':""?>>White Sox</option>
		<option<?=($team == "Yankees")?' selected="selected"':""?>>Yankees</option>
	</select> 
	<span class="add-on btn" id="favorite" value="<?=$team?>"><i class="icon-star <?=($_COOKIE["teamName"] != $team)?'icon-white':'favorite'?>"></i></span>
	</span>
	</div>
	<input type="hidden" name="date" value="<?=($_GET["date"])?$_GET["date"]:$month."/".$day."/".$year?>" />
</form>
<form action="index.php" method="get" id="date" class="pull-right">
    <div class="input-append">
		<input type="date" name="date" value="<?=($_GET["date"])?$_GET["date"]:$month."/".$day."/".$year?>" id="datepicker" style="width:75px;"/>
		<a href="index.php?team=<?=$team?>" class="btn">Today</a>
    </div>
	<input type="hidden" name="team" value="<?=$team?>" />
</form>
<!-- Team & Date End -->

<span class="clearfix"></span>
<span class="pull-right" id="real_time"><i class="icon-ban-circle"></i> Real time is disabled</span>
</div>

<div class="container well" style="text-align:center" id="main">
<?php if (!empty($json['data']['game'])){ ?>
<div class="pull-left">
	<table>
		<tr>
			<td>Away</td>
			<td class="hidden-phone" rowspan="2" id="away_score"><?=$json['data']['game']['away_team_runs']?></td>
		</tr>
		<tr>
			<td>
				<img src="http://mlb.mlb.com/images/logos/80x80/<?=$json['data']['game']['away_file_code']?>.png" alt="<?=$json['data']['game']['away_team_name'];?>" /><br />
				(<?=$json['data']['game']['away_win']?>-<?=$json['data']['game']['away_loss']?>)
			</td>
		</tr>
		<tr>
			<td></td>
		</tr>
	</table>
</div>
<div class="pull-right">
	<table>
		<tr>
			<td rowspan="2" id="home_score" class="hidden-phone"><?=$json['data']['game']['home_team_runs']?></td>
			<td>Home</td>
			
		</tr>
		<tr>
			<td>
				<img src="http://mlb.mlb.com/images/logos/80x80/<?=$json['data']['game']['home_file_code']?>.png" alt="<?=$json['data']['game']['home_team_name'];?>" /><br />
				(<?=$json['data']['game']['home_win']?>-<?=$json['data']['game']['home_loss']?>)
			</td>
		</tr>
		<tr>
			<td></td>
		</tr>
	</table>
</div>
<div>
	<span style="font-size:24px;"><?=$json['data']['game']['away_team_name'];?> vs <?=$json['data']['game']['home_team_name'];?></span><br />
	<span alt="PST" title="PST"><?=date("g:i A",strtotime($json['data']['game']['time']." ".$json['data']['game']['ampm']))?><sup>EST</sup></span>
		@ <?=$json['data']['game']['venue']?><br />
	<table align="center" cellpadding="5" class="hidden-phone">
		<tr>
			<td>
				<table align="center" id="boxscore" cellpadding="5">
					<thead>
						<tr>
							<th></th>
							<th>1</th>
							<th>2</th>
							<th>3</th>
							<th>4</th>
							<th>5</th>
							<th>6</th>
							<th>7</th>
							<th>8</th>
							<th>9</th>
							<?=(isset($json['data']['game']['linescore'][9]['away_inning_runs']))?"<th>10</th>":""?>
							<?=(isset($json['data']['game']['linescore'][10]['away_inning_runs']))?"<th>11</th>":""?>
							<?=(isset($json['data']['game']['linescore'][11]['away_inning_runs']))?"<th>12</th>":""?>
							<?=(isset($json['data']['game']['linescore'][12]['away_inning_runs']))?"<th>13</th>":""?>
							<?=(isset($json['data']['game']['linescore'][13]['away_inning_runs']))?"<th>14</th>":""?>
							<?=(isset($json['data']['game']['linescore'][14]['away_inning_runs']))?"<th>15</th>":""?>
							<?=(isset($json['data']['game']['linescore'][15]['away_inning_runs']))?"<th>16</th>":""?>
							<?=(isset($json['data']['game']['linescore'][16]['away_inning_runs']))?"<th>17</th>":""?>
							<?=(isset($json['data']['game']['linescore'][17]['away_inning_runs']))?"<th>18</th>":""?>
							<?=(isset($json['data']['game']['linescore'][18]['away_inning_runs']))?"<th>19</th>":""?>
							<?=(isset($json['data']['game']['linescore'][19]['away_inning_runs']))?"<th>20</th>":""?>
							<?=(isset($json['data']['game']['linescore'][20]['away_inning_runs']))?"<th>21</th>":""?>
							<?=(isset($json['data']['game']['linescore'][21]['away_inning_runs']))?"<th>22</th>":""?>
							<?=(isset($json['data']['game']['linescore'][22]['away_inning_runs']))?"<th>23</th>":""?>
							<th>R</th>
							<th>H</th>
							<th>E</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><?=$json['data']['game']['away_name_abbrev'];?></th>
							<td id="away_inning_1"><?=$json['data']['game']['linescore'][0]['away_inning_runs']?></td>
							<td id="away_inning_2"><?=$json['data']['game']['linescore'][1]['away_inning_runs']?></td>
							<td id="away_inning_3"><?=$json['data']['game']['linescore'][2]['away_inning_runs']?></td>
							<td id="away_inning_4"><?=$json['data']['game']['linescore'][3]['away_inning_runs']?></td>
							<td id="away_inning_5"><?=$json['data']['game']['linescore'][4]['away_inning_runs']?></td>
							<td id="away_inning_6"><?=$json['data']['game']['linescore'][5]['away_inning_runs']?></td>
							<td id="away_inning_7"><?=$json['data']['game']['linescore'][6]['away_inning_runs']?></td>
							<td id="away_inning_8"><?=$json['data']['game']['linescore'][7]['away_inning_runs']?></td>
							<td id="away_inning_9"><?=$json['data']['game']['linescore'][8]['away_inning_runs']?></td>
							<?=(isset($json['data']['game']['linescore'][9]['away_inning_runs']))?'<td id="away_inning_10">'.$json['data']['game']['linescore'][9]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][10]['away_inning_runs']))?'<td id="away_inning_11">'.$json['data']['game']['linescore'][10]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][11]['away_inning_runs']))?'<td id="away_inning_12">'.$json['data']['game']['linescore'][11]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][12]['away_inning_runs']))?'<td id="away_inning_13">'.$json['data']['game']['linescore'][12]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][13]['away_inning_runs']))?'<td id="away_inning_14">'.$json['data']['game']['linescore'][13]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][14]['away_inning_runs']))?'<td id="away_inning_15">'.$json['data']['game']['linescore'][14]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][15]['away_inning_runs']))?'<td id="away_inning_16">'.$json['data']['game']['linescore'][15]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][16]['away_inning_runs']))?'<td id="away_inning_17">'.$json['data']['game']['linescore'][16]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][17]['away_inning_runs']))?'<td id="away_inning_18">'.$json['data']['game']['linescore'][17]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][18]['away_inning_runs']))?'<td id="away_inning_19">'.$json['data']['game']['linescore'][18]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][19]['away_inning_runs']))?'<td id="away_inning_20">'.$json['data']['game']['linescore'][19]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][20]['away_inning_runs']))?'<td id="away_inning_21">'.$json['data']['game']['linescore'][20]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][21]['away_inning_runs']))?'<td id="away_inning_22">'.$json['data']['game']['linescore'][21]['away_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][22]['away_inning_runs']))?'<td id="away_inning_23">'.$json['data']['game']['linescore'][22]['away_inning_runs'].'</td>':""?>
						
							<td id="away_runs"><?=$json['data']['game']['away_team_runs']?></td>
							<td id="away_hits"><?=$json['data']['game']['away_team_hits']?></td>
							<td id="away_errors"><?=$json['data']['game']['away_team_errors']?></td>
						</tr>
						<tr>
							<th><?=$json['data']['game']['home_name_abbrev'];?></th>
							<td id="home_inning_1"><?=$json['data']['game']['linescore'][0]['home_inning_runs']?></td>
							<td id="home_inning_2"><?=$json['data']['game']['linescore'][1]['home_inning_runs']?></td>
							<td id="home_inning_3"><?=$json['data']['game']['linescore'][2]['home_inning_runs']?></td>
							<td id="home_inning_4"><?=$json['data']['game']['linescore'][3]['home_inning_runs']?></td>
							<td id="home_inning_5"><?=$json['data']['game']['linescore'][4]['home_inning_runs']?></td>
							<td id="home_inning_6"><?=$json['data']['game']['linescore'][5]['home_inning_runs']?></td>
							<td id="home_inning_7"><?=$json['data']['game']['linescore'][6]['home_inning_runs']?></td>
							<td id="home_inning_8"><?=$json['data']['game']['linescore'][7]['home_inning_runs']?></td>
							<td id="home_inning_9"><?=$json['data']['game']['linescore'][8]['home_inning_runs']?></td>
							<?=(isset($json['data']['game']['linescore'][9]['home_inning_runs']))?'<td id="home_inning_10">'.$json['data']['game']['linescore'][9]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][10]['home_inning_runs']))?'<td id="home_inning_11">'.$json['data']['game']['linescore'][10]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][11]['home_inning_runs']))?'<td id="home_inning_12">'.$json['data']['game']['linescore'][11]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][12]['home_inning_runs']))?'<td id="home_inning_13">'.$json['data']['game']['linescore'][12]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][13]['home_inning_runs']))?'<td id="home_inning_14">'.$json['data']['game']['linescore'][13]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][14]['home_inning_runs']))?'<td id="home_inning_15">'.$json['data']['game']['linescore'][14]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][15]['home_inning_runs']))?'<td id="home_inning_16">'.$json['data']['game']['linescore'][15]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][16]['home_inning_runs']))?'<td id="home_inning_17">'.$json['data']['game']['linescore'][16]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][17]['home_inning_runs']))?'<td id="home_inning_18">'.$json['data']['game']['linescore'][17]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][18]['home_inning_runs']))?'<td id="home_inning_19">'.$json['data']['game']['linescore'][18]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][19]['home_inning_runs']))?'<td id="home_inning_20">'.$json['data']['game']['linescore'][19]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][20]['home_inning_runs']))?'<td id="home_inning_21">'.$json['data']['game']['linescore'][20]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][21]['home_inning_runs']))?'<td id="home_inning_22">'.$json['data']['game']['linescore'][21]['home_inning_runs'].'</td>':""?>
							<?=(isset($json['data']['game']['linescore'][22]['home_inning_runs']))?'<td id="home_inning_23">'.$json['data']['game']['linescore'][22]['home_inning_runs'].'</td>':""?>
							<td id="home_runs"><?=$json['data']['game']['home_team_runs']?></td>
							<td id="home_hits"><?=$json['data']['game']['home_team_hits']?></td>
							<td id="home_errors"><?=$json['data']['game']['home_team_errors']?></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td>
				&nbsp;
			</td>
			<?php if ($json["data"]['game']['status'] == "In Progress"){ ?>
			<td>
				<table cellpadding="2" id="count">
					<tr>
						<th>B</th>
						<?php
						$balls = $json['data']['game']['balls'];
						$balls_left = 4 - $balls;
						while ($balls > 0) {
							echo '<td><span class="count-green">&nbsp; &nbsp;&nbsp;</span></td>';
							$balls--;
						}
						while ($balls_left > 0) {
							echo '<td><span class="count-grey">&nbsp; &nbsp;&nbsp;</span></td>';
							$balls_left--;
						}
						?>
					</tr>
					<tr>
						<th>S</th>
						<?php
						$strikes = $json['data']['game']['strikes'];
						$strikes_left = 3 - $strikes;
						while ($strikes > 0) {
							echo '<td><span class="count-red">&nbsp; &nbsp;&nbsp;</span></td>';
							$strikes--;
						}
						while ($strikes_left > 0) {
							echo '<td><span class="count-grey">&nbsp; &nbsp;&nbsp;</span></td>';
							$strikes_left--;
						}
						?>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th>O</th>
						<?php
						$outs = $json['data']['game']['outs'];
						$outs_left = 3 - $outs;
						while ($outs > 0) {
							echo '<td><span class="count-red">&nbsp; &nbsp;&nbsp;</span></td>';
							$outs--;
						}
						while ($outs_left > 0) {
							echo '<td><span class="count-grey">&nbsp; &nbsp;&nbsp;</span></td>';
							$outs_left--;
						}
						?>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
			<td>
				<div class="bases">
					<div id="first" class="base <?=($json['data']['game']['runner_on_1b'])?"on_base":""?>" title="<?=$json['data']['game']['runner_on_1b']?>">&nbsp; &nbsp;&nbsp;</div>
					<div id="second" class="base <?=($json['data']['game']['runner_on_2b'])?"on_base":""?>" title="<?=$json['data']['game']['runner_on_2b']?>">&nbsp; &nbsp;&nbsp;</div>
					<div id="third" class="base <?=($json['data']['game']['runner_on_3b'])?"on_base":""?>" title="<?=$json['data']['game']['runner_on_3b']?>">&nbsp; &nbsp;&nbsp;</div>
				</div>
			</td>
			<?php } ?>
		</tr>
	</table>
</div>
<?php } else { ?>
<h1>Off Day</h1>
<?php } ?>
	<span class="clearfix"></span>
	<?php 
	if ($json["data"]['game']['status'] == "Preview" || $json["data"]['game']['status'] == "Pre-Game" || $json["data"]['game']['status'] == "Warmup"){
	$file = "gamecenter.xml";
	$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
	if (url_exists($url)){
	$extra = json_decode(XmlToJson::Parse($url),true);
	
	
	?>
	<legend><?=$json["data"]['game']['status']?><?php if (!empty($json["data"]['game']['reason'])){echo " - ".$json['data']['game']['reason'];}?></legend>
	<table style="width:100%" id="pitchers">
		<tr>
			<td width="50%;"><a href="http://wapc.mlb.com/play/?content_id=<?=$extra['probables']['away']['relatedvideo']['media']['@attributes']['content_id']?>" target="_blank"><img style="width:98%;" class="pull-left thumbnail" src="http://mlb.mlb.com/images/players/525x330/alt/<?=$json['data']['game']['away_probable_pitcher']['id']?>.jpg" alt="<?=$json['data']['game']['away_probable_pitcher']['first_name']." ".$json['data']['game']['away_probable_pitcher']['last']?>"/></a></td>
			<td width="50%;"><a href="http://wapc.mlb.com/play/?content_id=<?=$extra['probables']['home']['relatedvideo']['media']['@attributes']['content_id']?>" target="_blank"><img style="width:98%;" class="pull-right thumbnail" src="http://mlb.mlb.com/images/players/525x330/alt/<?=$json['data']['game']['home_probable_pitcher']['id']?>.jpg" alt="<?=$json['data']['game']['home_probable_pitcher']['first_name']." ".$json['data']['game']['home_probable_pitcher']['last']?>"/></a></td>
		</tr>
		<tr>
			<td><h3><?=$json['data']['game']['away_probable_pitcher']['first_name']." ".$json['data']['game']['away_probable_pitcher']['last']?>, <span class="muted"><?=$extra['probables']['away']['throwinghand']?></span></h3></td>
			<td><h3><?=$json['data']['game']['home_probable_pitcher']['first_name']." ".$json['data']['game']['home_probable_pitcher']['last']?>, <span class="muted"><?=$extra['probables']['home']['throwinghand']?></span></h3></td>
		</tr>
		<tr>
			<td><?=$json['data']['game']['away_probable_pitcher']['wins']." - ".$json['data']['game']['away_probable_pitcher']['losses'].", ".$json['data']['game']['away_probable_pitcher']['era']." ERA";?></td>
			<td><?=$json['data']['game']['home_probable_pitcher']['wins']." - ".$json['data']['game']['home_probable_pitcher']['losses'].", ".$json['data']['game']['home_probable_pitcher']['era']." ERA";?></td>
		</tr>
		<tr>
			<td><p><?=$extra['probables']['away']['report']?></p></td>
			<td><p><?=$extra['probables']['home']['report']?></p></td>
		</tr>
		<tr>
			<td><a href="http://mlb.mlb.com/team/player.jsp?player_id=<?=$json['data']['game']['away_probable_pitcher']['id']?>" target="_blank">Extended Stats</a></td>
			<td><a href="http://mlb.mlb.com/team/player.jsp?player_id=<?=$json['data']['game']['home_probable_pitcher']['id']?>" target="_blank">Extended Stats</a></td>
		</tr>
	</table>
	<hr />
	<?php 
	if (!empty($lineups) && $lineups['data']['game']['game-status']['@attributes']['status'] == "Pre-Game"){ ?>
	<legend id="lineups">Lineups</legend>
	<table class="table table-bordered table-striped sortable" style="background-color:white" id="lineup_away">
		<thead>
			<tr>
				<th><?=$json['data']['game']['away_team_name']?></th>
				<th>AVG</th>
				<th>HR</th>
				<th class="hidden-phone">RBI</th>
				
				
			</tr>
		</thead>
		<tbody>
			<?php
			$away_batter = $lineups['game']['lineup'][0]['batter'];
			foreach($away_batter as $player){
				$batter_info = getBatterById($player['@attributes']['pid']);
				echo '
				<tr>
					<td>'.$player['@attributes']['batting_order'].'. '.$batter_info['@attributes']['last_name'].', '.$batter_info['@attributes']['current_position'].'</td>
					<td>'.$batter_info['season']['@attributes']['avg'].'</td>
					<td>'.$batter_info['season']['@attributes']['hr'].'</td>
					<td class="hidden-phone">'.$batter_info['season']['@attributes']['rbi'].'</td>
					
				</tr>
				';
			}
			?>
		</tbody>
	</table>
	<table class="table table-bordered table-striped sortable" style="background-color:white" id="lineup_home">
		<thead>
			<tr>
				<th><?=$json['data']['game']['home_team_name']?></th>
				<th>AVG</th>
				<th>HR</th>
				<th class="hidden-phone">RBI</th>
				
				
			</tr>
		</thead>
		<tbody>
			<?php
			$home_batter = $lineups['game']['lineup'][1]['batter'];
			foreach($home_batter as $player){
			$batter_info = getBatterById($player['@attributes']['pid']);
			echo '
			<tr>
				<td>'.$player['@attributes']['batting_order'].'. '.$batter_info['@attributes']['last_name'].', '.$batter_info['@attributes']['current_position'].'</td>
				<td>'.$batter_info['season']['@attributes']['avg'].'</td>
				<td>'.$batter_info['season']['@attributes']['hr'].'</td>
				<td class="hidden-phone">'.$batter_info['season']['@attributes']['rbi'].'</td>
				
			</tr>
			';
			}
			?>
		</tbody>
	</table>
	<?php }?>
	<!--pre style="text-align:left"><?php print_r($lineups);?></pre-->
	<?php }}	else if ($json['data']['game']['status'] == "Final" || $json['data']['game']['status'] == "Game Over") { 
	$file = "gamecenter.xml";
	$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
	if (url_exists($url)){
	$extra = json_decode(XmlToJson::Parse($url),true);
	?>
		<legend>Wrap-Up</legend>
		<table align="center">
			<tr>
				<td style="width:100%;"><img class="thumbnail" style="margin:0 auto" src="http://mlb.mlb.com<?=$extra['photos']['ipad']['url']?>" alt="<?=$extra['photos']['ipad']['caption']?>"/></td></tr><tr>
				<td><h2 class="span4"><?=$extra['wrap']['mlb']['headline']?></h2><p align="left" style="vertical-align:middle"><?=$extra['wrap']['mlb']['blurb']?></p></td>
			</tr>
		</table>
		<?php
		$file = "boxscore.json";
		$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
		if (url_exists($url)){
		$boxscore = json_decode(file_get_contents($url),true);
		$boxscore = $boxscore['data']['boxscore'];
		$home_batters = $boxscore['batting'][0];
		$home_pitchers = $boxscore['pitching'][1];
		$away_batters = $boxscore['batting'][1];
		$away_pitchers = $boxscore['pitching'][0];
		foreach($home_pitchers['pitcher'] as $pitcher){$strike_count_home = $strike_count_home + $pitcher['@attributes']['s'];}
		foreach($away_pitchers['pitcher'] as $pitcher){$strike_count_away = $strike_count_away + $pitcher['@attributes']['s'];}
		//echo "<pre style='text-align:left'>";print_r($boxscore);echo "</pre>";
 		?>
 		<ul class="nav nav-tabs" id="myTab">
			<li class="<?php if ($json['data']['game']['away_team_name'] == $team){echo 'active';}?>"><a href="#<?=$json['data']['game']['away_team_name'];?>"><?=$json['data']['game']['away_team_name'];?></a></li>
			<li class="<?php if ($json['data']['game']['home_team_name'] == $team){echo 'active';}?>"><a href="#<?=$json['data']['game']['home_team_name'];?>"><?=$json['data']['game']['home_team_name'];?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane <?=($json['data']['game']['away_team_name'] == $team)?"active":""?>" id="<?=$json['data']['game']['away_team_name'];?>">
				<legend>Batting</legend>
				<table class="table table-bordered table-striped sortable" id="away_box_batting">
					<thead>
						<tr>
							<th><?=$json['data']['game']['away_team_name'];?></th>
							<th>AB</th>
							<th>R</th>
							<th>H</th>
							<th>RBI</th>
							<th>HR</th>
							<th>BB</th>
							<th>SO</th>
							<th>LOB</th>
							<th>AVG</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Totals</th>
							<th><?=$away_batters['ab']?></th>
							<th><?=$away_batters['r']?></th>
							<th><?=$away_batters['h']?></th>
							<th><?=$away_batters['rbi']?></th>
							<th><?=$away_batters['hr']?></th>
							<th><?=$away_batters['bb']?></th>
							<th><?=$away_batters['so']?></th>
							<th><?=$away_batters['lob']?></th>
							<th><?=$away_batters['avg']?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach($away_batters['batter'] as $batter){
							//$ph = ($batter['fldg'] == "1.000")?"":' class="ph"';
							echo '
							<tr'.$ph.'>
								<td><a href="http://mlb.mlb.com/team/player.jsp?player_id='.$batter['id'].'" target="_blank">'.$batter['name'].'</a></td>
								<td>'.$batter['ab'].'</td>
								<td>'.$batter['r'].'</td>
								<td>'.$batter['h'].'</td>
								<td>'.$batter['rbi'].'</td>
								<td>'.$batter['hr'].'</td>
								<td>'.$batter['bb'].'</td>
								<td>'.$batter['so'].'</td>
								<td>'.$batter['lob'].'</td>
								<td>'.$batter['avg'].'</td>
							</tr>
							';
						}
						?>
					</tbody>
				</table>
				<!--p style="text-align:left"><?=$away_batters['text_data']?></p-->
				<table class="table table-bordered table-striped sortable" id="away_box_pitching">
					<legend>Pitching</legend>
					<thead>
						<tr>
							<th><?=$json['data']['game']['away_team_name'];?></th>
							<th>IP</th>
							<th>H</th>
							<th>R</th>
							<th>ER</th>
							<th>S</th>
							<th>BB</th>
							<th>SO</th>
							<th>HR</th>
							<th>ERA</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Totals</th>
							<th>
							<?php
							$outs = $away_pitchers['out'];
							$ip_math = round($outs / 3, 2);
							$ip_math = explode(".",$ip_math);
							if (empty($ip_math[1])){$point = ".0";}
							if ($ip_math[1] == "33"){$point = ".1";}
							if ($ip_math[1] == "67"){$point = ".2";}
							echo $ip_math[0]."".$point;
							?>
							</th>
							<th><?=$away_pitchers['h']?></th>
							<th><?=$away_pitchers['r']?></th>
							<th><?=$away_pitchers['er']?></th>
							<th><?=$strike_count_away?></th>
							<th><?=$away_pitchers['bb']?></th>
							<th><?=$away_pitchers['so']?></th>
							<th><?=$away_pitchers['hr']?></th>
							<th><?=$away_pitchers['era']?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach ($away_pitchers['pitcher'] as $pitcher){
							$outs = $pitcher['out'];
							$ip_math = round($outs / 3, 2);
							$ip_math = explode(".",$ip_math);
							if (empty($ip_math[1])){$point = ".0";}
							if ($ip_math[1] == "33"){$point = ".1";}
							if ($ip_math[1] == "67"){$point = ".2";}
							
							echo '
							<tr>
								<td>'.$pitcher['name']. " " .$pitcher['note'].'</td>
								<td>'.$ip_math[0]."".$point.'</td>
								<td>'.$pitcher['h'].'</td>
								<td>'.$pitcher['r'].'</td>
								<td>'.$pitcher['er'].'</td>
								<td>'.$pitcher['s'].'</td>
								<td>'.$pitcher['bb'].'</td>
								<td>'.$pitcher['so'].'</td>
								<td>'.$pitcher['hr'].'</td>
								<td>'.$pitcher['era'].'</td>
							</tr>';
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="tab-pane <?=($json['data']['game']['home_team_name'] == $team)?"active":""?>" id="<?=$json['data']['game']['home_team_name'];?>">
				<table class="table table-bordered table-striped sortable" id="home_box_batting">
					<legend>Batting</legend>
					<thead>
						<tr>
							<th><?=$json['data']['game']['home_team_name'];?></th>
							<th>AB</th>
							<th>R</th>
							<th>H</th>
							<th>RBI</th>
							<th>HR</th>
							<th>BB</th>
							<th>SO</th>
							<th>LOB</th>
							<th>AVG</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Totals</th>
							<th><?=$away_batters['ab']?></th>
							<th><?=$away_batters['r']?></th>
							<th><?=$away_batters['h']?></th>
							<th><?=$away_batters['rbi']?></th>
							<th><?=$away_batters['hr']?></th>
							<th><?=$away_batters['bb']?></th>
							<th><?=$away_batters['so']?></th>
							<th><?=$away_batters['lob']?></th>
							<th><?=$away_batters['avg']?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach($home_batters['batter'] as $batter){
							//$ph = ($batter['fldg'] == "1.000")?"":' class="ph"';
							echo '
							<tr'.$ph.'>
								<td><a href="http://mlb.mlb.com/team/player.jsp?player_id='.$batter['id'].'" target="_blank">'.$batter['name'].'</a></td>
								<td>'.$batter['ab'].'</td>
								<td>'.$batter['r'].'</td>
								<td>'.$batter['h'].'</td>
								<td>'.$batter['rbi'].'</td>
								<td>'.$batter['hr'].'</td>
								<td>'.$batter['bb'].'</td>
								<td>'.$batter['so'].'</td>
								<td>'.$batter['lob'].'</td>
								<td>'.$batter['avg'].'</td>
							</tr>
							';
						}
						?>
					</tbody>
				</table>
				<!--p style="text-align:left"><?=$home_batters['text_data']?></p-->
				<table class="table table-bordered table-striped sortable" id="home_box_pitching">
					<legend>Pitching</legend>
					<thead>
						<tr>
							<th><?=$json['data']['game']['home_team_name'];?></th>
							<th>IP</th>
							<th>H</th>
							<th>R</th>
							<th>ER</th>
							<th>S</th>
							<th>BB</th>
							<th>SO</th>
							<th>HR</th>
							<th>ERA</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Totals</th>
							<th>
							<?php
							$outs = $home_pitchers['out'];
							$ip_math = round($outs / 3, 2);
							$ip_math = explode(".",$ip_math);
							if (empty($ip_math[1])){$point = ".0";}
							if ($ip_math[1] == "33"){$point = ".1";}
							if ($ip_math[1] == "67"){$point = ".2";}
							echo $ip_math[0]."".$point;
							?>
							</th>
							<th><?=$home_pitchers['h']?></th>
							<th><?=$home_pitchers['r']?></th>
							<th><?=$home_pitchers['er']?></th>
							<th><?=$strike_count_home?></th>
							<th><?=$home_pitchers['bb']?></th>
							<th><?=$home_pitchers['so']?></th>
							<th><?=$home_pitchers['hr']?></th>
							<th><?=$home_pitchers['era']?></th>
						</tr>
					</tfoot>
					<tbody>
						<?php
						foreach ($home_pitchers['pitcher'] as $pitcher){
							$outs = $pitcher['out'];
							$ip_math = round($outs / 3, 2);
							$ip_math = explode(".",$ip_math);
							if (empty($ip_math[1])){$point = ".0";}
							if ($ip_math[1] == "33"){$point = ".1";}
							if ($ip_math[1] == "67"){$point = ".2";}
							
							echo '
							<tr>
								<td>'.$pitcher['name']. " " .$pitcher['note'].'</td>
								<td>'.$ip_math[0]."".$point.'</td>
								<td>'.$pitcher['h'].'</td>
								<td>'.$pitcher['r'].'</td>
								<td>'.$pitcher['er'].'</td>
								<td>'.$pitcher['s'].'</td>
								<td>'.$pitcher['bb'].'</td>
								<td>'.$pitcher['so'].'</td>
								<td>'.$pitcher['hr'].'</td>
								<td>'.$pitcher['era'].'</td>
							</tr>';
						}
						?>
					</tbody>
				</table>
				
			</div>
		</div>
		<!--p style="text-align:left"><?=$boxscore['game_info']?></p-->
		<legend id="events">Game Events</legend>
	<?php
	if (!empty($plays)){
	$events = json_decode($plays,true);
	$events = $events['data']['game'];
	//print_r($events);
	echo '<div class="accordion" id="accordion2">';
	
	foreach ($events['inning'] as $inning){
		if (isset($inning['top']['atbat'][0])){
			echo '<div class="accordion-group">';
			echo '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_top_'.$inning['num'].'"><i class="icon-chevron-up"></i> '.$inning['num'].'</a></div>';
			echo '<div id="collapse_top_'.$inning['num'].'" class="accordion-body collapse in"><div class="accordion-inner"><table class="table">';
			foreach($inning['top']['atbat'] as $atbat){
				echo '<tr><td>'.$atbat['event'].'</td><td>'.$atbat['des'].'</td></tr>';
			}
			echo '</table></div></div></div>';
		}
		if (isset($inning['bottom']['atbat'][0])){
			echo '<div class="accordion-group">';
			echo '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_bottom_'.$inning['num'].'"><i class="icon-chevron-down"></i> '.$inning['num'].'</a></div>';
			echo '<div id="collapse_bottom_'.$inning['num'].'" class="accordion-body collapse in"><div class="accordion-inner"><table class="table">';
			foreach($inning['bottom']['atbat'] as $atbat){
				echo '<tr><td>'.$atbat['event'].'</td><td>'.$atbat['des'].'</td></tr>';
			}
			echo '</table></div></div></div>';
		}	
	}
	echo '</div>';
	}
	?>
		
	<?php }}} else if ($json["data"]['game']['status'] != "In Progress") {?>
		<legend><?=$json["data"]['game']['status']?><?php if (!empty($json["data"]['game']['reason'])){echo " - ".$json['data']['game']['reason'];}?></legend>
	<?php } else if ($json["data"]['game']['status'] == "In Progress"){ ?>
		<div class="span2">
			Current Batter: <br /><img src="http://gdx.mlb.com/images/gameday/mugshots/mlb/<?=$json['data']['game']['current_batter']['id']?>.jpg" /><br /><?=$json['data']['game']['current_batter']['first_name']. " " .$json['data']['game']['current_batter']['last_name']?><br />AVG <?=$json['data']['game']['current_batter']['avg']?>
		</div>
		<div class="span2">
			On Deck: <br /><img src="http://gdx.mlb.com/images/gameday/mugshots/mlb/<?=$json['data']['game']['current_ondeck']['id']?>.jpg" /><br /><?=$json['data']['game']['current_ondeck']['first_name']. " " .$json['data']['game']['current_ondeck']['last_name']?>
		</div>
		<div class="span2">
			In Hole: <br /><img src="http://gdx.mlb.com/images/gameday/mugshots/mlb/<?=$json['data']['game']['current_inhole']['id']?>.jpg" /><br /><?=$json['data']['game']['current_inhole']['first_name']. " " .$json['data']['game']['current_inhole']['last_name']?>
		</div>
		<div class="span2 pull-right">
			Current Pitcher: <br /><img src="http://gdx.mlb.com/images/gameday/mugshots/mlb/<?=$json['data']['game']['current_pitcher']['id']?>.jpg" /><br /><?=$json['data']['game']['current_pitcher']['first_name']. " " .$json['data']['game']['current_pitcher']['last_name']?>
		</div>
		<span class="clearfix"></span>
		<hr />
		<legend id="plays">Plays</legend>
		<div class="" style="position:relative;">
			<?php 
			$now = time();
			$timeofday = (date("h:i A",strtotime($now)+144000) > date("h:i A",strtotime("7:00 PM")))?"night":"day";
			?>
			<img src="http://mlb.mlb.com/shared/flash/gameday/v5.2/assets/images/stadiums/<?=$timeofday?>/<?=$json['data']['game']['venue_id']?>.jpg" style=""/>
			<div id="batter">
				<?php
				$file = $json['data']['game']['current_batter']['id'].".xml";
				$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/batters/".$file;
				if (url_exists($url)){$batter = file_get_contents($url);}
				$batter = json_decode(XmlToJson::Parse($url),true);
				/*echo "<pre style='text-align:left;'>";
				print_r($batter);
				echo "</pre>";*/
				$batHand = ($batter['@attributes']['bats'] == "L")?"left":"right";
				if ($json['data']['game']['inning_state'] == "Top"){
					echo '<img class="'.$batHand.'" src="http://mlb.mlb.com/shared/components/gameday/gdapp/release/shared/img/batters/'.$json['data']['game']['away_team_id'].'_away_'.$batHand.'.png" style="position:absolute;"/>';
				} else if ($json['data']['game']['inning_state'] == "Bottom"){
					echo '<img class="'.$batHand.'" src="http://mlb.mlb.com/shared/components/gameday/gdapp/release/shared/img/batters/'.$json['data']['game']['home_team_id'].'_home_'.$batHand.'.png" style="position:absolute;"/>';
				}
				?>
			</div>
		</div>
		<?php
		$file = "plays.json";
		$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
		if (url_exists($url)){$plays = file_get_contents($url);}
		$plays = json_decode($plays,true);
		$plays = $plays['data']['game'];
		/*echo "<pre style='text-align:left'>";
		print_r($json);
		echo "</pre>";*/
		?>
		<div id="strikezone_container" style="position:relative">
		<div id="strikezone" style="width:200px;height:200px;position:absolute;top:-350px;left:365px;border:1px solid black;background-color:#295B8C">
			<div style="width:100px;height:115px;border:1px solid black;margin-left:50px;margin-top:40px;position:relative;background-color:#BD6863">
		
			</div>
		</div>
		</div>
		<table class="table">
		<?php
		if (!empty($plays['atbat']['p'])){
			if (!empty($plays['atbat']['p'][1])){

				foreach($plays['atbat']['p'] as $pitch){
					switch($pitch['pitch_type']){
						case 'FF':
							$ptype= 'Four-seam Fastball';
							break;
						case 'FT':
							$ptype= 'Two-seam Fastball';
							break;
						case 'FC':
							$ptype= 'Cutter';
							break;
						case 'CH':
							$ptype= 'Changeup';
							break;
						case 'CU':
							$ptype= 'Curveball';
							break;
						case 'SL':
							$ptype= 'Slider';
							break;
						case 'SI':
							$ptype= 'Sinker';
							break;
						case 'KC':
							$ptype= 'Knuckle Curve';
							break;
						case 'KN':
							$ptype= 'Knuckleball';
							break;
						case 'PO':
							$ptype= 'Pitch Out';
							break;
					}
					$count++;
					if ($pitch['type'] == "B"){
						$orb = '<span class="badge badge-success">'.$count.'</span>';
					} else if ($pitch['type'] == "S"){
						$orb = '<span class="badge badge-important">'.$count.'</span>';
					} else if ($pitch['type'] == "X"){
						$orb = '<span class="badge badge-info">'.$count.'</span>';
					}
					echo '
					<tr>
						<td>'.$count.'</td>
						<td>'.$pitch['type'].'</td>
						<td>'.$pitch['des'].'</td>
						<td>'.$pitch['start_speed'].'</td>
						<td>'.$pitch['pitch_type'].'</td>
						<td>
							<div style="width:200px;height:200px;position:relative;border:1px solid black;background-color:#295B8C">
								<div style="width:100px;height:115px;border:1px solid black;margin-left:50px;margin-top:40px;position:relative;background-color:#BD6863">
									<div style="position:absolute;top:'.($pitch['y']-40).'px;right:'.($pitch['x']).'px;">'.$orb.'</div>
								</div>
							</div>
							x: '.$pitch['x'].'<br />y: '.$pitch['y'].'
							<script>
								$("#strikezone_container #strikezone > div").append(\'<div class="tip" data-toggle="tooltip" title="'.$count.'. '.$ptype.': '.$pitch['start_speed'].' MPH ('.$pitch['des'].')" style="position:absolute;top:'.($pitch['y']-40).'px;right:'.$pitch['x'].'px;">'.$orb.'</div>\');
							</script>
						</td>
					</tr>';
					$pitches[] = array(
						"x"=>$pitch['x'],
						"y"=>$pitch['y']
					);
				}
			} else {
				if ($plays['atbat']['p']['type'] == "B"){
						$orb = '<span class="badge badge-success">1</span>';
					} else if ($plays['atbat']['p']['type'] == "S"){
						$orb = '<span class="badge badge-important">1</span>';
					} else if ($plays['atbat']['p']['type'] == "X"){
						$orb = '<span class="badge badge-info">1</span>';
					}
					switch($plays['atbat']['p']['pitch_type']){
						case 'FF':
							$ptype= 'Four-seam Fastball';
							break;
						case 'FT':
							$ptype= 'Two-seam Fastball';
							break;
						case 'FC':
							$ptype= 'Cutter';
							break;
						case 'CH':
							$ptype= 'Changeup';
							break;
						case 'CU':
							$ptype= 'Curveball';
							break;
						case 'SL':
							$ptype= 'Slider';
							break;
						case 'SI':
							$ptype= 'Sinker';
							break;
						case 'KC':
							$ptype= 'Knuckle Curve';
							break;
						case 'KN':
							$ptype= 'Knuckleball';
							break;
						case 'PO':
							$ptype= 'Pitch Out';
							break;
					}
				echo '
				<tr>
					<td>1</td>
					<td>'.$plays['atbat']['p']['type'].'</td>
					<td>'.$plays['atbat']['p']['des'].'</td>
					<td>'.$plays['atbat']['p']['start_speed'].'</td>
					<td>'.$plays['atbat']['p']['pitch_type'].'</td>
					<td>
						<script>
								$("#strikezone_container #strikezone > div").append(\'<div class="tip" data-toggle="tooltip" title="1. '.$ptype.': '.$plays['atbat']['p']['start_speed'].' MPH ('.$plays['atbat']['p']['des'].')" style="position:absolute;top:'.($pitch['y']-40).'px;right:'.$pitch['x'].'px;">'.$orb.'</div>\');
							</script>
					<div style="width:200px;height:200px;position:relative;border:1px solid black;background-color:#295B8C"><div style="width:100px;height:115px;border:1px solid black;margin-left:50px;margin-top:40px;position:relative;background-color:#BD6863"><div style="position:absolute;top:'.($plays['atbat']['p']['y']-60).'px;right:'.($plays['atbat']['p']['x']-50).'px;">'.$orb.'</div></div></div>x: '.$plays['atbat']['p']['x'].'<br />y: '.$plays['atbat']['p']['y'].'</td>
				</tr>';
				$pitches[] = array(
						"x"=>$plays['atbat']['p']['x'],
						"y"=>$plays['atbat']['p']['y']
					);
			}
		}
		?>
		</table>
		<legend id="events">Game Events</legend>
	<?php
	$file = "game_events.json";
	$url = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/".$gid."/".$file;
	if (url_exists($url)){$plays = file_get_contents($url);}
	$events = json_decode($plays,true);
	$events = $events['data']['game'];
	if ($_GET["show"]){echo '<pre style="text-align:left">';print_r($events);echo'</pre>';}
	echo '<div class="accordion" id="accordion2">';
	if (isset($events['inning'][1])){
		foreach ($events['inning'] as $inning){
		if (isset($inning['top']['atbat'][0])){
			echo '<div class="accordion-group">';
			echo '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_top_'.$inning['num'].'"><i class="icon-chevron-up"></i> '.$inning['num'].'</a></div>';
			echo '<div id="collapse_top_'.$inning['num'].'" class="accordion-body collapse in"><div class="accordion-inner"><table class="table">';
			foreach($inning['top']['atbat'] as $atbat){
				echo '<tr><td>'.$atbat['event'].'</td><td>'.$atbat['des'].'</td></tr>';
			}
			echo '</table></div></div></div>';
		}
		if (isset($inning['bottom']['atbat'][0])){
			echo '<div class="accordion-group">';
			echo '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_bottom_'.$inning['num'].'"><i class="icon-chevron-down"></i> '.$inning['num'].'</a></div>';
			echo '<div id="collapse_bottom_'.$inning['num'].'" class="accordion-body collapse in"><div class="accordion-inner"><table class="table">';
			foreach($inning['bottom']['atbat'] as $atbat){
				echo '<tr><td>'.$atbat['event'].'</td><td>'.$atbat['des'].'</td></tr>';
			}
			echo '</table></div></div></div>';
		}	
	}
	} else {
		$inning = $events['inning'];
		echo '<div class="accordion-group">';
		echo '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_top_'.$inning['num'].'"><i class="icon-chevron-up"></i> '.$inning['num'].'</a></div>';
		echo '<div id="collapse_top_'.$inning['num'].'" class="accordion-body collapse in"><div class="accordion-inner"><table class="table">';
		foreach($inning['top']['atbat'] as $atbat){
			echo '<tr><td>'.$atbat['event'].'</td><td>'.$atbat['des'].'</td></tr>';
		}
		echo '</table></div></div></div>';
	
	
		echo '<div class="accordion-group">';
		echo '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse_bottom_'.$inning['num'].'"><i class="icon-chevron-down"></i> '.$inning['num'].'</a></div>';
		echo '<div id="collapse_bottom_'.$inning['num'].'" class="accordion-body collapse in"><div class="accordion-inner"><table class="table">';
		foreach($inning['bottom']['atbat'] as $atbat){
			echo '<tr><td>'.$atbat['event'].'</td><td>'.$atbat['des'].'</td></tr>';
		}
		echo '</table></div></div></div>';
	}
	
	
	echo '</div>';
	//echo "<pre style='text-align:left'>";print_r($events);echo "</pre>";
	?>
	<?php } ?>
	
	
</div>	
	<br />
	<br />
	<footer style="text-align:center">
		<?=$json['copyright']?>
	</footer>
</body>
</html>