<?php
$pageTitle = 'Регистрация — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Регистрация', 'url' => '#']
];
$navButtons = [
    ['title' => 'Вход', 'url' => '/DE/korochki/pages/auth.php']
];

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Регистрация</h2>
    <form action="#" method="POST">
        <div class="form-group">
            <label>Логин</label>
            <input type="text" name="login" required>
            <span class="error-text">латиница и цифры, не менее 6 символов</span>
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
            <span class="error-text">минимум 8 символов</span>
        </div>
        <div class="form-group">
            <label>ФИО</label>
            <input type="text" name="fio" required>
            <span class="error-text">Используйте только символы кириллицы и пробелы</span>
        </div>
        <div class="form-group">
            <label>Адрес эл. почты</label>
            <input type="email" name="email" required>
            <span class="error-text">Неверный формат электронной почты</span>
        </div>
        <div class="form-group">
            <label>Телефон</label>
            <input type="tel" name="phone" placeholder="8(XXX)XXX-XX-XX" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <a href="/DE/korochki/pages/auth.php" class="btn-link">Уже есть аккаунт? Войти</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';