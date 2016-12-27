<?php 
    require("config.php"); 
    session_start();
//    print(session_id());
//    if (!isset($_SESSION)) { session_start(); }
    if (empty($_SESSION['CCP']['user']['CharacterID'])&&empty($_SESSION['user'])) {
        echo "<a href='#' id='ssoSelectButton'><img alt='EVE SSO Login Buttons Small White' src='https://images.contentful.com/idjq7aai9ylm/18BxKSXCymyqY4QKo8KwKe/c2bdded6118472dd587c8107f24104d7/EVE_SSO_Login_Buttons_Small_White.png?w=195&amp;h=30' data-pin-nopin='true'></a>";    
    }
?> 
