const carousel = document.querySelector('[data-carousel]');
if (carousel) {
    const slides = Array.from(carousel.querySelectorAll('img'));
    const controls = carousel.querySelector('.gallery-controls');
    const counter = carousel.querySelector('[data-position]');
    const pause = carousel.querySelector('[data-pause]');
    let index = 0;
    let timer;
    let paused = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function show(offset) {
        slides[index].hidden = true;
        index = (index + offset + slides.length) % slides.length;
        slides[index].hidden = false;
        counter.textContent = (index + 1) + ' / ' + slides.length;
    }

    function updateTimer() {
        window.clearInterval(timer);
        pause.textContent = paused ? 'Продолжить' : 'Пауза';
        if (!paused && !document.hidden) {
            timer = window.setInterval(() => show(1), 5000);
        }
    }

    function navigate(offset) {
        show(offset);
        updateTimer();
    }

    carousel.querySelector('[data-prev]').addEventListener('click', () => navigate(-1));
    carousel.querySelector('[data-next]').addEventListener('click', () => navigate(1));
    pause.addEventListener('click', () => {
        paused = !paused;
        updateTimer();
    });
    document.addEventListener('visibilitychange', updateTimer);
    controls.hidden = false;
    updateTimer();
}

document.querySelectorAll('.notice-close').forEach(button => {
    button.hidden = false;
    button.addEventListener('click', () => button.closest('.notice').remove());
});

const menuToggle = document.querySelector('.menu-toggle');
if (menuToggle) {
    menuToggle.hidden = false;
    const menu = document.getElementById(menuToggle.getAttribute('aria-controls'));
    function setMenuOpen(open) {
        menuToggle.setAttribute('aria-expanded', String(open));
    }
    menuToggle.addEventListener('click', () => setMenuOpen(menuToggle.getAttribute('aria-expanded') !== 'true'));
    menu.parentElement.addEventListener('keydown', event => {
        if (event.key === 'Escape' && menuToggle.getAttribute('aria-expanded') === 'true') {
            setMenuOpen(false);
            menuToggle.focus();
        }
    });
}
