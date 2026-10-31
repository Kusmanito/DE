<?php
session_start();
$pageTitle = 'Главная — Корочки.Есть';
$breadcrumbs = [['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php']];

ob_start();
?>

<div class="hero-text">
    <h1>Добро пожаловать на портал «Корочки.Есть»!</h1>
</div>

<div class="slider-wrapper">
    <button type="button" class="slider-btn slider-btn-prev" onclick="moveSlide(-1)">&#10094;</button>
    <div class="slider">
        <div class="slide active"><img src="/DE/korochki/media/image01.webp" alt="Обучение 1"></div>
        <div class="slide"><img src="/DE/korochki/media/image03.png" alt="Обучение 2"></div>
        <div class="slide"><img src="/DE/korochki/media/image04.jpg" alt="Обучение 3"></div>
        <div class="slide"><img src="/DE/korochki/media/image06.jpg" alt="Обучение 4"></div>
    </div>
    <button type="button" class="slider-btn slider-btn-next" onclick="moveSlide(1)">&#10095;</button>
    <div class="slider-dots">
        <span class="dot active" onclick="currentSlide(0)"></span>
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>
</div>

<div class="hero-text" style="margin-top: 20px;">
    <p>У нас вы найдёте широкий выбор курсов по программированию, веб-дизайну, проектированию баз данных и другим направлениям. Получите официальный документ об образовании, не выходя из дома!</p>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/DE/korochki/pages/new.php" class="btn btn-primary" style="width: auto; padding: 12px 30px; font-size: 16px;">Подать заявку на курс</a>
    <?php else: ?>
        <a href="/DE/korochki/pages/register.php" class="btn btn-primary" style="width: auto; padding: 12px 30px; font-size: 16px;">Зарегистрироваться</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';