<?php
require_once(realpath(dirname(__FILE__))."/"."config.php");

function writeLog ($text) {
  global $config;

  $msg = date("Y-m-d H:i:s").": $text".PHP_EOL;
  if (!is_null($config['log'])) {
    file_put_contents(realpath(dirname(__FILE__)).'/'.$config['log'],$msg,FILE_APPEND);
  } else {
    return 'Ошибка '.$msg;
  }
}

$conn = @mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die(writeLog(mysql_error()));
@mysql_select_db($config['db']['dbname']) or die(writeLog(mysql_error()));

$xmlUrl="https://api.eveonline.com/corp/MemberTracking.xml.aspx?keyID={$config['keyID']}&vCode={$config['vCode']}&extended=1";
$corpID= $config['corpID']['FSP-T']['corporationID'];
//$xmlObj=simplexml_load_file($xmlUrl);
var_dump(libxml_use_internal_errors(true));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$xmlUrl );
curl_setopt ($ch , CURLOPT_RETURNTRANSFER,1 );
curl_setopt ($ch, CURLOPT_HEADER,0 );

$data = curl_exec($ch);
curl_close($ch);
//writelog($data);
$xmlObj=simplexml_load_string($data);
foreach ($xmlObj->result->rowset->row as $row) {
  $name = str_replace("'","''",$row['name']);
  $location = str_replace("'","''",$row['location']);
  $shipType = str_replace("'","''",$row['shipType']);
  $title = str_replace("'","''",$row['title']);
  $urlname = urlencode ($row['name']);
  $urlid = urlencode ($row['characterid']);
  mysql_query("INSERT INTO `characters` (`id`,`name`,`startDateTime`,`roles`,`title`, `corporationID`)
               VALUES ({$row['characterID']},'$name','{$row['startDateTime']}','{$row['roles']}','$title','$corpID')
               ON DUPLICATE KEY UPDATE startDateTime='{$row['startDateTime']}',roles='{$row['roles']}',title='$title', corporationID='$corpID'") or die(writeLog('characters: '.$row['name'].' '.mysql_error()));
  mysql_query("INSERT INTO `logons` (`characterID`,`logonDateTime`,`logoffDateTime`,`location`,`shipType`,`shipTypeID`)
               VALUES ({$row['characterID']},'{$row['logonDateTime']}','{$row['logoffDateTime']}','{$location}','$shipType',{$row['shipTypeID']})
               ON DUPLICATE KEY UPDATE location='$location',shipType='$shipType',shipTypeID={$row['shipTypeID']}") or die(writeLog('logons: '.$row['name'].' '.mysql_error()));

}

$xmlUrl="https://api.eveonline.com/corp/MemberTracking.xml.aspx?keyID={$config['keyID']}&vCode={$config['vCode']}&extended=1";
$corpID= $config['corpID']['ACADEMY']['corporationID'];
//$xmlObj=simplexml_load_file($xmlUrl);
var_dump(libxml_use_internal_errors(true));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$xmlUrl );
curl_setopt ($ch , CURLOPT_RETURNTRANSFER,1 );
curl_setopt ($ch, CURLOPT_HEADER,0 );

$data = curl_exec($ch);
curl_close($ch);
//writelog($data);
$xmlObj=simplexml_load_string($data);
foreach ($xmlObj->result->rowset->row as $row) {
  $name = str_replace("'","''",$row['name']);
  $location = str_replace("'","''",$row['location']);
  $shipType = str_replace("'","''",$row['shipType']);
  $title = str_replace("'","''",$row['title']);
  $urlname = urlencode ($row['name']);
  $urlid = urlencode ($row['characterid']);
  mysql_query("INSERT INTO `characters` (`id`,`name`,`startDateTime`,`roles`,`title`, `corporationID`)
               VALUES ({$row['characterID']},'$name','{$row['startDateTime']}','{$row['roles']}','$title','$corpID')
               ON DUPLICATE KEY UPDATE startDateTime='{$row['startDateTime']}',roles='{$row['roles']}',title='$title', corporationID='$corpID'") or die(writeLog('characters: '.$row['name'].' '.mysql_error()));
  mysql_query("INSERT INTO `logons` (`characterID`,`logonDateTime`,`logoffDateTime`,`location`,`shipType`,`shipTypeID`)
               VALUES ({$row['characterID']},'{$row['logonDateTime']}','{$row['logoffDateTime']}','{$location}','$shipType',{$row['shipTypeID']})
               ON DUPLICATE KEY UPDATE location='$location',shipType='$shipType',shipTypeID={$row['shipTypeID']}") or die(writeLog('logons: '.$row['name'].' '.mysql_error()));

}

$xmlUrl="https://api.eveonline.com/corp/MemberTracking.xml.aspx?keyID={$config['keyID']}&vCode={$config['vCode']}&extended=1";
$corpID= $config['corpID']['Geten']['corporationID'];
//$xmlObj=simplexml_load_file($xmlUrl);
var_dump(libxml_use_internal_errors(true));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$xmlUrl );
curl_setopt ($ch , CURLOPT_RETURNTRANSFER,1 );
curl_setopt ($ch, CURLOPT_HEADER,0 );

$data = curl_exec($ch);
curl_close($ch);
//writelog($data);
$xmlObj=simplexml_load_string($data);
foreach ($xmlObj->result->rowset->row as $row) {
  $name = str_replace("'","''",$row['name']);
  $location = str_replace("'","''",$row['location']);
  $shipType = str_replace("'","''",$row['shipType']);
  $title = str_replace("'","''",$row['title']);
  $urlname = urlencode ($row['name']);
  $urlid = urlencode ($row['characterid']);
  mysql_query("INSERT INTO `characters` (`id`,`name`,`startDateTime`,`roles`,`title`, `corporationID`)
               VALUES ({$row['characterID']},'$name','{$row['startDateTime']}','{$row['roles']}','$title','$corpID')
               ON DUPLICATE KEY UPDATE startDateTime='{$row['startDateTime']}',roles='{$row['roles']}',title='$title', corporationID='$corpID'") or die(writeLog('characters: '.$row['name'].' '.mysql_error()));
  mysql_query("INSERT INTO `logons` (`characterID`,`logonDateTime`,`logoffDateTime`,`location`,`shipType`,`shipTypeID`)
               VALUES ({$row['characterID']},'{$row['logonDateTime']}','{$row['logoffDateTime']}','{$location}','$shipType',{$row['shipTypeID']})
               ON DUPLICATE KEY UPDATE location='$location',shipType='$shipType',shipTypeID={$row['shipTypeID']}") or die(writeLog('logons: '.$row['name'].' '.mysql_error()));

}

mysql_close($conn);

?>
