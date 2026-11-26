<?php

require_once __DIR__ . '/helpers/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ((int)$_SESSION['user_role'] === 1) {
    http_response_code(403);
    exit('Администратор не может создавать заявки.');
}

$pageTitle = 'Подача заявки — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => 'index.php'],
    ['title' => 'Мои заявки', 'url' => 'dashboard.php'],
    ['title' => 'Подача заявки', 'url' => '#']
];

$error = '';
$success = '';

$courses = $pdo->query("SELECT * FROM courses")->fetchAll();
$payments = $pdo->query("SELECT * FROM payments")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = filter_var($_POST['course'] ?? '', FILTER_VALIDATE_INT);
    $rawDate=trim($_POST['date']??'');
    $parsedDate=DateTimeImmutable::createFromFormat('!d.m.Y',$rawDate);
    $date=$parsedDate?$parsedDate->format('Y-m-d'):'';
    $paymentId = filter_var($_POST['payment'] ?? '', FILTER_VALIDATE_INT);
    $userId = $_SESSION['user_id'];

    if (!in_array($courseId,array_column($courses,'id_course')) || !in_array($paymentId,array_column($payments,'id_payment')) || !$parsedDate || $parsedDate->format('d.m.Y')!==$rawDate || (int)$parsedDate->format('Y') < 1000) {
        $error = 'Выберите курс, способ оплаты и существующую дату в формате ДД.ММ.ГГГГ.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO requests (course_id, data_nach, payment_id, status_id, user_id) VALUES (?, ?, ?, 1, ?)");
            $stmt->execute([$courseId, $date, $paymentId, $userId]);
            $_SESSION['flash']='Заявка успешно создана!';
            header('Location: dashboard.php');exit;
        } catch (PDOException $e) {
            $error = 'Не удалось создать заявку. Попробуйте ещё раз.';
        }
    }
}

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Подача заявки</h2>

    <?php if ($error): ?><div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color: green; text-align: center; margin-bottom: 15px;"><?= $success ?></div><?php endif; ?>

    <form action="" method="POST"><?= csrf() ?>
        <div class="form-group">
            <label for="course">Наименование курса</label>
            <select name="course" id="course" required>
                <option value="">— Выберите курс —</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id_course'] ?>" <?= (int)($_POST['course']??0)===(int)$c['id_course']?'selected':'' ?>><?= htmlspecialchars($c['name_course']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Дата начала обучения</label>
            <div class="date-entry"><input type="text" name="date" id="date" inputmode="numeric" placeholder="ДД.ММ.ГГГГ" value="<?=e($_POST['date']??'')?>" required><button type="button" id="open-calendar" class="calendar-button" aria-label="Выбрать дату в календаре" title="Выбрать дату в календаре">📅</button><input type="date" id="date-calendar" class="calendar-input" tabindex="-1" aria-label="Дата начала обучения — календарь"></div><span class="field-hint">Например, 15.12.2026</span>
        </div>
        <div class="form-group">
            <label for="payment">Способ оплаты</label>
            <select name="payment" id="payment" required>
                <option value="">— Выберите способ —</option>
                <?php foreach ($payments as $p): ?>
                    <option value="<?= $p['id_payment'] ?>" <?= (int)($_POST['payment']??0)===(int)$p['id_payment']?'selected':'' ?>><?= htmlspecialchars($p['method']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Отправить заявку</button>
            <a href="dashboard.php" class="btn-link">Мои заявки</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/helpers/layout.php';
