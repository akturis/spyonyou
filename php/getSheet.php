<?php
require_once("config.php");
if (!isset($_SESSION)) { session_start(); }
$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$id=(integer)$_GET['id'];
if (!empty($_SESSION['CCP']['user'])) {$_SESSION['user']="";$v="";}
else {$v=(empty($_SESSION['user']))?"v_":"";}
$shiph=(!empty($v))?"":"<th>Ship</th>";

$char = mysql_query('SELECT id,name,DATE_FORMAT(startDateTime,\'%e %b %Y\') start,roles,
(SELECT COUNT(*) FROM `kills` k WHERE killTime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() AND k.characterID=id AND k.characterID!=victimID) kills,
(SELECT COUNT(*) FROM `kills` k WHERE killTime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() AND k.characterID=id AND k.characterID=victimID) looses,
title
FROM `'.$v.'characters` WHERE id='.$id);
if ($char = mysql_fetch_assoc($char)) {
  $success=($char['kills']>0) ? " label-success" : "";
  $important=($char['looses']>0) ? " label-important" : "";
  $kills=$char['kills'];
  $looses=$char['looses'];
  $urlname=urlencode($char['name']);
  $title=(!empty($char['title']))?"<p>{$char['title']}</p>":"";
  echo "<div class='container-fluid'><div class='row-fluid'><div class='span8'><h3>{$char['name']}</h3>$title<p>In corp since {$char['start']}</p><p>Roles {$char['roles']}";

  $imgdiv="</div><div class='span4'><div class='picture picture_128'><a href='http://evewho.com/pilot/$urlname' target='_blank' rel='img' style='background: url(http://image.eveonline.com/Character/{$char['id']}_128.jpg);'></a></div><p><span class='label$success' id='kills'>$kills kills</span>&nbsp;&nbsp;<span class='label$important' id='looses'>$looses looses</span></p><p><a href='https://zkillboard.com/character/{$char['id']}' target='_blank'>Killboard</a></p></div></div></div>";
  $mis = mysql_query('SELECT COUNT(*) missions FROM `entries` WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() AND characterID='.$id);
  if ($mis = mysql_fetch_assoc($mis)) 
    echo "<p>Total missions: {$mis['missions']}</p>";
  $char = mysql_query('SELECT MAX(logonDateTime) last,SEC_TO_TIME(COALESCE(SUM(TIME_TO_SEC(CASE WHEN TIMEDIFF(logoffDateTime,logonDateTime)>0 THEN TIMEDIFF(logoffDateTime,logonDateTime) ELSE 0 END )),0)) time,COUNT(*) count FROM `logons` WHERE characterid='.$id);
  if ($char = mysql_fetch_assoc($char)) 
    echo "<p>Last login on {$char['last']}</p><p>Total flight time: {$char['time']}</p><p>Records count: {$char['count']}</p>$imgdiv";
  echo "<table class='table table-bordered'><thead><tr><th>Logon time</th><th>Session</th><th>Location</th>$shiph</tr></thead><tbody>";
  $char = mysql_query('SELECT logonDateTime,(CASE WHEN TIMEDIFF(logoffDateTime,logonDateTime)>0 THEN TIMEDIFF(logoffDateTime,logonDateTime) ELSE 0 END) time,location,shipType FROM `logons` WHERE characterId='.$id.' AND logonDateTime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ORDER BY logonDateTime DESC');
  while ($row = mysql_fetch_assoc($char)) {
    $shipd=(!empty($v))?"":"<td>{$row['shipType']}</td>";
    echo "<tr><td>{$row['logonDateTime']}</td><td>{$row['time']}</td>
          <td>{$row['location']}</td>$shipd</tr>";
  }
  echo "</tbody></table>";
}
else {
  if(empty($_GET['text'])) $text_err = " Sorry, you should be authorized to view this profile";
  else $text_err = " Login again with Eve SSO";
  echo $_SESSION['user'].$text_err;
}
mysql_close($conn);

?>

