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
echo "start";
$mysqli = mysqli_connect($config['db']['host'],$config['db']['username'],$config['db']['password'],$config['db']['dbname']) or die(writeLog(mysqli_connect_error()));

$corp_exist =array();
foreach($setting['corpID'] as $corpid){
    $corp_exist[] = $corpid['corporationID'];
}
//$corpid_m = $setting['corpID']['FSP-T']['corporationID'];
//$corpid_a = $setting['corpID']['ACADEMY']['corporationID'];
//$corpid_g = $setting['corpID']['Geten']['corporationID'];
foreach($setting['corpID'] as $corpID_arr){
//    $xmlUrl="https://api.eveonline.com/corp/KillMails.xml.aspx?keyID={$corpID_arr['keyID']}&vCode={$corpID_arr['vCode']}&rowCount=300";
    $xmlUrl="https://zkillboard.com/api/corporationID/{$corpID_arr['corporationID']}/limit/200/no-items/";
    //$xmlObj=simplexml_load_file($xmlUrl);
    //var_dump(libxml_use_internal_errors(true));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$xmlUrl );
    curl_setopt($ch , CURLOPT_RETURNTRANSFER,1 );
    curl_setopt($ch,CURLOPT_ENCODING , 'gzip');
    curl_setopt($ch, CURLOPT_HEADER,0 );
    curl_setopt( $ch, CURLOPT_USERAGENT, "User-Agent: http://spy.ddcreation.ru/ Maintainer: Akturis daw99@mail.ru" );
    
    $data = curl_exec($ch);
    curl_close($ch);
    
//    $xmlObj=simplexml_load_string($data);
    $xmlObj=json_decode($data,true);
//  if (is_object($xmlObj)) {
    foreach ($xmlObj as $key => $kill) {
      $urlkill = urlencode ($kill['killmail_id']);
      $_chid = $kill['victim']['character_id'];
      if($_chid == '0') continue;
      if (in_array($kill['victim']['corporation_id'], $corp_exist)) {
          mysqli_query($mysqli,"INSERT INTO kills (`killID`,`characterID`,`attackerID`,`killTime`,`killInternalID`,`victimID`)
                   VALUES ({$kill['killmail_id']},{$_chid},{$_chid},'{$kill['killmail_time']}',{$kill['killmail_id']},{$_chid})
                    ON DUPLICATE KEY UPDATE killID={$kill['killmail_id']},characterID={$_chid}") or die(writeLog(mysqli_connect_error()));
      }  
      foreach ($kill['attackers'] as $key2 => $attack) {
        if (in_array($attack['corporation_id'], $corp_exist)) {  
          mysqli_query($mysqli,"INSERT INTO kills (`killID`,`characterID`,`attackerID`,`killTime`,`killInternalID`,`victimID`)
                   VALUES ({$kill['killmail_id']},{$attack['character_id']},{$attack['character_id']},'{$kill['killmail_time']}',{$kill['killmail_id']},{$_chid})
                    ON DUPLICATE KEY UPDATE killID={$kill['killmail_id']},characterID={$attack['character_id']}") or die(writeLog(mysqli_connect_error()));
        }          
      } //if
    }
//  }
}

mysqli_close($mysqli);

?>
