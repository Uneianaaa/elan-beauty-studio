// Запуск при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initCursorEffects('a, button, .service-item, .time-slot, .date, .specialist-mini-card, .review-card, .btn-primary, .btn-secondary');
    initScrollAnimations('.service-item, .review-card, .specialist-mini-card, .booking-container');
    initSmoothScroll();
    initStatsAnimation('.stat-value');
    
    let selectedDate = null;
    let selectedTime = null;
    
    const dates = document.querySelectorAll('.date');
    const timeSlots = document.querySelectorAll('.time-slot');
    
    if (dates.length) {
        dates.forEach(date => {
            date.addEventListener('click', () => {
                dates.forEach(d => d.classList.remove('active'));
                date.classList.add('active');
                selectedDate = date.textContent;
                loadTimeSlots(selectedDate);
            });
        });
    }
    
    function loadTimeSlots(date) {
        const timeSlots = document.querySelectorAll('.time-slot');
        
        timeSlots.forEach(slot => {
            slot.style.opacity = '0.5';
            slot.style.pointerEvents = 'none';
        });
        
        setTimeout(() => {
            timeSlots.forEach(slot => {
                slot.style.opacity = '1';
                slot.style.pointerEvents = 'auto';
                
                if (Math.random() > 0.7) {
                    slot.classList.add('booked');
                    slot.style.opacity = '0.3';
                    slot.style.pointerEvents = 'none';
                } else {
                    slot.classList.remove('booked');
                }
            });
            
            showNotification(`Доступное время на ${date} число загружено`);
        }, 500);
    }
    
    if (timeSlots.length) {
        timeSlots.forEach(slot => {
            slot.addEventListener('click', () => {
                if (slot.classList.contains('booked')) return;
                
                timeSlots.forEach(s => s.classList.remove('selected'));
                slot.classList.add('selected');
                selectedTime = slot.textContent;
            });
        });
    }
    
    // Сохранение в localStorage
    window.addEventListener('beforeunload', () => {
        if (selectedDate) localStorage.setItem('selectedDate', selectedDate);
        if (selectedTime) localStorage.setItem('selectedTime', selectedTime);
    });
    
    // Восстановление из localStorage
    const savedDate = localStorage.getItem('selectedDate');
    const savedTime = localStorage.getItem('selectedTime');
    
    if (savedDate && dates.length) {
        dates.forEach(date => {
            if (date.textContent === savedDate) {
                date.classList.add('active');
                selectedDate = savedDate;
            }
        });
    }
    
    if (savedTime && timeSlots.length) {
        timeSlots.forEach(slot => {
            if (slot.textContent === savedTime && !slot.classList.contains('booked')) {
                slot.classList.add('selected');
                selectedTime = savedTime;
            }
        });
    }
});

// Склонение слова "отзыв"
function declineReviews(count) {
    const num = Math.abs(count);
    const lastDigit = num % 10;
    const lastTwoDigits = num % 100;
    
    if (lastTwoDigits >= 11 && lastTwoDigits <= 19) {
        return num + ' отзывов';
    }
    
    if (lastDigit === 1) {
        return num + ' отзыв';
    }
    
    if (lastDigit >= 2 && lastDigit <= 4) {
        return num + ' отзыва';
    }
    
    return num + ' отзывов';
}

// Применяем склонение при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const reviewsCount = document.getElementById('reviews-count');
    const reviewsText = document.getElementById('reviews-text');
    
    if (reviewsCount && reviewsText) {
        const count = parseInt(reviewsCount.textContent);
        const declined = declineReviews(count);
        const parts = declined.split(' ');
        reviewsCount.textContent = parts[0];
        reviewsText.textContent = parts[1];
    }
});

// Склонение имени специалиста на странице
function initNameDeclension() {
    const bookingTitle = document.querySelector('.booking-title');
    const dativeSpan = document.querySelector('.specialist-dative');
    const modalDativeSpan = document.querySelector('.specialist-dative-modal');
    
    if (bookingTitle && dativeSpan) {
        const fullName = bookingTitle.dataset.name;
        if (fullName) {
            dativeSpan.textContent = getNameInDative(fullName);
        }
    }
    
    if (modalDativeSpan) {
        // Берём имя из заголовка страницы или из data-атрибута
        const specialistNameElem = document.querySelector('.profile-name');
        if (specialistNameElem) {
            const fullName = specialistNameElem.textContent;
            modalDativeSpan.textContent = getNameInDative(fullName);
        }
    }
}

// Запускаем склонение после загрузки страницы
document.addEventListener('DOMContentLoaded', () => {
    initNameDeclension();
});