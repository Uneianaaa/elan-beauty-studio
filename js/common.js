// Курсор
const cursor = document.querySelector(".cursor");
const header = document.querySelector("header");

if (cursor) {
    document.addEventListener("mousemove", e => {
        cursor.style.left = e.clientX + "px";
        cursor.style.top = e.clientY + "px";
    });
}

// Функция для добавления эффекта курсора на интерактивные элементы
function initCursorEffects(selectors) {
    if (!cursor) return;
    
    const elements = document.querySelectorAll(selectors);
    elements.forEach(el => {
        el.addEventListener("mouseenter", () => {
            cursor.classList.add("hover");
        });
        el.addEventListener("mouseleave", () => {
            cursor.classList.remove("hover");
        });
    });
}

// Изменение стиля шапки при скролле
if (header) {
    window.addEventListener("scroll", () => {
        if (window.scrollY > 50) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });
}

// Кнопка "Наверх"
function initScrollTop() {
    let scrollTop = document.querySelector('.scroll-top');
    
    if (!scrollTop) {
        scrollTop = document.createElement('button');
        scrollTop.innerHTML = '↑';
        scrollTop.className = 'scroll-top';
        scrollTop.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #c46b7b;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            opacity: 0;
            transition: 0.3s;
            z-index: 100;
            box-shadow: 0 5px 15px rgba(196, 107, 123, 0.3);
            pointer-events: none;
        `;
        document.body.appendChild(scrollTop);
    }
    
    scrollTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            scrollTop.style.opacity = '1';
            scrollTop.style.pointerEvents = 'auto';
        } else {
            scrollTop.style.opacity = '0';
            scrollTop.style.pointerEvents = 'none';
        }
    });
}

// Система уведомлений
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'error' ? '#f44336' : '#c46b7b'};
        color: white;
        border-radius: 10px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Анимация при скролле
function initScrollAnimations(selectors) {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const elements = document.querySelectorAll(selectors);
    elements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s, transform 0.6s';
        observer.observe(el);
    });
}

// Плавный скролл для якорных ссылок
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Сохранение позиции скролла при отправке формы или переходе по ссылкам
function initScrollPositionSave(formSelectors = '.datetime-form', linkSelectors = 'a[href*="step="]') {
    const forms = document.querySelectorAll(formSelectors);
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            localStorage.setItem('scrollPosition', window.scrollY);
        });
    });
    const links = document.querySelectorAll(linkSelectors);
    links.forEach(link => {
        link.addEventListener('click', function() {
            localStorage.setItem('scrollPosition', window.scrollY);
        });
    });
    const savedPosition = localStorage.getItem('scrollPosition');
    if (savedPosition) {
        setTimeout(() => {
            window.scrollTo({
                top: parseInt(savedPosition),
                behavior: 'smooth'
            });
            localStorage.removeItem('scrollPosition');
        }, 100);
    }
}

// Анимация чисел при скролле
function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 10);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.round(current);
    }, 10);
}

function initStatsAnimation(selector = '.stat-value') {
    const statValues = document.querySelectorAll(selector);
    statValues.forEach(stat => {
        const value = parseInt(stat.textContent);
        if (!isNaN(value) && value > 0) {
            const originalValue = value;
            stat.textContent = '0';
            
            const statObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateValue(entry.target, 0, originalValue, 1000);
                        statObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            statObserver.observe(stat);
        }
    });
}

// Добавление базовых стилей для анимаций
function addAnimationStyles() {
    if (!document.querySelector('#animation-styles')) {
        const style = document.createElement('style');
        style.id = 'animation-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Установка минимальной даты для полей типа date
function setMinDate(selector = 'input[type="date"]') {
    const dateInputs = document.querySelectorAll(selector);
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        input.min = today;
    });
}

// Бургер-меню
function initBurgerMenu() {
    const burger = document.querySelector('.burger-menu');
    const mobileNav = document.querySelector('.mobile-nav');
    const overlay = document.querySelector('.overlay');
    
    if (!burger || !mobileNav || !overlay) return;
    
    function toggleMenu() {
        burger.classList.toggle('active');
        mobileNav.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
    }
    
    burger.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
    
    // Закрываем меню при клике на ссылку
    const mobileLinks = mobileNav.querySelectorAll('a');
    mobileLinks.forEach(link => {
        link.addEventListener('click', toggleMenu);
    });
}

// Создаем элементы бургер-меню динамически (если их нет в HTML)
function createBurgerMenuElements() {
    if (document.querySelector('.burger-menu')) return;
    
    const burger = document.createElement('button');
    burger.className = 'burger-menu';
    burger.setAttribute('aria-label', 'Меню');
    burger.innerHTML = '<span></span><span></span><span></span>';
    
    const mobileNav = document.createElement('div');
    mobileNav.className = 'mobile-nav';
    
    // Копируем ссылки из обычного меню
    const desktopNav = document.querySelector('nav');
    if (desktopNav) {
        const links = desktopNav.querySelectorAll('a');
        links.forEach(link => {
            const newLink = document.createElement('a');
            newLink.href = link.href;
            newLink.textContent = link.textContent;
            if (link.classList.contains('active')) {
                newLink.classList.add('active');
            }
            mobileNav.appendChild(newLink);
        });
    }
    
    // Создаем оверлей
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    
    // Добавляем элементы в DOM
    const header = document.querySelector('header');
    if (header) {
        header.appendChild(burger);
        document.body.appendChild(mobileNav);
        document.body.appendChild(overlay);
    }
}

// Функция открытия модального окна с отзывом
function openReviewForm(specialistId) {
    const modal = document.getElementById('reviewFormModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Функция закрытия модального окна с отзывом
function closeReviewModal() {
    const modal = document.getElementById('reviewFormModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Закрытие при клике на крестик
document.addEventListener('DOMContentLoaded', () => {
    const closeBtn = document.querySelector('.modal .close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeReviewModal);
    }
    
    // Закрытие при клике вне окна
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('reviewFormModal');
        if (e.target === modal) {
            closeReviewModal();
        }
    });
});

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    addAnimationStyles();
    createBurgerMenuElements();
    initBurgerMenu();
    initScrollTop();
    if (document.querySelector('.service, .gallery-item, .member, .promo-box')) {
        initScrollAnimations('.service, .gallery-item, .member, .promo-box');
    }
});

