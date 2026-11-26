<?php
declare(strict_types=1);

require_once __DIR__ . '/Repository.php';
$config = require __DIR__ . '/../config.php';
session_name('PORTFOLIOSID');
session_set_cookie_params([
    'path' => rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/',
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

function repository(): Repository
{
    static $repository;
    global $config;
    return $repository ??= new Repository($config);
}

function h(string|int|null $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post(string $key): string
{
    return is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
}

function intValue(mixed $value): int
{
    return (int)(is_scalar($value) ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : 0);
}

function csrfField(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . h($_SESSION['csrf']) . '">';
}

function verifyPost(): bool
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], post('csrf'))) {
        http_response_code(400);
        exit('Форма устарела. Откройте страницу заново.');
    }
    return true;
}

function redirect(string $page): never
{
    header('Location: ' . $page, true, 303);
    exit;
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireRole(string $role): array
{
    $current = user();
    if (!$current) {
        redirect('login.php');
    }
    if ($current['role'] !== $role) {
        redirect($current['role'] === 'admin' ? 'admin.php' : 'dashboard.php');
    }
    return $current;
}

function notice(string $message, string $type = 'success'): void
{
    $_SESSION['notice'] = [$message, $type];
}

const STATUSES = [1 => 'На проверке', 2 => 'Подтверждено', 3 => 'Отклонено'];

function fieldError(string $name): void
{
    global $errors;
    if (isset($errors[$name])) {
        echo '<p class="field-error" id="' . h($name) . '-error">' . h($errors[$name]) . '</p>';
    }
}

function invalid(string $name): string
{
    global $errors;
    return isset($errors[$name]) ? ' aria-invalid="true" aria-describedby="' . h($name) . '-error"' : '';
}
