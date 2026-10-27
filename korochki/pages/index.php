<?php
$pageTitle = 'Главная — Корочки.Есть';
$breadcrumbs = [['title' => 'Главная', 'url' => '/DE/korochki/pages/index.php']];
$navButtons = [
    ['title' => 'Вход', 'url' => '/DE/korochki/pages/auth.php'],
    ['title' => 'Регистрация', 'url' => '/DE/korochki/pages/register.php']
];

ob_start();
?>

<div class="hero-text">
    <h1>Добро пожаловать на портал «Корочки.Есть»!</h1>
</div>

<!-- Слайдер -->
<div class="slider-wrapper">
    <button type="button" class="slider-btn slider-btn-prev" onclick="moveSlide(-1)">&#10094;</button>
    
    <div class="slider">
        <div class="slide active">
            <img src="/DE/korochki/media/image01.webp" alt="Обучение 1">
        </div>
        <div class="slide">
            <img src="/DE/korochki/media/image03.png" alt="Обучение 2">
        </div>
        <div class="slide">
            <img src="/DE/korochki/media/image04.jpg" alt="Обучение 3">
        </div>
        <div class="slide">
            <img src="/DE/korochki/media/image06.jpg" alt="Обучение 4">
        </div>
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
    <a href="/DE/korochki/pages/register.php" class="btn btn-primary" style="width: auto; padding: 12px 30px; font-size: 16px;">Зарегистрироваться</a>
</div>

<!-- Скрипт слайдера прямо здесь, чтобы точно сработал -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0) return;

    let slideIndex = 0;
    let slideInterval;

    function showSlide(n) {
        if (n >= slides.length) slideIndex = 0;
        if (n < 0) slideIndex = slides.length - 1;

        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        slides[slideIndex].classList.add('active');
        if (dots[slideIndex]) dots[slideIndex].classList.add('active');
    }

    // Делаем функции глобальными, чтобы их видели onclick
    window.moveSlide = function(step) {
        slideIndex += step;
        showSlide(slideIndex);
        resetInterval();
    };

    window.currentSlide = function(n) {
        slideIndex = n;
        showSlide(slideIndex);
        resetInterval();
    };

    function autoPlay() {
        slideIndex++;
        showSlide(slideIndex);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        slideInterval = setInterval(autoPlay, 5000);
    }

    showSlide(slideIndex);
    resetInterval();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';