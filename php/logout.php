<?php 
    require("config.php"); 
    session_start();//session is a way to store information (in variables) to be used across multiple pages.  
    unset($_SESSION['user']);
    unset($_SESSION['CCP']['user']);
    unset($_SESSION['CCP']['user']['CharacterID']);
    unset($_SESSION['director']);
    session_destroy();    
//    header("Location: ../index.html"); 
//    die("Redirecting to: index.html");
?>
