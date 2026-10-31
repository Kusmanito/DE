<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /DE/korochki/pages/auth.php');
    exit;
}

$pageTitle = 'Оставить отзыв — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Мои заявки', 'url' => '/DE/korochki/pages/zayavki.php'],
    ['title' => 'Оставить отзыв', 'url' => '#']
];

$userId = $_SESSION['user_id'];
$requestId = (int)($_GET['request_id'] ?? 0);
$error = '';
$success = '';

$stmt = $pdo->prepare("
    SELECT r.*, c.name_course
    FROM requests r
    LEFT JOIN courses c ON r.course_id = c.id_course
    WHERE r.id_request = ? AND r.user_id = ? AND r.status_id = 3 AND r.review_id IS NULL
");
$stmt->execute([$requestId, $userId]);
$request = $stmt->fetch();

if (!$request) {
    die("Заявка не найдена, либо обучение ещё не завершено, либо отзыв уже оставлен.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stars = (int)($_POST['stars'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($stars < 1 || $stars > 5) {
        $error = 'Поставьте оценку от 1 до 5 звёзд.';
    } elseif (empty($comment)) {
        $error = 'Напишите комментарий.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO reviews (comment, stars) VALUES (?, ?)");
            $stmt->execute([$comment, $stars]);
            $reviewId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("UPDATE requests SET review_id = ? WHERE id_request = ?");
            $stmt->execute([$reviewId, $requestId]);

            $pdo->commit();
            $success = 'Спасибо за отзыв! <a href="zayavki.php">Вернуться к заявкам</a>';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Ошибка при сохранении отзыва: ' . $e->getMessage();
        }
    }
}

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Оставить отзыв</h2>
    <p style="text-align: center; margin-bottom: 20px;"><strong>Курс:</strong> <?= htmlspecialchars($request['name_course']) ?></p>

    <?php if ($error): ?><div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color: green; text-align: center; margin-bottom: 15px;"><?= $success ?></div><?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <div class="form-group">
            <label>Оценка (1–5 звёзд)</label>
            <select name="stars" required>
                <option value="5">5 ★★★★★ — Отлично</option>
                <option value="4">4 ★★★★ — Хорошо</option>
                <option value="3">3 ★★★ — Нормально</option>
                <option value="2">2 ★★ — Плохо</option>
                <option value="1">1 ★ — Ужасно</option>
            </select>
        </div>
        <div class="form-group">
            <label>Комментарий</label>
            <textarea name="comment" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Отправить отзыв</button>
            <a href="/DE/korochki/pages/zayavki.php" class="btn-link">Отмена</a>
        </div>
    </form>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';