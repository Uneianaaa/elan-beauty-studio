// Запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initCursorEffects('a, button, .service-booking-card, .specialist-booking-card, .date-card, .time-slot-card');
    initScrollAnimations('.service-booking-card, .specialist-booking-card, .date-card, .time-slot-card');
    initScrollPositionSave('.datetime-form', 'a[href*="step="]');
});