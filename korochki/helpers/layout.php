<?php
require_once __DIR__.'/security.php';

$pageTitle   = $pageTitle   ?? 'Корочки.Есть';
$breadcrumbs = $breadcrumbs ?? [];
$content     = $content     ?? '';
$logoPath    = 'media/image02.jpg';

// Динамическое меню в зависимости от роли
$navButtons = [];
if (isset($_SESSION['user_id'])) {
    if ((int)($_SESSION['user_role']??2)!==1) {
        $navButtons[] = ['title' => 'Мои заявки', 'url' => 'dashboard.php'];
        $navButtons[] = ['title' => 'Новая заявка', 'url' => 'add_request.php'];
    }
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
        $navButtons[] = ['title' => 'Панель администратора', 'url' => 'admin.php'];
    }
    $navButtons[] = ['title' => 'Выйти', 'url' => 'logout.php'];
} else {
    $navButtons[] = ['title' => 'Вход', 'url' => 'login.php'];
    $navButtons[] = ['title' => 'Регистрация', 'url' => 'register.php'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css?v=<?=filemtime(__DIR__.'/../css/style.css')?>">
</head>
<body>

<header class="site-header">
    <div class="header-top">
        <a href="index.php" class="logo">
            <img src="<?= $logoPath ?>" alt="Логотип">
            <span class="site-title">Корочки.есть</span>
        </a>
        <button class="menu-toggle" type="button" aria-label="Меню" aria-controls="site-navigation" aria-expanded="false" hidden>☰</button>
        <nav class="header-actions" id="site-navigation" aria-label="Главное меню">
            <?php foreach ($navButtons as $btn): ?>
                <?php if($btn['title']==='Выйти'): ?><form action="logout.php" method="post" class="logout-form"><?=csrf()?><button class="nav-btn" type="submit">Выйти</button></form><?php else: ?><a href="<?= htmlspecialchars($btn['url']) ?>" class="nav-btn">
                    <?= htmlspecialchars($btn['title']) ?>
                </a><?php endif ?>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<?php if (!empty($breadcrumbs)): ?>
    <nav class="breadcrumbs">
        <ul>
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <li>
                    <?php if ($i < count($breadcrumbs) - 1 && !empty($crumb['url'])): ?>
                        <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['title']) ?></a>
                    <?php else: ?>
                        <span class="current"><?= htmlspecialchars($crumb['title']) ?></span>
                    <?php endif; ?>
                    <?php if ($i < count($breadcrumbs) - 1): ?><span class="separator">/</span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
<?php endif; ?>

<main class="site-content">
    <?= $content ?>
</main>

<footer class="site-footer">
    <p>Контакты <a href="mailto:admin@example.com">admin@example.com</a></p>
    <p>&copy; <?= date('Y') ?> Корочки.Есть — портал онлайн-курсов дополнительного профессионального образования.</p>
</footer>

<script src="scripts/script.js?v=<?=filemtime(__DIR__.'/../scripts/script.js')?>"></script>
</body>
</html>
