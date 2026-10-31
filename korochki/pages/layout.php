<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$pageTitle   = $pageTitle   ?? 'Корочки.Есть';
$breadcrumbs = $breadcrumbs ?? [];
$content     = $content     ?? '';
$logoPath    = '/DE/korochki/media/image02.jpg';

// Динамическое меню в зависимости от роли
$navButtons = [];
if (isset($_SESSION['user_id'])) {
    $navButtons[] = ['title' => 'Мои заявки', 'url' => '/DE/korochki/pages/zayavki.php'];
    $navButtons[] = ['title' => 'Новая заявка', 'url' => '/DE/korochki/pages/new.php'];
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
        $navButtons[] = ['title' => 'Админка', 'url' => '/DE/korochki/pages/admin.php'];
    }
    $navButtons[] = ['title' => 'Выход', 'url' => '/DE/korochki/pages/logout.php'];
} else {
    $navButtons[] = ['title' => 'Вход', 'url' => '/DE/korochki/pages/auth.php'];
    $navButtons[] = ['title' => 'Регистрация', 'url' => '/DE/korochki/pages/register.php'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/DE/korochki/styles/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-top">
        <a href="/DE/korochki/pages/index.php" class="logo">
            <img src="<?= $logoPath ?>" alt="Логотип">
            <span class="site-title">Корочки.есть</span>
        </a>
        <div class="header-actions">
            <div class="search-box">
                <input type="text" placeholder="Search">
            </div>
            <?php foreach ($navButtons as $btn): ?>
                <a href="<?= htmlspecialchars($btn['url']) ?>" class="nav-btn">
                    <?= htmlspecialchars($btn['title']) ?>
                </a>
            <?php endforeach; ?>
        </div>
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
    <p>&copy; <?= date('Y') ?> Корочки.Есть — портал онлайн-курсов дополнительного профессионального образования.</p>
</footer>

<script src="/DE/korochki/scripts/script.js"></script>
</body>
</html>