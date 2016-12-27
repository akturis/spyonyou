<?php
require_once("config.php");
session_start();
$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

$corpid = $_GET['corpid'];
$table = mysql_query('SELECT HOUR(logonDateTime) "Hour", COUNT(*) "Logon", o.Logoff "Logoff"
 FROM `logons` as l
 JOIN (SELECT HOUR(logoffDateTime) "Hour", COUNT(*) "Logoff" FROM logons LEFT JOIN characters c1 ON c1.id = characterid WHERE c1.corporationid = '.$corpid.' AND logoffDateTime BETWEEN NOW() - INTERVAL 12 MONTH AND NOW() AND logonDateTime<logoffDateTime GROUP BY HOUR(logoffDateTime)) as o ON o.hour=HOUR(l.logonDateTime)
 LEFT JOIN characters c ON c.id = characterid
 WHERE c.corporationid = '.$corpid.' AND logonDateTime BETWEEN NOW() - INTERVAL 12 MONTH AND NOW() AND logonDateTime<logoffDateTime GROUP BY HOUR(logonDateTime)') or die($config['db']['errormsg'].mysql_error());

$types = array ('string','number','number');

while($r = mysql_fetch_assoc($table)) {
   if(!isset($google_JSON)){    
     $google_JSON = "{\"cols\": [";    
     $column = array_keys($r);
     foreach($column as $key=>$value){
         $google_JSON_cols[]="{\"id\": \"".$key."\", \"label\": \"".$value."\", \"type\": \"".$types[$key]."\"}";
     }    
     $google_JSON .= implode(",",$google_JSON_cols)."],\"rows\": [";       
   }
   $google_JSON_rows[] = "{\"c\":[{\"v\": \"".$r['Hour']."\"}, {\"v\": \"".$r['Logon']."\"}, {\"v\": \"".$r['Logoff']."\"}]}";
}


$data = $google_JSON.implode(",",$google_JSON_rows)."]}";

mysql_close($conn);

echo $data;
?>
