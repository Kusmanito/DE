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