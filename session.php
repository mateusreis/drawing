<?php 
session_start();

// Retorna "Acesso Negado" (Erro 403) para requisições AJAX não autenticadas
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit;
}

$expire=time()+60*60*24*30;

if(isset($_GET['time'])){
    setcookie("time", $_GET['time']);
}

if(isset($_GET['pause'])){
    setcookie("pause", $_GET['pause']);
}

if(isset($_GET['models'])){
    setcookie("models", $_GET['models']);
}

if(isset($_GET['qtd'])){
    setcookie("qtd", $_GET['qtd']);
}

if(isset($_GET['timer'])){
    setcookie("timer", $_GET['timer']);
}

// echo "time: " . $_GET['time'] .", model: " .$_GET['model'] .", qtd: " .$_GET['qtd'] .", pause: " .$_GET['pause'];

 ?>