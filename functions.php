<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bloqueia acesso e redireciona para login se o usuário não estiver autenticado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

function cleanFolderName($f){
    $new = str_replace("-", " ", $f);
    $str2 = substr($new, 3);
    return $str2;
}

function getCookies(){

	if(!isset($_COOKIE['models'])){
	    setcookie("models", "");
	}

	if(!isset($_COOKIE['qtd'])){
	    setcookie("qtd", "20");
	}

	if(!isset($_COOKIE['time'])){
	    setcookie("time", "180");
	}

	if(!isset($_COOKIE['pause'])){
	    setcookie("pause", "3");
	}

	if(!isset($_COOKIE['timer'])){
	    setcookie("timer", "yes");
	}

	return true;
}
?>