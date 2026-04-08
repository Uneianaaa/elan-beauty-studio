// Запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initCursorEffects('a, button, .contact-card, .social-card');
    initScrollAnimations('.contact-card, .social-card, .map-container, .feedback-box');
});