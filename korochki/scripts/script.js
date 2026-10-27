document.addEventListener('DOMContentLoaded', function() {
    // Ищем слайдер
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    // Если слайдера на странице нет — выходим
    if (slides.length === 0) return;

    let slideIndex = 0;
    let slideInterval;

    // Функция показа слайда
    function showSlide(n) {
        // Зацикливание
        if (n >= slides.length) slideIndex = 0;
        if (n < 0) slideIndex = slides.length - 1;

        // Скрываем все слайды и точки
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Показываем нужный
        slides[slideIndex].classList.add('active');
        if (dots[slideIndex]) dots[slideIndex].classList.add('active');
    }

    // Функция для кнопок (вызывается из HTML через onclick)
    window.moveSlide = function(step) {
        slideIndex += step;
        showSlide(slideIndex);
        resetInterval();
    };

    // Функция для точек (вызывается из HTML через onclick)
    window.currentSlide = function(n) {
        slideIndex = n;
        showSlide(slideIndex);
        resetInterval();
    };

    // Автопрокрутка
    function autoPlay() {
        slideIndex++;
        showSlide(slideIndex);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        slideInterval = setInterval(autoPlay, 5000); // 5 секунд
    }

    // Инициализация
    showSlide(slideIndex);
    resetInterval();
});