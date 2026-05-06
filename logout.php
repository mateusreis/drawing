<?php
session_start();
session_unset();
session_destroy();

setcookie('auth_token', '', time() - 3600, '/'); // Remove o cookie de lembrar

header('Location: index.php');
exit;