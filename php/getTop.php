<?php
require_once("config.php");

$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$char = mysql_query('SELECT name,id,SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(logoffDateTime,logonDateTime)))) flight FROM `logons`,`characters` WHERE MONTH(CURDATE())=MONTH(logonDateTime) AND YEAR(CURDATE())=YEAR(logonDateTime) AND id=characterID GROUP BY characterID ORDER BY SUM(TIME_TO_SEC(TIMEDIFF(logoffDateTime,logonDateTime))) DESC LIMIT 1');
if ($char =  mysql_fetch_assoc($char))
echo "          <h2>Top pilot</h2>
          <div class='picture picture_256'><a href='#{$char['id']}' id='topchar' rel='img' style='background: url(http://image.eveonline.com/Character/{$char['id']}_256.jpg);'></a></div>
          <h4>{$char['name']}</h4><h5>with {$char['flight']} hours flown this month.</h5>";

$ship = mysql_query('SELECT shipTypeID,MAX(shipType) shipType,COUNT(shipTypeID) total FROM `logons` WHERE MONTH(CURDATE())=MONTH(logonDateTime) AND YEAR(CURDATE())=YEAR(logonDateTime) AND shipTypeID>0 GROUP BY shipTypeID ORDER BY COUNT(shipTypeID) DESC LIMIT 1');
if ($ship = mysql_fetch_assoc($ship))
echo "          <hr>
          <h2>Top ship</h2>
          <div class='picture picture_256'><a href='#' id='topship' rel='img' style='background: url(http://image.eveonline.com/Render/{$ship['shipTypeID']}_256.png);'></a></div>
          <h4>{$ship['shipType']}</h4><h5>with {$ship['total']} times left in space this month.</h5>";

mysql_close($conn);

?>

