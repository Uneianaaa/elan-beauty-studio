// Запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initCursorEffects('a, .service-card, .category-btn');
    initScrollAnimations('.service-card');
    
    // Фильтрация по категориям
    const categoryBtns = document.querySelectorAll('.category-btn');
    const categorySections = document.querySelectorAll('.category-section');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const category = btn.dataset.category;
            
            if (category === 'all') {
                categorySections.forEach(section => {
                    section.classList.remove('hidden');
                });
            } else {
                categorySections.forEach(section => {
                    if (section.dataset.category === category) {
                        section.classList.remove('hidden');
                    } else {
                        section.classList.add('hidden');
                    }
                });
            }
            
            const servicesPage = document.querySelector('.services-page');
            servicesPage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});