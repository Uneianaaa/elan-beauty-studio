// Запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initCursorEffects('a, button, .specialist-card-mini, .similar-card, .btn-primary');
    initScrollAnimations('.specialist-card-mini, .similar-card, .service-content');
    setMinDate();
    
    // Валидация формы быстрой записи
    const quickForm = document.querySelector('.quick-booking');
    if (quickForm) {
        quickForm.addEventListener('submit', (e) => {
            const date = quickForm.querySelector('input[name="date"]').value;
            if (!date) {
                e.preventDefault();
                showNotification('Пожалуйста, выберите дату', 'error');
            }
        });
    }
});