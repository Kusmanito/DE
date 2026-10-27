<?php
$pageTitle = 'Подача заявки — Корочки.Есть';
$breadcrumbs = [
    ['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php'],
    ['title' => 'Просмотр заявок', 'url' => '/DE/korochki/pages/zayavki.php'],
    ['title' => 'Подача заявки', 'url' => '#']
];
$navButtons = [['title' => 'Выход', 'url' => '/DE/korochki/pages/auth.php']];

ob_start();
?>
<div class="form-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Подача заявки</h2>
    <form action="#" method="POST">
        <div class="form-group">
            <label>Наименование курса</label>
            <select name="course">
                <option>Основы алгоритмизации и программирования</option>
                <option>Основы веб-дизайна</option>
                <option>Основы проектирования баз данных</option>
            </select>
        </div>
        <div class="form-group">
            <label>Дата начала обучения</label>
            <input type="date" name="date" required>
        </div>
        <div class="form-group">
            <label>Способ оплаты</label>
            <select name="payment">
                <option>Наличными</option>
                <option>Переводом</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Подать заявку</button>
            <a href="/DE/korochki/pages/zayavki.php" class="btn-link">Заявка уже создана? Посмотреть</a>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';