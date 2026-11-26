<?php
declare(strict_types=1);
date_default_timezone_set('Europe/Moscow');
if (session_status() === PHP_SESSION_NONE) {
    session_name('korochki_session');
    session_start(['use_strict_mode' => 1, 'cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
}
foreach ([$_GET, $_POST] as $fields) {
    foreach ($fields as $value) {
        if (!is_string($value) || str_contains($value, "\0") || !mb_check_encoding($value, 'UTF-8')) {
            http_response_code(400);
            exit('Некорректный формат данных формы.');
        }
    }
}
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
function e($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function csrf():string{return '<input type="hidden" name="csrf" value="'.e($_SESSION['csrf']).'">';}
if($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf'],(string)($_POST['csrf']??''))){http_response_code(403);exit('Сессия формы устарела. Вернитесь назад и обновите страницу.');}
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// Older accounts and the supplied seed use an ordinary bcrypt hash.
function coursePasswordHash(string $password): string {
    if (strlen($password) <= 72) return password_hash($password, PASSWORD_DEFAULT);
    return 'sha256:' . password_hash(hash('sha256', $password), PASSWORD_DEFAULT);
}

function coursePasswordMatches(string $password, string $stored): bool {
    if (str_starts_with($stored, 'sha256:')) {
        return password_verify(hash('sha256', $password), substr($stored, 7));
    }
    return strlen($password) <= 72 && password_verify($password, $stored);
}
