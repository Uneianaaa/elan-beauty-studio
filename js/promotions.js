document.addEventListener('DOMContentLoaded', () => {
    // Эффект курсора
    initCursorEffects('a, button, .promo-card, .step-card, .upcoming-card');
    
    // Анимация
    initScrollAnimations('.promo-card, .step-card, .upcoming-card, .terms-box');
    
    // Функция для записи по акции
    function openBooking(promoId) {
        showNotification('Перенаправление на страницу записи...', 'info');
        // setTimeout(() => {
        //     window.location.href = `booking.php?promo_id=${promoId}`;
        // }, 1000);
    }
    
    // Подписка на новости
    const subscribeForm = document.querySelector('.subscribe-form');
    if (subscribeForm) {
        subscribeForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = subscribeForm.querySelector('input[type="email"]').value;
            showNotification('Спасибо за подписку!', 'success');
            subscribeForm.reset();
        });
    }
});