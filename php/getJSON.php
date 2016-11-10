<?php
require_once("config.php");
if (!isset($_SESSION)) { session_start(); }
$mysqli = mysqli_connect($config['db']['host'],$config['db']['username'],$config['db']['password'],$config['db']['dbname']) or die('1'.$config['db']['errormsg'].mysqli_connect_error());
//$conn = mysql_connect($config['db']['host'],$config['db']['username'],$config['db']['password']) or die($config['db']['errormsg'].mysql_error());
//mysql_select_db($config['db']['dbname']) or die($config['db']['errormsg'].mysql_error());

//$restrict=(empty($_SERVER['PHP_AUTH_USER']))?$config['rowsrestricted']:$config['rowsauth'];
//print($_SESSION['user']);
$restrict=(empty($_SESSION['user']))?$setting['rowsrestricted']:$setting['rowsauth'];
$restrict=(empty($restrict))?"":"LIMIT $restrict";
$corpid = $_GET['corpid'];
$days = (empty($_GET['days']))?$setting['days']:$_GET['days'];
//$v=(empty($_SERVER['PHP_AUTH_USER']))?"v_":"";
//$table = mysql_query('SELECT 
//killTime BETWEEN NOW() - INTERVAL 30 DAY AND NOW()
//DATE_FORMAT(killTime,\'%Y%m\')=DATE_FORMAT(CURDATE(),\'%Y%m\')
//LEFT OUTER JOIN `logons` ON MONTH(CURDATE())=MONTH(logonDateTime) AND YEAR(CURDATE())=YEAR(logonDateTime) AND id=characterID
$allkill = 'killTime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() AND ';
$alllogon = 'logonDateTime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() AND ';

$charid = $_SESSION['CCP']['user']['CharacterID'];

if(empty($charid)) {
  $where =' WHERE c.corporationID='.$corpid;
} else {
  $query_c = 'SELECT roles FROM characters WHERE id='.$charid.' AND roles=9223372036854775807';
  if ($result_c = mysqli_query($mysqli, $query_c)) {
    if ($result_c->num_rows > 0) {
        $where =' WHERE c.corporationID='.$corpid;
        $restrict = "";
    }
    else $where =' WHERE c.id='.$charid;
  }
}

$query = 'SELECT 
CONCAT(name,\'#\',id) "Pilot",
title "Main",
COALESCE(k.killsK,0) "K",
COALESCE(d.killsD,0) "D",
title "Title",
startDateTime "Member since",
f.times "Flight time",
MAX(logonDateTime) "Last login",
COALESCE(p1.days,0) "Days in PVP",
COALESCE(m.miss,0) "Missions",
COALESCE(a.anom,0) "Anomalies",
COALESCE(b.bounty,0) "Bounty",
l.location "Last location",
l.shiptype "Last ship"
FROM `characters` c
LEFT OUTER JOIN (SELECT characterID, COUNT(*) killsK FROM `kills` WHERE killTime BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() AND characterid!=victimid group by characterid ) as `k` ON id=k.characterID
LEFT OUTER JOIN (SELECT characterID, COUNT(*) killsD FROM `kills` WHERE killTime BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() AND characterid=victimid group by characterid ) as `d` ON id=d.characterID
LEFT OUTER JOIN (SELECT characterid, count(*) days FROM (SELECT characterid, COUNT(DATE_FORMAT(killTime,\'%Y%m%d\')) FROM `kills` WHERE killTime BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW()  group by characterid, DATE_FORMAT(killTime,\'%Y%m%d\')) as p2 group by characterid) as `p1` ON id=p1.characterID
LEFT OUTER JOIN (SELECT characterID, COUNT(*) miss FROM `entries` WHERE date BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() AND refTypeId=33 group by characterid) as `m` ON id=m.characterID
LEFT OUTER JOIN (SELECT characterID, COUNT(*) anom FROM `entries` WHERE date BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() AND refTypeId=85 AND amount>500000 group by characterid) as `a` ON id=a.characterID
LEFT OUTER JOIN (SELECT characterID, ROUND(SUM(amount/1000000),2) bounty FROM `entries` WHERE date BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() group by characterid) as `b` ON id=b.characterID
LEFT OUTER JOIN (SELECT characterID, SEC_TO_TIME(SUM(TIME_TO_SEC(CASE WHEN TIMEDIFF(logoffDateTime,logonDateTime)>0 THEN TIMEDIFF(logoffDateTime,logonDateTime) ELSE 0 END ))) times FROM `logons` WHERE logonDateTime BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() group by characterid ) as `f` ON  id=f.characterID
LEFT OUTER JOIN (SELECT l1.*  FROM `logons` l1 JOIN (SELECT characterID, MAX(logonDateTime) maxdate, SEC_TO_TIME(SUM(TIME_TO_SEC(CASE WHEN TIMEDIFF(logoffDateTime,logonDateTime)>0 THEN TIMEDIFF(logoffDateTime,logonDateTime) ELSE 0 END ))) times FROM `logons` GROUP BY characterid) l2 ON l2.characterid=l1.characterid AND l2.maxdate=l1.logonDateTime WHERE l1.logonDateTime BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() ) as `l` ON  id=l.characterID' 
.$where.
' GROUP BY id ORDER BY startDateTime DESC '.$restrict;

//$query = mysql_escape_string($mysqli, $query) or die('3'.$query);
$table = mysqli_query($mysqli, $query) or die('3'.$config['db']['errormsg'].mysqli_connect_error().$query);
//COALESCE(DATE_FORMAT(MAX(logonDateTime),\'%Y-%m-%d\'),\'No logons this month\') "Last login" 
//SEC_TO_TIME(COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(logoffDateTime,logonDateTime))),0)) "Flight time",
//(SELECT count(*) froM (SELECT DATE_FORMAT(a.killTime,\'%Y%m%d\'), COUNT(DATE_FORMAT(a.killTime,\'%Y%m%d\')) FROM `kills` A WHERE DATE_FORMAT(a.killTime,\'%Y%m\')=DATE_FORMAT(CURDATE(),\'%Y%m\') AND (a.characterID=id or a.characterID=victimid) group by DATE_FORMAT(a.killTime,\'%Y%m%d\')) B)
$badge="<span class='badge ";
$types = array ('string','string','number','number','string','string','string','string','number','number','number','number','string','string');

//while($r = mysql_fetch_assoc($table)) {
while($r = mysqli_fetch_assoc($table)) {
     if(!isset($google_JSON)){    
     $google_JSON = "{\"cols\": [";    
     $column = array_keys($r);
     foreach($column as $key=>$value){
         $google_JSON_cols[]="{\"id\": \"".$key."\", \"label\": \"".$value."\", \"type\": \"".$types[$key]."\"}";
     }    
     $google_JSON .= implode(",",$google_JSON_cols)."],\"rows\": [";       
   }
   $pilot=explode('#',$r['Pilot']);
   $pilotf = "<a  href='#{$pilot[1]}'>{$pilot[0]}</a>";
   $query_m = 'SELECT name main, id main_id FROM `characters` WHERE id='.$r['Title'];
   $table_m = mysqli_query($mysqli, $query_m);
   if ($m = mysqli_fetch_assoc($table_m)) {
   }
       $main_name = $m['main'];
       $main_id = $m['main_id'];
       $mainf = "<a  href='#{$main_id}'>{$main_name}</a>";
//   $query_p = 'SELECT count(*) "Days in PVP" froM (SELECT DATE_FORMAT(a.killTime,\'%Y%m%d\'), COUNT(DATE_FORMAT(a.killTime,\'%Y%m%d\')) FROM `kills` a WHERE DATE_FORMAT(a.killTime,\'%Y%m\')=DATE_FORMAT(CURDATE(),\'%Y%m\') AND (a.characterID='.$pilot[1].' or a.victimid='.$pilot[1].') group by DATE_FORMAT(a.killTime,\'%Y%m%d\')) b';
//   $query_p = 'SELECT count(*) "Days in PVP" froM (SELECT DATE_FORMAT(a.killTime,\'%Y%m%d\'), COUNT(DATE_FORMAT(a.killTime,\'%Y%m%d\')) FROM `kills` a WHERE a.killTime BETWEEN NOW() - INTERVAL '.$days.' DAY AND NOW() AND (a.characterID='.$pilot[1].' or a.victimid='.$pilot[1].') group by DATE_FORMAT(a.killTime,\'%Y%m%d\')) b';
//   $table_p = mysqli_query($mysqli, $query_p) or die('4'.$config['db']['errormsg'].mysqli_connect_error().$query_p);
   $pvp = $r['Days in PVP'];
//   if ($p = mysqli_fetch_assoc($table_p) or die('5'.$config['db']['errormsg'].mysqli_connect_error())) $pvp = $p['Days in PVP'];

   $r['Bounty'] = (empty($r['Bounty']))?0:$r['Bounty']*$setting['tax'];

   $PVPf = ($pvp>1)?"badge-success'>&nbsp;{$pvp}":"'>&nbsp;{$pvp}"; $PVPf=$badge.$PVPf."&nbsp;</span>";
   $Mf = ($r['Missions'] / 30 > 5 )?"badge-important'>&nbsp;{$r['Missions']}":"'>&nbsp;{$r['Missions']}"; $Mf=$badge.$Mf."&nbsp;</span>";
   $Af = ($r['Anomalies'] / 30 > 5 )?"badge-important'>&nbsp;{$r['Anomalies']}":"'>&nbsp;{$r['Anomalies']}"; $Af=$badge.$Af."&nbsp;</span>";
   $Bf = "'>&nbsp;{$r['Bounty']}"; $Bf=$badge.$Bf."&nbsp;</span>";
   $Kf = ($r['K']>0)?"badge-success'>&nbsp;{$r['K']}":"'>&nbsp;{$r['K']}"; $Kf=$badge.$Kf."&nbsp;</span>";
   $Df = ($r['D']>0)?"badge-important'>&nbsp;{$r['D']}":"'>&nbsp;{$r['D']}"; $Df=$badge.$Df."&nbsp;</span>";
   $titlef = mb_substr (preg_replace('/[^\w\p{Cyrillic} .,!?*-]/u','_',$r['Title']),0,36,'UTF-8');
//   $last = new Date($r['Last login']).toJson;
//   $last = JSON.stringify($r['Last login']);
   $last = $r['Last login'];
   $r['Flight time'] = ($r['Flight time'][0]==="-")?0:$r['Flight time'];
   $google_JSON_rows[] = "{\"c\":[{\"v\": \"".strtoupper($pilot[0])."\", \"f\": \"$pilotf\"},{\"v\": \"".strtoupper($main_name)."\", \"f\": \"$mainf\"}, {\"v\": ".$r['K'].", \"f\": \"$Kf\"}, {\"v\": ".$r['D'].", \"f\": \"$Df\"}, {\"v\": \"".$titlef."\"}, {\"v\": \"".$r['Member since']."\"}, {\"v\": \"".$r['Flight time']."\"}, {\"v\": \"".$last."\"}, {\"v\": ".$pvp.", \"f\": \"$PVPf\"}, {\"v\": ".$r['Missions'].", \"f\": \"$Mf\"}, {\"v\": ".$r['Anomalies'].", \"f\": \"$Af\"}, {\"v\": ".$r['Bounty'].", \"f\": ".$r['Bounty']."}, {\"v\": \"".$r['Last location']."\"},{\"v\": \"".$r['Last ship']."\"}]}";
}    

$data = $google_JSON.implode(",",$google_JSON_rows)."]}";

mysqli_free_result($table);
//mysql_close($conn);able
mysqli_close($mysqli);

echo $data;
?>

