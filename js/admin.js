// Курсор
const cursor = document.querySelector(".cursor");
const header = document.querySelector("header");
const interactiveElements = document.querySelectorAll("a, button, .stat-card, .review-item");

document.addEventListener("mousemove", e => {
    cursor.style.left = e.clientX + "px";
    cursor.style.top = e.clientY + "px";
});

interactiveElements.forEach(el => {
    el.addEventListener("mouseenter", () => {
        cursor.classList.add("hover");
    });
    el.addEventListener("mouseleave", () => {
        cursor.classList.remove("hover");
    });
});

// Изменение хедера при скролле
window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

// Автоматическое скрытие уведомлений
const notification = document.querySelector('.notification');
if (notification) {
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Анимация появления элементов при скролле
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

document.querySelectorAll('.stat-card, .review-item, .appointments-table').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s, transform 0.6s';
    observer.observe(el);
});

// Кнопка "Наверх"
const scrollTop = document.createElement('button');
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
`;

document.body.appendChild(scrollTop);

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