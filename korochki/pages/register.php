<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

$pageTitle = 'Регистрация — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Регистрация', 'url' => '#']
];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $fio = trim($_POST['fio'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (strlen($login) < 6) {
        $error = 'Логин должен быть не менее 6 символов.';
    } elseif (strlen($password) < 8) {
        $error = 'Пароль должен быть не менее 8 символов.';
    } elseif (!preg_match('/^[а-яА-ЯёЁ\s]+$/u', $fio)) {
        $error = 'ФИО должно содержать только кириллицу и пробелы.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Неверный формат email.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_user FROM users WHERE login = ?");
            $stmt->execute([$login]);
            if ($stmt->fetch()) {
                $error = 'Пользователь с таким логином уже существует.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (login, password, fio, email, phone, role_id) VALUES (?, ?, ?, ?, ?, 2)");
                $stmt->execute([$login, $hash, $fio, $email, $phone]);
                $success = 'Регистрация успешна! <a href="auth.php">Войдите</a>.';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }
}

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Регистрация</h2>

    <?php if ($error): ?><div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color: green; text-align: center; margin-bottom: 15px;"><?= $success ?></div><?php endif; ?>

    <form action="" method="POST">
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