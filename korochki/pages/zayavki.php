<?php
$pageTitle = 'Просмотр заявок — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Просмотр заявок', 'url' => '#']
];
$navButtons = [['title' => 'Выход', 'url' => '/DE/korochki/pages/auth.php']];

ob_start();
?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <a href="/DE/korochki/pages/new.php" class="btn btn-primary" style="width: auto;">Создать новую заявку</a>
    <div>
        <select style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            <option>Все статусы</option>
            <option>Новая</option>
            <option>Идёт обучение</option>
            <option>Обучение завершено</option>
        </select>
    </div>
</div>

<div class="requests-grid">
    <?php for($i=1; $i<=3; $i++): ?>
    <div class="request-card">
        <div class="request-card-header">Заявка <?= $i ?></div>
        <div class="request-card-body">
            <p><strong>Наименование курса:</strong> Основы веб-дизайна</p>
            <p><strong>Статус:</strong> Новая</p>
            <p><strong>Способ оплаты:</strong> Наличными</p>
            <p><strong>Дата начала:</strong> 01.11.2026</p>
            <div style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">
                <a href="#" style="color: #b04f9e; text-decoration: none; font-size: 14px;">Оставить отзыв</a><br>
                <a href="#" style="color: #b04f9e; text-decoration: none; font-size: 14px;">Оставить звёзды</a>
            </div>
        </div>
    </div>
    <?php endfor; ?>
</div>

<div class="pagination">
    <a href="#" class="page-btn">&lt;</a>
    <a href="#" class="page-btn active">1</a>
    <a href="#" class="page-btn">2</a>
    <a href="#" class="page-btn">3</a>
    <a href="#" class="page-btn">&gt;</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';