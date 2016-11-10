<?php
require_once("config.php");

$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$table = mysql_query('SELECT MAX(DATE_FORMAT(STR_TO_DATE(logon,\'%Y%m\'),\'%b %Y\')) text, COUNT(*) "Online",
(SELECT COUNT(*) FROM `kills` k WHERE DATE_FORMAT(killTime,\'%Y%m\')=logon AND k.characterID!=victimID) "Kills",
(SELECT COUNT(*) FROM `kills` k WHERE DATE_FORMAT(killTime,\'%Y%m\')=logon AND k.characterID=victimID) "Looses"
 FROM (SELECT DISTINCT DATE_FORMAT(logonDateTime,\'%Y%m\') logon, characterId FROM `logons` WHERE logonDateTime>(CURDATE() - INTERVAL 12 MONTH)) dist GROUP BY logon');

$types = array ('string','number','number','number');

while($r = mysql_fetch_assoc($table)) {
   if(!isset($google_JSON)){    
     $google_JSON = "{\"cols\": [";    
     $column = array_keys($r);
     foreach($column as $key=>$value){
         $google_JSON_cols[]="{\"id\": \"".$key."\", \"label\": \"".$value."\", \"type\": \"".$types[$key]."\"}";
     }    
     $google_JSON .= implode(",",$google_JSON_cols)."],\"rows\": [";       
   }
   $google_JSON_rows[] = "{\"c\":[{\"v\": \"".$r['text']."\"}, {\"v\": \"".$r['Online']."\"}, {\"v\": \"".$r['Kills']."\"}, {\"v\": \"".$r['Looses']."\"}]}";
}


$data = $google_JSON.implode(",",$google_JSON_rows)."]}";

mysql_close($conn);

echo $data;
?>

