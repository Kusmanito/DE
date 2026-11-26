<?php
require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/uploads.php';
$student = requireRole('student');
$title = 'Новое достижение';
$types = repository()->types();
$levels = repository()->levels();
$errors = [];
$type = $level = 0;
$name = $date = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > MAX_UPLOAD) {
    $errors['file'] = 'Максимальный размер документа — 10 МБ.';
} elseif (verifyPost()) {
    $type = intValue(post('type'));
    $level = intValue(post('level'));
    $name = trim(post('event_name'));
    $date = trim(post('event_date'));
    $parsed = str_contains($date, chr(0)) ? false : DateTimeImmutable::createFromFormat('!d.m.Y', $date);
    if (!in_array($type, array_map('intval', array_column($types, 'id')), true)) {
        $errors['type'] = 'Выберите тип достижения.';
    }
    if (!in_array($level, array_map('intval', array_column($levels, 'id')), true)) {
        $errors['level'] = 'Выберите уровень мероприятия.';
    }
    if (!mb_check_encoding($name) || $name === '' || mb_strlen($name) > 180) {
        $errors['event_name'] = 'Введите название мероприятия длиной до 180 символов.';
    }
    if (!$parsed || $parsed->format('d.m.Y') !== $date || (int)$parsed->format('Y') < 1000) {
        $errors['event_date'] = 'Введите существующую дату в формате ДД.ММ.ГГГГ.';
    }
    if (!$errors) {
        try {
            [$stored, $original] = storeDocument($_FILES['file'] ?? [], $config['storage']);
            try {
                repository()->submit($student['id'], $type, $level, $name, $parsed->format('Y-m-d'), $stored, $original);
            } catch (Throwable $error) {
                unlink($config['storage'] . '/' . $stored);
                throw $error;
            }
            notice('Достижение отправлено на проверку куратору.');
            redirect('dashboard.php');
        } catch (DomainException $error) {
            $errors['file'] = $error->getMessage();
        } catch (RuntimeException $error) {
            error_log($error->getMessage());
            $errors['file'] = 'Не удалось сохранить достижение. Повторите попытку позже.';
        }
    } elseif (($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $errors['file'] = 'Выберите подтверждающий документ.';
    }
}
require __DIR__ . '/src/header.php';
?>
<section class="panel form-panel">
    <h1>Новое достижение</h1>
    <form method="post" enctype="multipart/form-data" novalidate>
        <?= csrfField() ?>
        <div class="field">
            <label for="type">Тип достижения</label>
            <select id="type" name="type" required<?= invalid('type') ?>>
                <option value="">Выберите тип</option>
                <?php foreach ($types as $item): ?>
                    <option value="<?= h($item['id']) ?>"<?= (int)$item['id'] === $type ? ' selected' : '' ?>><?= h($item['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php fieldError('type'); ?>
        </div>
        <div class="field">
            <label for="event_name">Название мероприятия</label>
            <input id="event_name" name="event_name" required maxlength="180" value="<?= h($name) ?>"<?= invalid('event_name') ?>>
            <?php fieldError('event_name'); ?>
        </div>
        <div class="field">
            <label for="event_date">Дата участия</label>
            <input id="event_date" name="event_date" required inputmode="numeric" maxlength="10" placeholder="ДД.ММ.ГГГГ" value="<?= h($date) ?>"<?= invalid('event_date') ?>>
            <?php fieldError('event_date'); ?>
        </div>
        <div class="field">
            <label for="level">Уровень мероприятия</label>
            <select id="level" name="level" required<?= invalid('level') ?>>
                <option value="">Выберите уровень</option>
                <?php foreach ($levels as $item): ?>
                    <option value="<?= h($item['id']) ?>"<?= (int)$item['id'] === $level ? ' selected' : '' ?>><?= h($item['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php fieldError('level'); ?>
        </div>
        <div class="field">
            <label for="file">Подтверждающий документ</label>
            <input id="file" type="file" name="file" accept=".pdf,.jpg,.jpeg" required<?= invalid('file') ?>>
            <p class="hint">Сертификат или диплом в PDF или JPG, до 10 МБ</p>
            <?php fieldError('file'); ?>
        </div>
        <button class="action" type="submit">Отправить</button>
    </form>
</section>
<?php require __DIR__ . '/src/footer.php'; ?>
