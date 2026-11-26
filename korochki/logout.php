<?php
require_once __DIR__.'/helpers/security.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Используйте кнопку «Выход».');}
$_SESSION=[];session_regenerate_id(true);header('Location: index.php');exit;
