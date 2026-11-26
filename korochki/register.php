<?php
require_once __DIR__ . '/helpers/db.php';

$pageTitle = 'Регистрация — Корочки.есть';
$breadcrumbs = [['title' => 'Главная', 'url' => 'index.php'], ['title' => 'Регистрация']];
$fio = trim($_POST['fio'] ?? '');
$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $digits = preg_replace('/[^0-9]/', '', $phone);
    if ($fio === '' || $login === '' || $email === '' || trim($password) === '' || $phone === '') {
        $error = 'Заполните все поля';
    } elseif (mb_strlen($fio) > 100 || preg_match('/[^\p{L} -]/u', $fio) || !preg_match('/^[\p{Cyrillic}\p{Latin}]+(?:[ -]+[\p{Cyrillic}\p{Latin}]+)*$/uD', $fio)) {
        $error = 'ФИО должно содержать только буквы';
    } elseif (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный адрес электронной почты';
    } elseif (strlen($phone) > 16 || !preg_match('/^\+?[0-9 ()-]+$/D', $phone) || !in_array(strlen($digits), [10, 11, 12], true)) {
        $error = 'Введите корректный номер телефона';
    } elseif (!preg_match('/^[A-Za-z0-9]{6,50}$/D', $login)) {
        $error = 'Логин: только латиница и цифры, от 6 до 50 символов.';
    } elseif (mb_strlen($password) < 8) {
        $error = 'Пароль: минимум 8 символов.';
    } else {
        $lookup = $pdo->prepare('SELECT 1 FROM users WHERE email = ?');
        $lookup->execute([$email]);
        if ($lookup->fetchColumn()) {
            $error = 'Пользователь с такой почтой уже существует';
        } else {
            try {
                $insert = $pdo->prepare('INSERT INTO users (login, password, fio, email, phone, role_id) VALUES (?, ?, ?, ?, ?, 2)');
                $insert->execute([$login, coursePasswordHash($password), $fio, $email, $phone]);
                $success = 'Регистрация успешна!';
                $fio = $login = $email = $phone = '';
            } catch (PDOException $exception) {
                if ((int)($exception->errorInfo[1] ?? 0) === 1062) {
                    $lookup->execute([$email]);
                    $error = $lookup->fetchColumn()
                        ? 'Пользователь с такой почтой уже существует'
                        : 'Пользователь с таким логином уже существует.';
                } else {
                    $error = 'Не удалось сохранить пользователя. Попробуйте ещё раз.';
                }
            }
        }
    }
}

ob_start();
?>
<div class="form-container">
    <h1 class="page-heading">Регистрация</h1>
    <?php if ($error): ?><p class="alert alert-danger" role="alert"><?= e($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="alert alert-success" role="status"><?= e($success) ?> <a href="login.php">Войдите</a>.</p><?php endif; ?>
    <form method="post" novalidate>
        <?= csrf() ?>
        <div class="form-group">
            <label for="fio">ФИО</label>
            <input id="fio" name="fio" autocomplete="name" maxlength="100" required value="<?= e($fio) ?>">
            <span class="field-hint">Кириллица или латиница, пробелы и дефисы</span>
        </div>
        <div class="form-group">
            <label for="login">Логин</label>
            <input id="login" name="login" autocomplete="username" maxlength="50" required value="<?= e($login) ?>">
            <span class="field-hint">От 6 символов, латиница и цифры</span>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            <span class="field-hint">От 8 символов</span>
        </div>
        <div class="form-group">
            <label for="email">Почта</label>
            <input id="email" name="email" type="email" autocomplete="email" maxlength="100" required value="<?= e($email) ?>">
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input id="phone" type="tel" name="phone" inputmode="numeric" autocomplete="tel" placeholder="8(999)123-45-67" required value="<?= e($phone) ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <a href="login.php" class="btn-link">Уже есть аккаунт? Войти</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/helpers/layout.php';
