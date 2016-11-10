<?php
require_once("config.php");

$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$char = mysql_query('SELECT id,name FROM `characters`');

while ($row = mysql_fetch_assoc($char)) {
  echo "<div id='{$row['id']}' class='{$row['name']}'></div>";
}

mysql_close($conn);

?>

