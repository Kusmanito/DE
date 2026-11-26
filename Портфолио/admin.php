<?php
require __DIR__ . '/src/bootstrap.php';
requireRole('admin');
$title = 'Панель куратора';
$type = intValue($_GET['type'] ?? 0);
$status = intValue($_GET['status'] ?? 0);
$status = isset(STATUSES[$status]) ? $status : 0;
$page = intValue($_GET['page'] ?? 1);
$pageUrl = static fn(int $number): string => 'admin.php?' . http_build_query(array_filter([
    'type' => $type, 'status' => $status, 'page' => $number,
]));
if (verifyPost()) {
    $id = intValue(post('id'));
    $decision = intValue(post('decision'));
    if (!in_array($decision, [2, 3], true)) {
        notice('Выберите результат проверки.', 'error');
    } elseif (repository()->changeStatus($id, $decision)) {
        notice('Статус достижения сохранён.');
    } else {
        notice('Запись не найдена.', 'error');
    }
    redirect($pageUrl(max(1, $page)));
}
$types = repository()->types();
[$rows, $page, $pages, $total] = repository()->achievements(null, $type, $status, $page);
require __DIR__ . '/src/header.php';
?>
<div class="page-heading"><h1>Панель куратора</h1></div>
<form method="get" class="panel filter-bar">
    <div class="field">
        <label for="type">Тип достижения</label>
        <select id="type" name="type">
            <option value="">Все типы</option>
            <?php foreach ($types as $item): ?>
                <option value="<?= h($item['id']) ?>"<?= (int)$item['id'] === $type ? ' selected' : '' ?>><?= h($item['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="status">Статус</label>
        <select id="status" name="status">
            <option value="">Все статусы</option>
            <?php foreach (STATUSES as $id => $label): ?>
                <option value="<?= $id ?>"<?= $status === $id ? ' selected' : '' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="action secondary" type="submit">Применить</button>
    <?php if ($type || $status): ?><a class="text-link" href="admin.php">Сбросить</a><?php endif; ?>
</form>
<p class="result-count">Достижений найдено: <?= h($total) ?></p>
<?php if (!$rows): ?><p class="panel empty">Достижений по выбранным условиям нет.</p><?php endif; ?>
<div class="request-grid">
    <?php foreach ($rows as $row): ?>
        <article class="panel application">
            <?php require __DIR__ . '/src/card.php'; ?>
            <form method="post" class="card-form">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= h($row['id']) ?>">
                <div class="field">
                    <label for="decision-<?= h($row['id']) ?>">Результат проверки</label>
                    <select id="decision-<?= h($row['id']) ?>" name="decision" required>
                        <option value="">Выберите статус</option>
                        <?php foreach ([2, 3] as $id): ?>
                            <option value="<?= $id ?>"<?= (int)$row['status_id'] === $id ? ' selected' : '' ?>><?= h(STATUSES[$id]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="action secondary" type="submit">Сохранить статус</button>
            </form>
        </article>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/src/pagination.php'; ?>
<?php require __DIR__ . '/src/footer.php'; ?>
