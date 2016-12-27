<?php
require_once("config.php");

$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$corpid = $_GET['corpid'];
$count = mysql_query('SELECT DATE_FORMAT(logon,\'%e %b %Y\') dat, COUNT(*) count FROM (SELECT DISTINCT date(logonDateTime) logon, characterId FROM `logons` WHERE logonDateTime>(CURDATE() - INTERVAL 12 MONTH)) dist LEFT JOIN characters c ON c.id = characterid WHERE c.corporationid = '.$corpid.' GROUP BY logon ORDER BY COUNT(*) DESC LIMIT 1');
if ($count =  mysql_fetch_assoc($count))
echo "          <h2>Top online</h2>
          <h1>{$count['count']}</h1>
          <p>players were seen on {$count['dat']}</p>";

mysql_close($conn);

?>

