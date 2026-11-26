<?php
require_once __DIR__ . '/helpers/db.php';

$pageTitle = 'Вход — Корочки.есть';
$breadcrumbs = [['title' => 'Главная', 'url' => 'index.php'], ['title' => 'Вход']];
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ((int)$_SESSION['user_role'] === 1 ? 'admin.php' : 'dashboard.php'));
    exit;
}

$login = trim($_POST['login'] ?? '');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($login === '' || trim($password) === '') {
        $error = 'Заполните пустые поля';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        if ($user && coursePasswordMatches($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['fio'];
            $_SESSION['user_role'] = $user['role_id'];
            header('Location: ' . ((int)$user['role_id'] === 1 ? 'admin.php' : 'dashboard.php'));
            exit;
        }
        $error = 'Пользователь с такими данными не найден в базе';
    }
}

ob_start();
?>
<div class="form-container">
    <h1 class="page-heading">Вход</h1>
    <?php if ($error): ?><p class="alert alert-danger" role="alert"><?= e($error) ?></p><?php endif; ?>
    <form method="post" novalidate>
        <?= csrf() ?>
        <div class="form-group">
            <label for="login">Логин</label>
            <input id="login" name="login" autocomplete="username" required value="<?= e($login) ?>">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Войти</button>
            <a href="register.php" class="btn-link">Еще не зарегистрированы? Регистрация</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/helpers/layout.php';
