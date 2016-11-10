<?php
require_once("config.php");

if ((@$_SERVER['PHP_AUTH_PW']===$config['user']['pass'] && @$_SERVER['PHP_AUTH_USER']===$config['user']['name']) && strlen(@$_SERVER['PHP_AUTH_USER'])>1) {
    echo "logged in as {$_SERVER['PHP_AUTH_USER']} ";
} else {
    if (!isset($_GET['query'])) {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
    }
    echo 0;
}
?>

