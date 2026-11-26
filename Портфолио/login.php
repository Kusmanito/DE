<?php
require __DIR__ . '/src/bootstrap.php';
$title = 'Вход';
$errors = [];
$login = '';
if (verifyPost()) {
    $login = trim(post('login'));
    $password = post('password');
    if ($login === '' || strlen($login) > 64) {
        $errors['login'] = 'Введите логин.';
    }
    if ($password === '' || strlen($password) > 72) {
        $errors['password'] = 'Введите пароль.';
    }
    if (!$errors) {
        $account = repository()->findUser($login);
        $hash = $account['password_hash'] ?? '$2y$10$X82Me1A9sl5q21VFLeo0Tu.YYHNTbHCDeXcz22ZKmo.6q94VSEhHS';
        if (password_verify($password, $hash) && $account) {
            session_regenerate_id(true);
            $_SESSION = ['user' => ['id' => (int)$account['id'], 'role' => $account['role']]];
            redirect($account['role'] === 'admin' ? 'admin.php' : 'dashboard.php');
        }
        $errors['login'] = 'Неверный логин или пароль.';
    }
}
require __DIR__ . '/src/header.php';
?>
<section class="panel form-panel">
    <h1>Вход</h1>
    <form method="post" novalidate>
        <?= csrfField() ?>
        <div class="field">
            <label for="login">Логин</label>
            <input id="login" name="login" required autocomplete="username" maxlength="64" value="<?= h($login) ?>"<?= invalid('login') ?>>
            <?php fieldError('login'); ?>
        </div>
        <div class="field">
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"<?= invalid('password') ?>>
            <?php fieldError('password'); ?>
        </div>
        <button class="action" type="submit">Войти</button>
    </form>
    <a class="text-link form-link" href="register.php">Еще не зарегистрированы? Регистрация</a>
</section>
<?php require __DIR__ . '/src/footer.php'; ?>
