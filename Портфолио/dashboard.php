<?php
require __DIR__ . '/src/bootstrap.php';
$student = requireRole('student');
$title = 'Мои достижения';
$page = intValue($_GET['page'] ?? 1);
$pageUrl = static fn(int $number): string => 'dashboard.php?page=' . $number;
if (verifyPost()) {
    $id = intValue(post('id'));
    $comment = trim(post('comment'));
    if ($comment === '' || !mb_check_encoding($comment) || mb_strlen($comment) > 2000) {
        notice('Введите отзыв длиной до 2000 символов.', 'error');
    } elseif (repository()->addReview($id, $student['id'], $comment)) {
        notice('Отзыв сохранён.');
    } else {
        notice('Отзыв можно оставить один раз для своего подтверждённого достижения.', 'error');
    }
    redirect($pageUrl(max(1, $page)) . '#achievement-' . $id);
}
[$rows, $page, $pages] = repository()->achievements($student['id'], 0, 0, $page);
require __DIR__ . '/src/header.php';
?>
<div class="page-heading">
    <h1>Мои достижения</h1>
    <a class="action" href="add_request.php">Добавить достижение</a>
</div>
<?php if (!$rows): ?><p class="panel empty">У вас пока нет записей о достижениях.</p><?php endif; ?>
<div class="request-grid">
    <?php foreach ($rows as $row): ?>
        <article class="panel application" id="achievement-<?= h($row['id']) ?>">
            <?php require __DIR__ . '/src/card.php'; ?>
            <?php if ((int)$row['status_id'] === 2 && $row['comment'] === null): ?>
                <form method="post" class="card-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= h($row['id']) ?>">
                    <div class="field">
                        <label for="review-<?= h($row['id']) ?>">Отзыв о мероприятии</label>
                        <textarea id="review-<?= h($row['id']) ?>" name="comment" rows="3" required maxlength="2000"></textarea>
                    </div>
                    <button class="action secondary" type="submit">Сохранить отзыв</button>
                </form>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/src/pagination.php'; ?>
<?php require __DIR__ . '/src/footer.php'; ?>
