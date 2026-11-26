<?php
$current = user();
$navigation = match ($current['role'] ?? '') {
    'student' => ['dashboard.php' => 'Мои достижения', 'add_request.php' => 'Добавить достижение'],
    'admin' => ['admin.php' => 'Панель куратора'],
    default => ['login.php' => 'Вход', 'register.php' => 'Регистрация'],
};
$active = basename($_SERVER['SCRIPT_NAME']);
$flash = $_SESSION['notice'] ?? null;
unset($_SESSION['notice']);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> · Портфолио</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script defer src="assets/app.js"></script>
</head>
<body>
<header class="site-header">
    <div class="shell header-inner">
        <a class="brand" href="index.php"><span aria-hidden="true">П</span> Портфолио</a>
        <button class="menu-toggle" type="button" aria-label="Меню" aria-controls="site-navigation" aria-expanded="false" hidden>☰</button>
        <nav id="site-navigation" aria-label="Главное меню">
            <?php foreach ($navigation as $url => $label): ?>
                <a href="<?= h($url) ?>"<?= $active === $url ? ' aria-current="page"' : '' ?>><?= h($label) ?></a>
            <?php endforeach; ?>
            <?php if ($current): ?>
                <form action="logout.php" method="post"><?= csrfField() ?><button type="submit">Выйти</button></form>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="shell main">
    <?php if ($flash): ?>
        <div class="notice notice-<?= h($flash[1]) ?>" role="<?= $flash[1] === 'error' ? 'alert' : 'status' ?>">
            <span><?= h($flash[0]) ?></span><button type="button" class="notice-close" aria-label="Закрыть уведомление" hidden>×</button>
        </div>
    <?php endif; ?>
