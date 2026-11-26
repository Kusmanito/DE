<?php
require __DIR__ . '/src/bootstrap.php';
$title = 'Главная';
require __DIR__ . '/src/header.php';
$slides = ['Подготовка к конкурсу профмастерства', 'Работа команды волонтёров', 'Студенческое спортивное соревнование', 'Выступление на научной конференции'];
?>
<section class="home-hero">
    <div class="hero-copy">
        <h1>Студенческое портфолио</h1>
        <p>Собирайте достижения, прикладывайте сертификаты и следите за подтверждением у куратора.</p>
        <?php if (($current['role'] ?? '') === 'admin'): ?>
            <a class="action" href="admin.php">Открыть панель куратора</a>
        <?php elseif ($current): ?>
            <a class="action" href="add_request.php">Добавить достижение</a>
        <?php else: ?>
            <a class="action" href="login.php">Войти в портфолио</a>
            <a class="text-link" href="register.php">Зарегистрироваться</a>
        <?php endif; ?>
    </div>
    <div class="gallery" data-carousel role="region" aria-label="Фотографии студенческих мероприятий">
        <div class="gallery-window">
            <?php foreach ($slides as $index => $description): ?>
                <img width="800" height="540" src="assets/slides/slide-<?= $index + 1 ?>.webp" alt="<?= h($description) ?>"<?= $index ? ' hidden' : '' ?>>
            <?php endforeach; ?>
        </div>
        <div class="gallery-controls" hidden>
            <button type="button" data-prev aria-label="Предыдущая фотография">←</button>
            <span data-position>1 / 4</span>
            <button type="button" data-next aria-label="Следующая фотография">→</button>
            <button type="button" data-pause>Пауза</button>
        </div>
    </div>
</section>
<?php require __DIR__ . '/src/footer.php'; ?>
