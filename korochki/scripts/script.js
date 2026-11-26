document.addEventListener('DOMContentLoaded',()=>{
 const slides=[...document.querySelectorAll('.slide')],dots=[...document.querySelectorAll('.dot')];let index=0,timer,paused=matchMedia('(prefers-reduced-motion: reduce)').matches;
 function show(n){if(!slides.length)return;index=(n+slides.length)%slides.length;slides.forEach((s,i)=>{s.classList.toggle('active',i===index);s.setAttribute('aria-hidden',i===index?'false':'true')});dots.forEach((d,i)=>{d.classList.toggle('active',i===index);d.setAttribute('aria-pressed',i===index?'true':'false')})}
 function reset(){clearInterval(timer);if(slides.length&&!paused&&!document.hidden)timer=setInterval(()=>show(index+1),3000)}
 window.moveSlide=step=>{show(index+step);reset()};window.currentSlide=n=>{show(n);reset()};document.addEventListener('visibilitychange',reset);
 const pause=document.querySelector('#slider-pause');if(pause){function text(){pause.textContent=paused?'Продолжить':'Пауза';pause.setAttribute('aria-pressed',paused?'true':'false')}pause.addEventListener('click',()=>{paused=!paused;text();reset()});text()}show(0);reset();

});

// Маски сохраняют позицию курсора и позволяют редактировать цифры в середине.
document.addEventListener('DOMContentLoaded', () => {
 function mask(input, limit, format, changed = () => {}) {
  if (!input) return;
  const digits = value => value.replace(/\D/g, '');
  function update() {
   const before = digits(input.value.slice(0, input.selectionStart ?? input.value.length)).length;
   const value = digits(input.value).slice(0, limit);
   input.value = format(value);
   let caret = 0, seen = 0;
   while (caret < input.value.length && seen < before) {
    if (/\d/.test(input.value[caret])) seen++;
    caret++;
   }
   while (caret < input.value.length && /\D/.test(input.value[caret])) caret++;
   input.setSelectionRange(caret, caret);
   changed(input.value);
  }
  input.addEventListener('input', update);
  input.addEventListener('keydown', event => {
   if (input.selectionStart !== input.selectionEnd) return;
   let caret = input.selectionStart;
   if (event.key === 'Backspace') {
    while (caret > 0 && /\D/.test(input.value[caret - 1])) caret--;
   } else if (event.key === 'Delete') {
    while (caret < input.value.length && /\D/.test(input.value[caret])) caret++;
   } else return;
   input.setSelectionRange(caret, caret);
  });
  if (input.value) update();
 }
 const phone = document.querySelector('#phone');
 mask(phone, 12, d => {
  if (!d) return '';
  if (d.length === 12) return '+' + d;
  let value = d[0] === '7' ? '+7' : d[0];
  if (d.length > 1) value += '(' + d.slice(1, 4);
  if (d.length >= 4) value += ')';
  if (d.length > 4) value += d.slice(4, 7);
  if (d.length >= 7) value += '-';
  if (d.length > 7) value += d.slice(7, 9);
  if (d.length >= 9) value += '-';
  if (d.length > 9) value += d.slice(9, 11);
  return value;
 });
 const date = document.querySelector('#date');
 const calendar = document.querySelector('#date-calendar');
 const button = document.querySelector('#open-calendar');
 mask(date, 8, d => {
  let value = d.slice(0, 2);
  if (d.length >= 2) value += '.';
  if (d.length > 2) value += d.slice(2, 4);
  if (d.length >= 4) value += '.';
  if (d.length > 4) value += d.slice(4, 8);
  return value;
 }, value => {
  if (!calendar) return;
  const parts = /^(\d{2})\.(\d{2})\.(\d{4})$/.exec(value);
  calendar.value = parts ? `${parts[3]}-${parts[2]}-${parts[1]}` : '';
 });
 if (date && calendar && button) {
  calendar.addEventListener('change', () => {
   if (!calendar.value) { date.value = ''; return; }
   const [year, month, day] = calendar.value.split('-');
   date.value = `${day}.${month}.${year}`;
   date.dispatchEvent(new Event('change', {bubbles: true}));
  });
  button.addEventListener('click', () => {
   if (typeof calendar.showPicker === 'function') {
    try { calendar.showPicker(); return; } catch (_) { /* Обычное поле для старых браузеров. */ }
   }
   calendar.classList.add('calendar-fallback');
   calendar.removeAttribute('tabindex');
   calendar.focus();
  });
 }
});

const statusPopup = document.querySelector('.status-popup');
if (statusPopup) setTimeout(() => statusPopup.remove(), 7000);

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
