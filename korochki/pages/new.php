<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /DE/korochki/pages/auth.php');
    exit;
}

$pageTitle = 'Подача заявки — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Мои заявки', 'url' => '/DE/korochki/pages/zayavki.php'],
    ['title' => 'Подача заявки', 'url' => '#']
];

$error = '';
$success = '';

$courses = $pdo->query("SELECT * FROM courses")->fetchAll();
$payments = $pdo->query("SELECT * FROM payments")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = (int)($_POST['course'] ?? 0);
    $date = $_POST['date'] ?? '';
    $paymentId = (int)($_POST['payment'] ?? 0);
    $userId = $_SESSION['user_id'];

    if ($courseId <= 0 || $paymentId <= 0 || empty($date)) {
        $error = 'Заполните все поля.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO requests (course_id, data_nach, payment_id, status_id, user_id) VALUES (?, ?, ?, 1, ?)");
            $stmt->execute([$courseId, $date, $paymentId, $userId]);
            $success = 'Заявка успешно создана! <a href="zayavki.php">Перейти к моим заявкам</a>';
        } catch (PDOException $e) {
            $error = 'Ошибка при создании заявки: ' . $e->getMessage();
        }
    }
}

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Подача заявки</h2>

    <?php if ($error): ?><div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color: green; text-align: center; margin-bottom: 15px;"><?= $success ?></div><?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Наименование курса</label>
            <select name="course" required>
                <option value="">— Выберите курс —</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id_course'] ?>"><?= htmlspecialchars($c['name_course']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Дата начала обучения</label>
            <input type="date" name="date" required>
        </div>
        <div class="form-group">
            <label>Способ оплаты</label>
            <select name="payment" required>
                <option value="">— Выберите способ —</option>
                <?php foreach ($payments as $p): ?>
                    <option value="<?= $p['id_payment'] ?>"><?= htmlspecialchars($p['method']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Подать заявку</button>
            <a href="/DE/korochki/pages/zayavki.php" class="btn-link">Мои заявки</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';