<?php 
require_once("eve.config.php");
session_start();
if(isset($_GET['code']) && defined("SSO_URL")){
	require_once ("ccpOAuth.php");
	$sso = new ccpOAuth(SSO_URL, SSO_CLIENTID, SSO_SECRET, SSO_CALLBACK, $curl, false);
	$token = $sso->getToken($_GET['code']);
    $userid = $sso->getUserInfo($token);
	print($_GET['code']);
}
?>