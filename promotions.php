<?php
require_once 'config.php';

// Получаем текущие активные акции
$stmt = $pdo->query("
    SELECT * FROM promotions 
    WHERE is_active = TRUE 
    AND valid_from <= CURDATE() 
    AND valid_to >= CURDATE()
    ORDER BY valid_to ASC
");
$promotions = $stmt->fetchAll();

// Получаем предстоящие акции (скоро начнутся)
$stmt = $pdo->query("
    SELECT * FROM promotions 
    WHERE is_active = TRUE 
    AND valid_from > CURDATE()
    ORDER BY valid_from ASC
    LIMIT 3
");
$upcoming = $stmt->fetchAll();

function getDiscountBadge($promotion) {
    switch ($promotion['discount_type']) {
        case 'percentage':
            return '<div class="promo-badge percentage">
                <span class="badge-value">-' . $promotion['discount_value'] . '%</span>
                <span class="badge-label">скидка</span>
            </div>';
        case 'fixed':
            return '<div class="promo-badge fixed">
                <span class="badge-value">-' . number_format($promotion['discount_value'], 0, '', ' ') . ' ₽</span>
                <span class="badge-label">скидка</span>
            </div>';
        case 'gift':
            return '<div class="promo-badge gift">
                <span class="badge-label">подарок</span>
            </div>';
        default:
            return '';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Акции и скидки | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/promotions.css">
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
            <a href="promotions.php" class="active">Акции</a>
            <a href="contacts.php">Контакты</a>
        </nav>
    </header>

    <section class="page-header">
        <h1>Акции и <span>скидки</span></h1>
        <p>Специальные предложения для вашей красоты</p>
    </section>

    <!-- Активные акции -->
    <section class="promotions-section">
        <div class="container">
            <h2>Действующие акции</h2>
            
            <?php if (empty($promotions)): ?>
                <p class="no-promotions">Сейчас нет активных акций. Загляните позже!</p>
            <?php else: ?>
                <div class="promotions-grid">
                    <?php foreach ($promotions as $promo): ?>
                        <div class="promo-card">
                            <?= getDiscountBadge($promo) ?>
                            <div class="promo-image"></div>
                            <div class="promo-content">
                                <h3><?= htmlspecialchars($promo['title']) ?></h3>
                                
                                <?php if ($promo['short_description']): ?>
                                    <div class="promo-tags">
                                        <span class="promo-tag"><?= htmlspecialchars($promo['short_description']) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <p class="promo-description"><?= htmlspecialchars($promo['description']) ?></p>
                                
                                <div class="promo-dates">
                                    <?= date('d.m', strtotime($promo['valid_from'])) ?> - <?= date('d.m.Y', strtotime($promo['valid_to'])) ?>
                                </div>
                                
                                <?php if ($promo['conditions']): ?>
                                    <div class="promo-conditions">
                                        <strong>Условия</strong>
                                        <?= htmlspecialchars($promo['conditions']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Скоро начнутся -->
    <?php if (!empty($upcoming)): ?>
    <section class="upcoming-section">
        <div class="container">
            <h2>Скоро начнутся</h2>
            <div class="upcoming-grid">
                <?php foreach ($upcoming as $promo): ?>
                    <div class="upcoming-card">
                        <?= getDiscountBadge($promo) ?>
                        <h4><?= htmlspecialchars($promo['title']) ?></h4>
                        <p><?= htmlspecialchars($promo['short_description'] ?: $promo['description']) ?></p>
                        <div class="upcoming-date">
                            Старт <?= date('d.m.Y', strtotime($promo['valid_from'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Как получить скидку -->
    <section class="how-to-get">
        <div class="container">
            <h2>Как получить скидку</h2>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Выберите акцию</h3>
                    <p>Ознакомьтесь с действующими предложениями и выберите подходящую</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Запишитесь онлайн</h3>
                    <p>При записи укажите, что хотите воспользоваться акцией</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Подтвердите условия</h3>
                    <p>При посещении предъявите необходимые документы (если требуется)</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h3>Получите скидку</h3>
                    <p>Наслаждайтесь услугами по специальной цене</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Важные условия -->
    <section class="terms">
        <div class="container">
            <div class="terms-box">
                <h3>Важная информация</h3>
                <ul>
                    <li>Акции не суммируются между собой, если не указано иное</li>
                    <li>Скидка по акции применяется к базовой стоимости услуг</li>
                    <li>Для получения скидки необходимо сообщить о ней при записи</li>
                    <li>Администрация вправе изменять условия акций</li>
                    <li>Подробности уточняйте у администраторов салона</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Подписка на новости -->
    <section class="subscribe">
        <div class="container">
            <div class="subscribe-box">
                <h3>Хотите узнавать об акциях первыми?</h3>
                <p>Подпишитесь на наши новости и получайте уведомления о новых скидках</p>
                <form class="subscribe-form" onsubmit="event.preventDefault(); showNotification('Спасибо за подписку!', 'success'); this.reset();">
                    <input type="email" placeholder="Ваш email" required>
                    <button type="submit" class="btn-primary">Подписаться</button>
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

    <script src="js/promotions.js"></script>
    <script src="js/common.js"></script>
</body>
</html>