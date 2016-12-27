<?php 
    require("config.php"); 
    $submitted_username = ''; 
//    session_start();
    if(!empty($_GET['u'])){ 
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');     
        try { $db = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['username'], $config['db']['password'], $options); } 
       catch(PDOException $ex){ die("Failed to connect to the database: " . $ex->getMessage());} 
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
        header('Content-Type: text/html; charset=utf-8'); 
        session_start();
        $query = " 
            SELECT 
                id, 
                username, 
                password 
           FROM users 
            WHERE 
                username = :username 
        "; 
        $query_params = array( 
//            ':username' => $_POST['username'] 
            ':username' => $_GET['u'] 
        ); 
          
        try{ 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $login_ok = false; 
        $row = $stmt->fetch(); 
        $check_password = $_GET['p']; 
//        $check_password = $_POST['password']; 
        if($row){ 
            if($check_password === $row['password']){
                $login_ok = true;
            } 
        } 
 
        if($login_ok){ 
            $_SESSION['user'] = $row['username'];
            $_SESSION['director'] = 1;
//            print($_SESSION['user']);
            print(session_id());
//            session_write_close();
        } 
        else{ 
            print("Login Failed."); 
            $submitted_username = htmlentities($_GET['u'], ENT_QUOTES, 'UTF-8'); 
        } 
    }
?> 
