<?php
session_start();
require_once 'config.php';

if (isset($_POST['username']) && isset($_POST['password'])) {

    if (($_POST['username'] == AUTH_USER) && ($_POST['password'] == AUTH_PASS)) {    
        
        $_SESSION['logged_in'] = true;
        
        if (isset($_POST['rememberme'])) {
            $token = hash_hmac('sha256', AUTH_USER, SECRET_KEY);
            setcookie('auth_token', AUTH_USER . ':' . $token, time()+60*60*24*30, '/', '', false, true);
        }
        
        header('Location: gallery.php');
        exit;
        
    } else {
        echo 'Username/Password Invalid';
    }
    
} else {
    echo 'You must supply a username and password.';
}
?>