<?php
require __DIR__ . '/src/bootstrap.php';
$title = 'Регистрация';
$fields = array_fill_keys(['login', 'full_name', 'phone', 'email'], '');
$errors = [];
if (verifyPost()) {
    foreach ($fields as $name => $value) {
        $fields[$name] = trim(post($name));
    }
    $password = post('password');
    if (!preg_match('/^[A-Za-z0-9]{6,64}$/D', $fields['login'])) {
        $errors['login'] = 'Введите от 6 до 64 латинских букв или цифр.';
    }
    if (!mb_check_encoding($password) || mb_strlen($password) < 8 || strlen($password) > 72 || str_contains($password, chr(0))) {
        $errors['password'] = 'Введите не менее 8 символов. Максимальный размер пароля — 72 байта.';
    }
    if (!preg_match('/^\p{Cyrillic}+(?: +\p{Cyrillic}+)*$/uD', $fields['full_name']) || mb_strlen($fields['full_name']) > 120) {
        $errors['full_name'] = 'Укажите ФИО кириллицей, разделяя слова пробелами.';
    }
    if (!preg_match('/^8\([0-9]{3}\)[0-9]{3}-[0-9]{2}-[0-9]{2}$/D', $fields['phone'])) {
        $errors['phone'] = 'Введите телефон в формате 8(XXX)XXX-XX-XX.';
    }
    if (strlen($fields['email']) > 254 || !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите действительный адрес электронной почты.';
    }
    if (!$errors) {
        if (repository()->register($fields, $password)) {
            notice('Учётная запись создана. Войдите в портал.');
            redirect('login.php');
        }
        $errors['login'] = 'Этот логин уже занят.';
    }
}
require __DIR__ . '/src/header.php';
?>
<section class="panel form-panel">
    <h1>Регистрация</h1>
    <form method="post" novalidate>
        <?= csrfField() ?>
        <div class="field">
            <label for="full_name">ФИО</label>
            <input id="full_name" name="full_name" required autocomplete="name" maxlength="120" value="<?= h($fields['full_name']) ?>"<?= invalid('full_name') ?>>
            <?php fieldError('full_name'); ?>
        </div>
        <div class="field">
            <label for="phone">Телефон</label>
            <input id="phone" name="phone" type="tel" required autocomplete="tel" placeholder="8(999)123-45-67" maxlength="15" value="<?= h($fields['phone']) ?>"<?= invalid('phone') ?>>
            <?php fieldError('phone'); ?>
        </div>
        <div class="field">
            <label for="email">Электронная почта</label>
            <input id="email" name="email" type="email" required autocomplete="email" maxlength="254" value="<?= h($fields['email']) ?>"<?= invalid('email') ?>>
            <?php fieldError('email'); ?>
        </div>
        <div class="field">
            <label for="login">Логин</label>
            <input id="login" name="login" required autocomplete="username" minlength="6" maxlength="64" value="<?= h($fields['login']) ?>"<?= invalid('login') ?>>
            <p class="hint">От 6 символов, латиница и цифры</p>
            <?php fieldError('login'); ?>
        </div>
        <div class="field">
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"<?= invalid('password') ?>>
            <p class="hint">Не менее 8 символов</p>
            <?php fieldError('password'); ?>
        </div>
        <button class="action" type="submit">Зарегистрироваться</button>
    </form>
    <a class="text-link form-link" href="login.php">Уже зарегистрированы? Вход</a>
</section>
<?php require __DIR__ . '/src/footer.php'; ?>
