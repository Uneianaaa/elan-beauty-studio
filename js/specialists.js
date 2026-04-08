// Запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initCursorEffects('a, .specialist-card, .filter-btn, .advantage, .step');
    initScrollAnimations('.specialist-card, .advantage, .step');
    initStatsAnimation('.stat-value');
    
    // Фильтрация по специализациям
    const filterBtns = document.querySelectorAll('.filter-btn');
    const specialistCards = document.querySelectorAll('.specialist-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filter = btn.dataset.filter;
            
            specialistCards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    const specializations = card.dataset.specializations.toLowerCase();
                    if (specializations.includes(filter.toLowerCase())) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(30px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                }
            });
            
            const specialistsGrid = document.querySelector('.specialists-page');
            specialistsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});