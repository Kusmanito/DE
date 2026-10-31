<?php
session_start();
require_once __DIR__ . '/../helpers/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /DE/korochki/pages/auth.php');
    exit;
}

$pageTitle = 'Мои заявки — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Мои заявки', 'url' => '#']
];

$userId = $_SESSION['user_id'];

$sql = "
    SELECT r.*, c.name_course, p.method, s.name_status, rv.stars, rv.comment
    FROM requests r
    LEFT JOIN courses c ON r.course_id = c.id_course
    LEFT JOIN payments p ON r.payment_id = p.id_payment
    LEFT JOIN statuses s ON r.status_id = s.id_status
    LEFT JOIN reviews rv ON r.review_id = rv.id_review
    WHERE r.user_id = ?
    ORDER BY r.id_request DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <a href="/DE/korochki/pages/new.php" class="btn btn-primary" style="width: auto;">Создать новую заявку</a>
</div>

<div class="requests-grid">
    <?php if (empty($requests)): ?>
        <p style="grid-column: 1 / -1; text-align: center;">У вас пока нет заявок. <a href="new.php">Создайте первую!</a></p>
    <?php else: ?>
        <?php foreach ($requests as $req): ?>
        <div class="request-card">
            <div class="request-card-header">Заявка №<?= $req['id_request'] ?></div>
            <div class="request-card-body">
                <p><strong>Курс:</strong> <?= htmlspecialchars($req['name_course'] ?? 'Неизвестно') ?></p>
                <p><strong>Статус:</strong> <?= htmlspecialchars($req['name_status'] ?? 'Неизвестно') ?></p>
                <p><strong>Оплата:</strong> <?= htmlspecialchars($req['method'] ?? 'Неизвестно') ?></p>
                <p><strong>Дата начала:</strong> <?= date('d.m.Y', strtotime($req['data_nach'])) ?></p>

                <?php if ($req['review_id']): ?>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                        <p><strong>Ваш отзыв:</strong> <?= htmlspecialchars($req['comment']) ?> (<?= $req['stars'] ?> ★)</p>
                    </div>
                <?php elseif ($req['name_status'] === 'Обучение завершено'): ?>
                    <div style="margin-top: 10px;">
                        <a href="review.php?request_id=<?= $req['id_request'] ?>" style="color: #b04f9e; text-decoration: none; font-size: 14px;">Оставить отзыв</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';