<header class="card-heading">
    <div>
        <h2><?= h($row['event_name']) ?></h2>
        <p><?= h($row['type']) ?> · <time datetime="<?= h($row['event_date']) ?>"><?= h(date('d.m.Y', strtotime($row['event_date']))) ?></time></p>
    </div>
    <span class="status status-<?= h($row['status_id']) ?>"><?= h($row['status']) ?></span>
</header>
<dl class="card-details">
    <?php if ($current['role'] === 'admin'): ?>
        <div><dt>Студент</dt><dd><?= h($row['student']) ?></dd></div>
    <?php endif; ?>
    <div><dt>Уровень</dt><dd><?= h($row['level']) ?></dd></div>
    <div><dt>Подтверждающий документ</dt><dd><a class="text-link" href="download.php?id=<?= h($row['id']) ?>"><?= h($row['original_file']) ?></a></dd></div>
    <?php if ($row['comment'] !== null): ?>
        <div><dt>Отзыв о мероприятии</dt><dd class="preserve-lines"><?= h($row['comment']) ?></dd></div>
    <?php endif; ?>
</dl>
