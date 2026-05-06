<?php
session_start();
require_once 'config.php';

// Verifica se já existe sessão ativa ou um cookie "Lembrar de mim" válido
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: gallery.php');
    exit;
} elseif (isset($_COOKIE['auth_token'])) {
    list($cookie_user, $cookie_token) = explode(':', $_COOKIE['auth_token']);
    if ($cookie_user === AUTH_USER && hash_equals(hash_hmac('sha256', $cookie_user, SECRET_KEY), $cookie_token)) {
        $_SESSION['logged_in'] = true;
        header('Location: gallery.php');
        exit;
    }
}
?>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Drawing Slideshow</title>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="" />
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
    </head>
    <body style="display:flex; align-items:center; justify-content:center; min-height:100vh;">

      <form class="form-signin" method="post" action="login.php" style="width:320px;">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email</label>
        <input type="email" name="username" id="inputEmail" class="form-control" placeholder="Email">
        <br>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password">

        <div class="checkbox">
          <label>
            <input type="checkbox" value="yes" name="rememberme"> Remember me
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </body>
</html>