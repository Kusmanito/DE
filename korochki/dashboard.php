<?php

require_once __DIR__ . '/helpers/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Мои заявки — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => 'index.php'],
    ['title' => 'Мои заявки', 'url' => '#']
];

if ((int)$_SESSION['user_role'] === 1) {
    header('Location: admin.php');
    exit;
}

$userId = $_SESSION['user_id'];
$countStmt = $pdo->prepare('SELECT COUNT(*) FROM requests WHERE user_id = ?');
$countStmt->execute([$userId]);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / 3));
$currentPage = min($totalPages, max(1, (int)($_GET['p'] ?? 1)));
$offset = ($currentPage - 1) * 3;

$sql = "
    SELECT r.*, c.name_course, p.method, rv.comment
    FROM requests r
    LEFT JOIN courses c ON r.course_id = c.id_course
    LEFT JOIN payments p ON r.payment_id = p.id_payment
    LEFT JOIN reviews rv ON r.review_id = rv.id_review
    WHERE r.user_id = ?
    ORDER BY r.id_request DESC
    LIMIT 3 OFFSET " . $offset;
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

ob_start();
?>
<h1 class="page-heading">Мои заявки</h1>
<?php if(isset($_SESSION['review_error'])): ?><div class="alert alert-danger" role="alert"><?=e($_SESSION['review_error'])?></div><?php unset($_SESSION['review_error']);endif?>
<?php if(isset($_SESSION['flash'])): ?><div class="alert alert-success" role="status"><?=e($_SESSION['flash'])?></div><?php unset($_SESSION['flash']);endif?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <a href="add_request.php" class="btn btn-primary" style="width: auto;">Создать новую заявку</a>
</div>

<div class="requests-grid">
    <?php if (empty($requests)): ?>
        <p style="grid-column: 1 / -1; text-align: center;">У вас пока нет заявок. <a href="add_request.php">Создайте первую!</a></p>
    <?php else: ?>
        <?php foreach ($requests as $req): ?>
        <article class="request-card request-status-<?=(int)$req['status_id']?>" id="request-<?=$req['id_request']?>">
            <div class="request-card-header">Заявка №<?= $req['id_request'] ?></div>
            <div class="request-card-body">
                <p><strong>Курс:</strong> <?= htmlspecialchars($req['name_course'] ?? 'Неизвестно') ?></p>
                <p><strong>Статус:</strong> <span class="status-tag"><?= e(REQUEST_STATUSES[(int)$req['status_id']] ?? 'Неизвестно') ?></span></p>
                <p><strong>Оплата:</strong> <?= htmlspecialchars($req['method'] ?? 'Неизвестно') ?></p>
                <p><strong>Дата начала:</strong> <?= date('d.m.Y', strtotime($req['data_nach'])) ?></p>

                <?php if ($req['review_id']): ?>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                        <p><strong>Ваш отзыв:</strong> <?= htmlspecialchars($req['comment']) ?></p>
                    </div>
                <?php elseif ((int)$req['status_id'] === 3): ?>
                    <div style="margin-top: 10px;">
                        <form action="review.php?request_id=<?=$req['id_request']?>" method="post" class="inline-review"><?= csrf() ?>
                            <input type="hidden" name="p" value="<?=$currentPage?>">
                            <label for="review-<?=$req['id_request']?>">Оставить отзыв</label>
                            <textarea name="comment" id="review-<?=$req['id_request']?>" rows="3" maxlength="3000" required></textarea>
                            <button type="submit" class="btn btn-primary">Сохранить отзыв</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php if ($totalPages > 1): ?>
<nav class="pagination application-pagination" aria-label="Страницы моих заявок">
    <?php if ($currentPage > 1): ?><a class="page-btn" href="?p=<?=$currentPage-1?>" aria-label="Предыдущие заявки">←</a><?php else: ?><span class="page-btn disabled" aria-disabled="true">←</span><?php endif ?>
    <span class="page-position">Страница <?=$currentPage?> из <?=$totalPages?></span>
    <?php if ($currentPage < $totalPages): ?><a class="page-btn" href="?p=<?=$currentPage+1?>" aria-label="Следующие заявки">→</a><?php else: ?><span class="page-btn disabled" aria-disabled="true">→</span><?php endif ?>
</nav>
<?php endif ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/helpers/layout.php';
