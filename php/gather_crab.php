<?php
require_once(realpath(dirname(__FILE__))."/"."config.php");

function writeLog ($text) {
  global $config;
  global $setting;

  $msg = date("Y-m-d H:i:s").": $text".PHP_EOL;
  if (!is_null($setting['log_crab'])) {
    file_put_contents(realpath(dirname(__FILE__)).'/'.$setting['log_crab'],$msg,FILE_APPEND);
  } else {
    return 'Ошибка '.$msg;
  }
}

//writelog("Start");
$reftypes = array("33","85");

$conn = @mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die(writeLog(mysql_error()));
@mysql_select_db($config['db']['dbname']) or die(writeLog(mysql_error()));
//Main corp
$xmlUrl="https://api.eveonline.com/corp/WalletJournal.xml.aspx?keyID={$setting['keyID']}&vCode={$setting['vCode']}";
//$xmlObj=simplexml_load_file($xmlUrl);
//var_dump(libxml_use_internal_errors(true));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$xmlUrl );
curl_setopt ($ch , CURLOPT_RETURNTRANSFER,1 );
curl_setopt ($ch, CURLOPT_HEADER,0 );

$data = curl_exec($ch);
curl_close($ch);

$xmlObj=simplexml_load_string($data);
foreach ($xmlObj->result->rowset->row as $row) {
//  if ($row['refTypeID'] <> '33') continue;
  if (!in_array($row['refTypeID'],$reftypes)) continue;
  mysql_query("INSERT INTO `entries` (`date`,`characterID`,`refTypeID`,`amount`)
               VALUES ('{$row['date']}','{$row['ownerID2']}','{$row['refTypeID']}','{$row['amount']}}')
               ON DUPLICATE KEY UPDATE amount='{$row['amount']}'") or die(mysql_error());
}

//ACADEMY
$xmlUrl="https://api.eveonline.com/corp/WalletJournal.xml.aspx?keyID={$setting['corpID']['ACADEMY']['keyID']}&vCode={$setting['corpID']['ACADEMY']['vCode']}";
//$xmlObj=simplexml_load_file($xmlUrl);
//var_dump(libxml_use_internal_errors(true));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$xmlUrl );
curl_setopt ($ch , CURLOPT_RETURNTRANSFER,1 );
curl_setopt ($ch, CURLOPT_HEADER,0 );

$data = curl_exec($ch);
curl_close($ch);

$xmlObj=simplexml_load_string($data);
foreach ($xmlObj->result->rowset->row as $row) {
  if (!in_array($row['refTypeID'],$reftypes)) continue;
  mysql_query("INSERT INTO `entries` (`date`,`characterID`,`refTypeID`,`amount`)
               VALUES ('{$row['date']}','{$row['ownerID2']}','{$row['refTypeID']}','{$row['amount']}}')
               ON DUPLICATE KEY UPDATE amount='{$row['amount']}'") or die(mysql_error());
}

//GETEN
$xmlUrl="https://api.eveonline.com/corp/WalletJournal.xml.aspx?keyID={$setting['corpID']['Geten']['keyID']}&vCode={$setting['corpID']['Geten']['vCode']}";
//$xmlObj=simplexml_load_file($xmlUrl);
//var_dump(libxml_use_internal_errors(true));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$xmlUrl );
curl_setopt ($ch , CURLOPT_RETURNTRANSFER,1 );
curl_setopt ($ch, CURLOPT_HEADER,0 );

$data = curl_exec($ch);
curl_close($ch);

$xmlObj=simplexml_load_string($data);
foreach ($xmlObj->result->rowset->row as $row) {
//  if ($row['refTypeID'] <> '33') continue;
  if (!in_array($row['refTypeID'],$reftypes)) continue;
  mysql_query("INSERT INTO `entries` (`date`,`characterID`,`refTypeID`,`amount`)
               VALUES ('{$row['date']}','{$row['ownerID2']}','{$row['refTypeID']}','{$row['amount']}}')
               ON DUPLICATE KEY UPDATE amount='{$row['amount']}'") or die(mysql_error());
}

mysql_close($conn);

?>
