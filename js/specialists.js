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
    // Функция склонения слова "отзыв"
    function getReviewWord(count) {
        count = count % 100;
        if (count >= 11 && count <= 19) return 'отзывов';
        const last = count % 10;
        if (last === 1) return 'отзыв';
        if (last >= 2 && last <= 4) return 'отзыва';
        return 'отзывов';
    }

    // Если нужно обновить текст на странице динамически
    document.querySelectorAll('.specialist-card').forEach(card => {
        const reviewsCount = parseInt(card.dataset.reviewsCount);
        const reviewsLabel = card.querySelector('.reviews-label');
        if (reviewsLabel && !isNaN(reviewsCount)) {
            reviewsLabel.textContent = reviewsCount + ' ' + getReviewWord(reviewsCount);
        }
    });
});