<?php
$pageTitle = 'Авторизация — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Авторизация', 'url' => '#']
];
$navButtons = [
    ['title' => 'Регистрация', 'url' => '/DE/korochki/pages/register.php']
];

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Авторизация</h2>
    <form action="#" method="POST">
        <div class="form-group">
            <label>Логин</label>
            <input type="text" name="login" required>
            <span class="error-text">Неверный логин</span>
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
            <span class="error-text">Неверный пароль</span>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Войти в аккаунт</button>
            <a href="/DE/korochki/pages/register.php" class="btn-link">Ещё не зарегистрированы? Регистрация</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';