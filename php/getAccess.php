<?php
require_once("config.php");
session_start();

if(!empty($_SESSION['director'])) {
    echo $_SESSION['director'];
    exit;
}

$mysqli = mysqli_connect($config['db']['host'],$config['db']['username'],$config['db']['password'],$config['db']['dbname']) or die('1'.$config['db']['errormsg'].mysqli_connect_error());

$charid = $_SESSION['CCP']['user']['CharacterID'];

if(empty($charid)&&empty($_SESSION['user'])) {
  echo 0;
} elseif (!empty($charid)){
  $query_c = 'SELECT roles FROM characters WHERE id='.$charid.' AND roles=9223372036854775807';
  if ($result_c = mysqli_query($mysqli, $query_c)) {
    if ($result_c->num_rows > 0) {
        $_SESSION['director'] = 1;
        echo 1;
    }
    else echo 0;
  }
} elseif (!empty($_SESSION['user'])) {
  $_SESSION['director'] = 1;
  echo 1;
}
?>
