<?php
// Подключаем конфиг с БД
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/contacts.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>

    <div class="cursor"></div>
    
    <header>
        <div class="logo">ÉLAN</div>
        <nav>
            <a href="index.php">Главная</a>
            <a href="services.php">Услуги</a>
            <a href="specialists.php">Специалисты</a>
            <a href="promotions.php">Акции</a>
            <a href="contacts.php" class="active">Контакты</a>
        </nav>
    </header>

    <!-- Шапка страницы -->
    <section class="page-header">
        <h1>Наши <span>контакты</span></h1>
        <p>Всегда рады видеть вас в нашем салоне</p>
    </section>

    <!-- Блок с контактной информацией -->
    <section class="contacts-info">
        <div class="container">
            <div class="contacts-grid">
                <!-- Карточка с адресом -->
                <div class="contact-card">
                    <div class="contact-icon"></div>
                    <h3>Адрес</h3>
                    <p>г. Ярославль, ул. Кирова, 15</p>
                    <p class="contact-note">Центральный район, вход с улицы</p>
                </div>

                <!-- Карточка с телефоном -->
                <div class="contact-card">
                    <div class="contact-icon"></div>
                    <h3>Телефон</h3>
                    <p><a href="tel:+79001234567">+7 (900) 123-45-67</a></p>
                    <p class="contact-note">Ежедневно с 10:00 до 21:00</p>
                </div>

                <!-- Карточка с email -->
                <div class="contact-card">
                    <div class="contact-icon"></div>
                    <h3>Email</h3>
                    <p><a href="mailto:info@elanbeauty.ru">info@elanbeauty.ru</a></p>
                    <p class="contact-note">Ответим в течение часа</p>
                </div>

                <!-- Карточка с режимом работы -->
                <div class="contact-card">
                    <div class="contact-icon"></div>
                    <h3>Режим работы</h3>
                    <p>Пн-Вс: 10:00 – 21:00</p>
                    <p class="contact-note">Без выходных</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Блок с социальными сетями -->
    <section class="social-section">
        <div class="container">
            <h2>Мы в соцсетях</h2>
            <div class="social-grid">
                <a href="#" class="social-card">
                    <div class="social-icon"></div>
                    <span class="social-name">VKontakte</span>
                    <span class="social-username">vk.com/elanbeauty</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Блок с формой обратной связи -->
    <section class="feedback-section">
        <div class="container">
            <div class="feedback-box">
                <h2>Остались вопросы?</h2>
                <p>Напишите нам, и мы ответим в ближайшее время</p>
                <!-- Форма с имитацией отправки -->
                <form class="feedback-form" onsubmit="event.preventDefault(); showNotification('Сообщение отправлено! Мы скоро свяжемся с вами', 'success'); this.reset();">
                    <div class="form-row">
                        <input type="text" placeholder="Ваше имя" required>
                        <input type="tel" placeholder="Телефон" required>
                    </div>
                    <textarea rows="4" placeholder="Ваше сообщение" required></textarea>
                    <button type="submit" class="btn-primary">Отправить сообщение</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div>
            <h3>ÉLAN Beauty</h3>
            <p>г. Ярославль, ул. Кирова, 15</p>
        </div>
        <div>
            <p>+7 (900) 123-45-67</p>
            <p>10:00 – 21:00</p>
            <p>Ежедневно</p>
        </div>
        <div class="copyright">
            <p>© 2026 ÉLAN Beauty Studio. Все права защищены</p>
        </div>
    </footer>

    <script src="js/contacts.js"></script>
    <script src="js/common.js"></script>
</body>
</html>