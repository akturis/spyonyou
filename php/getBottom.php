<?php
require_once("config.php");

$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$v=(empty($_SERVER['PHP_AUTH_USER']))?"v_":"";

$char = mysql_query('SELECT name,id,SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(logoffDateTime,logonDateTime)))) flight,
(SELECT COUNT(*) FROM `kills` k WHERE DATE_FORMAT(killTime,\'%Y%m\')=DATE_FORMAT(CURDATE(),\'%Y%m\') AND k.characterID=id AND k.characterID!=victimID) kills,
(SELECT COUNT(*) FROM `kills` k WHERE DATE_FORMAT(killTime,\'%Y%m\')=DATE_FORMAT(CURDATE(),\'%Y%m\') AND k.characterID=id AND k.characterID=victimID) looses
FROM `logons`,`'.$v.'characters` WHERE YEAR(CURDATE())=YEAR(logonDateTime) AND id=characterID GROUP BY characterID ORDER BY SUM(TIME_TO_SEC(TIMEDIFF(logoffDateTime,logonDateTime))) LIMIT 100');

while ($row = mysql_fetch_assoc($char)) {
  echo "<a href='#{$row['id']}' rel='bottomtip' title='flew {$row['flight']}, {$row['kills']} kills, {$row['looses']} looses'>{$row['name']}</a><br />";
}

mysql_close($conn);

?>

