<?php
$pageTitle = 'Панель администратора — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Панель Администратора', 'url' => '#']
];
$navButtons = [['title' => 'Выход', 'url' => '/DE/korochki/pages/auth.php']];

ob_start();
?>
<h2 style="margin-bottom: 20px;">Управление заявками</h2>

<div class="requests-grid">
    <?php for($i=1; $i<=3; $i++): ?>
    <div class="request-card">
        <div class="request-card-header">Заявка <?= $i ?></div>
        <div class="request-card-body">
            <p><strong>ФИО пользователя:</strong> Иванов Иван Иванович</p>
            <p><strong>Наименование курса:</strong> Основы веб-дизайна</p>
            <p><strong>Способ оплаты:</strong> Наличными</p>
            <p><strong>Дата начала:</strong> 01.11.2026</p>
            
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                <label style="font-size: 14px; font-weight: bold;">Статус:</label>
                <select class="status-select">
                    <option>Новая</option>
                    <option>Идёт обучение</option>
                    <option>Обучение завершено</option>
                </select>
            </div>
            
            <div style="margin-top: 10px;">
                <a href="#" style="color: #b04f9e; text-decoration: none; font-size: 14px;">Оставить отзыв</a><br>
                <a href="#" style="color: #b04f9e; text-decoration: none; font-size: 14px;">Оставить звёзды</a>
            </div>
            
            <button class="btn btn-primary" style="margin-top: 15px; width: 100%; font-size: 12px;">Сохранить</button>
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