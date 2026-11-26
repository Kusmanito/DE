<?php
require __DIR__ . '/src/bootstrap.php';
if (!verifyPost()) {
    redirect('index.php');
}
$_SESSION = [];
$params = session_get_cookie_params();
unset($params['lifetime']);
setcookie(session_name(), '', ['expires' => time() - 3600, ...$params]);
session_destroy();
redirect('login.php');
