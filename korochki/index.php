<?php
require_once __DIR__.'/helpers/security.php';
$pageTitle = 'Главная — Корочки.Есть';
$breadcrumbs = [['title' => 'Главная', 'url' => 'index.php']];

ob_start();
?>

<div class="hero-text">
    <h1>Добро пожаловать на портал «Корочки.Есть»!</h1>
</div>

<div class="slider-wrapper">
    <button type="button" aria-label="Предыдущий слайд" class="slider-btn slider-btn-prev" onclick="moveSlide(-1)">&#10094;</button>
    <div class="slider">
        <div class="slide active"><img src="media/image01.webp" alt="Обучение в сфере информационных технологий"></div>
        <div class="slide"><img src="media/image03.png" alt="Онлайн-образование"></div>
        <div class="slide"><img src="media/image04.jpg" alt="Освоение новых навыков"></div>
        <div class="slide"><img src="media/image06.jpg" alt="Дополнительное профессиональное образование"></div>
    </div>
    <button type="button" aria-label="Следующий слайд" class="slider-btn slider-btn-next" onclick="moveSlide(1)">&#10095;</button>
    <div class="slider-dots">
        <button type="button" class="dot active" aria-label="Слайд 1" onclick="currentSlide(0)"></button>
        <button type="button" class="dot" aria-label="Слайд 2" onclick="currentSlide(1)"></button>
        <button type="button" class="dot" aria-label="Слайд 3" onclick="currentSlide(2)"></button>
        <button type="button" class="dot" aria-label="Слайд 4" onclick="currentSlide(3)"></button>
    </div><button type="button" id="slider-pause" class="btn-link slider-pause">Пауза</button>
</div>

<div class="hero-text" style="margin-top: 20px;">
    <p>Выберите курс по программированию, веб-дизайну или проектированию баз данных и подайте заявку на обучение по программе дополнительного профессионального образования.</p>
    <?php if ((int)($_SESSION['user_role']??0)===1): ?><a href="admin.php" class="btn btn-primary" style="width:auto">Панель администратора</a><?php elseif (isset($_SESSION['user_id'])): ?>
        <a href="add_request.php" class="btn btn-primary" style="width: auto; padding: 12px 30px; font-size: 16px;">Подать заявку на курс</a>
    <?php else: ?>
        <a href="register.php" class="btn btn-primary" style="width: auto; padding: 12px 30px; font-size: 16px;">Зарегистрироваться</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/helpers/layout.php';
