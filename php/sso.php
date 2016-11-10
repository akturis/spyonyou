<?php 
include_once('eve.config.php');
$pages=array("index.php","manage.php");
if(isset($_POST['action'])){
        $info['result']="success";
        switch($_POST['action']){
                case "ssoLogin":
                    session_start();
                    if(defined("SSO_URL")) {
                        require_once ("ccpOAuth.php");
                        $sso = new ccpOAuth(SSO_URL, SSO_CLIENTID, SSO_SECRET, SSO_CALLBACK, $curl, false);
                        $info['url']=$sso->generateLink($_POST['scopes']);
                    }else {
                        $_SESSION['mailFormatted'] = false;
                    }
                break;
                default:
                        $info['result']="failure";
                break;

        }

        echo json_encode($info);
}



 ?>