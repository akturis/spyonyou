<?php
require_once(realpath(dirname(__FILE__))."/"."config.php");

function writeLog ($text) {
  global $config;
  global $setting;

  $msg = date("Y-m-d H:i:s").": $text".PHP_EOL;
  if (!is_null($setting['log_kill'])) {
    file_put_contents(realpath(dirname(__FILE__)).'/'.$setting['log_kill'],$msg,FILE_APPEND);
  } else {
    return $msg;
  }
}
//writelog("start");
$conn = @mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die(mysql_error());
@mysql_select_db($config['db']['dbname']) or die(writeLog(mysql_error()));

$corp_exist =array();
foreach($setting['corpID'] as $corpid){
    $corp_exist[] = $corpid['corporationID'];
}
//$corpid_m = $setting['corpID']['FSP-T']['corporationID'];
//$corpid_a = $setting['corpID']['ACADEMY']['corporationID'];
//$corpid_g = $setting['corpID']['Geten']['corporationID'];

$query = mysql_query("SELECT * FROM characters WHERE 1;") or die(mysql_error());
$rows = array();
while($row = mysql_fetch_array($query))
    $rows[] = $row;
foreach ($rows as $row) {
  $name = str_replace("'","''",$row['name']);
  $urlid = urlencode ($row['id']);
// writelog($name);
//  @$xmlKills=simplexml_load_file("http://eve-kill.net/?a=idfeed&pilotname=$urlname&lastID=$lastId");
  $ch1 = curl_init();
  $now_date = date('Ymd').'0000';
//  writelog($now_date);
  $lastId = mysql_query("SELECT MAX(killID) id FROM `kills` WHERE characterID={$urlid}") or die(writeLog(mysql_error()));
  if ($lastId = mysql_fetch_assoc($lastId)) $lastId=($lastId==="")?"":$lastId['id'];
//  writelog($lastId);
  curl_setopt ($ch1, CURLOPT_URL,"https://zkillboard.com/api/characterID/$urlid/afterkillID/$lastId/no-items/xml/" );
//  curl_setopt ($ch1, CURLOPT_URL,"https://zkillboard.com/api/corporationID/604035876/xml/no-items/" );
  curl_setopt ($ch1, CURLOPT_RETURNTRANSFER,1 );
  curl_setopt ($ch1, CURLOPT_HEADER,0 );

  $data = curl_exec($ch1);
  curl_close($ch1);
  $xmlKills=simplexml_load_string($data);

  if (is_object($xmlKills)) {
    foreach ($xmlKills->result->rowset->row as $kill) {
      $urlkill = urlencode ($kill['killid']);
      $_chid = $kill->victim['characterID'];
      if (in_array($kill->victim['corporationID'], $corp_exist)) {
          @mysql_query("INSERT INTO kills (`killID`,`characterID`,`attackerID`,`killTime`,`killInternalID`,`victimID`)
                   VALUES ({$kill['killID']},{$_chid},{$_chid},'{$kill['killTime']}',{$kill['killID']},{$_chid})
                    ON DUPLICATE KEY UPDATE killID={$kill['killID']},characterID={$_chid}") or die(writeLog(mysql_error()));
      }  
      
      foreach ($kill->rowset->row as $attack) {
        if (in_array($attack['corporationID'], $corp_exist)) {  
          @mysql_query("INSERT INTO kills (`killID`,`characterID`,`attackerID`,`killTime`,`killInternalID`,`victimID`)
                   VALUES ({$kill['killID']},{$attack['characterID']},{$attack['characterID']},'{$kill['killTime']}',{$kill['killID']},{$_chid})
                    ON DUPLICATE KEY UPDATE killID={$kill['killID']},characterID={$attack['characterID']}") or die(writeLog(mysql_error()));
        }          
      }            
    }
  }
}

mysql_close($conn);

?>
