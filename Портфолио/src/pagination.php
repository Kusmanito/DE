<?php if ($pages > 1): ?>
    <nav class="pagination-nav" aria-label="Страницы заявок">
        <?php if ($page > 1): ?><a href="<?= h($pageUrl($page - 1)) ?>">← Назад</a><?php endif; ?>
        <span>Страница <?= h($page) ?> из <?= h($pages) ?></span>
        <?php if ($page < $pages): ?><a href="<?= h($pageUrl($page + 1)) ?>">Далее →</a><?php endif; ?>
    </nav>
<?php endif; ?>
