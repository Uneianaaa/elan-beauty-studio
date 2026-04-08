<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
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
            <a href="contacts.php">Контакты</a>
        </nav>
    </header>
    
    <!-- Главный экран -->
    <section class="hero">
        <div>
            <h1>Beauty <span>Studio</span></h1>
            <p>Современный салон красоты с профессиональными мастерами, премиальной косметикой и атмосферой комфорта.</p>
            <a href="booking.php" class="btn">Онлайн запись</a>
        </div>
        <img src="img/indexhero.jpg" alt="Beauty studio">
    </section>
    
    <!-- Блок услуг -->
    <section class="services">
        <h2>Наши услуги</h2>
        <div class="service-grid">
            <a href="services.php?category=Парикмахерские%20услуги" class="service">
                <h3>Парикмахерские услуги</h3>
                <p>Стрижки, окрашивание и укладки.</p>
            </a>
            <a href="services.php?category=Ногтевой%20сервис" class="service">
                <h3>Ногтевой сервис</h3>
                <p>Маникюр, педикюр, дизайн.</p>
            </a>
            <a href="services.php?category=Косметология" class="service">
                <h3>Косметология</h3>
                <p>Уход за кожей лица.</p>
            </a>
            <a href="services.php?category=Макияж" class="service">
                <h3>Макияж</h3>
                <p>Дневной и вечерний макияж.</p>
            </a>
        </div>
    </section>
    
    <!-- Блок галереи -->
    <section class="gallery">
        <h2>Наши работы</h2>
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="img/work1.jpg" alt="работа 1">
            </div>
            <div class="gallery-item">
                <img src="img/work2.jpg" alt="работа 2">
            </div>
            <div class="gallery-item">
                <img src="img/work3.jpg" alt="работа 3">
            </div>
        </div>
    </section>
    
    <!-- Блок акций -->
    <section class="promo">
        <a href="promotions.php" class="promo-box">
            <h3>Скидка 20%</h3>
            <p>На первое посещение салона.</p>
        </a>
        <a href="promotions.php" class="promo-box">
            <h3>SPA день</h3>
            <p>Комплексный уход для лица и тела.</p>
        </a>
    </section>
    
    <!-- Блок специалистов (топ-3) -->
    <section class="team">
        <h2>Наши специалисты</h2>
        <div class="team-grid">
            <?php
            // Подключаемся к базе и получаем топ-3 специалиста
            require_once 'config.php';
            
            $stmt = $pdo->query("
                SELECT s.*, COUNT(ss.service_id) as services_count 
                FROM specialists s
                LEFT JOIN service_specialist ss ON s.id = ss.specialist_id
                GROUP BY s.id
                ORDER BY services_count DESC
                LIMIT 3
            ");
            $top_specialists = $stmt->fetchAll();
            
            foreach ($top_specialists as $specialist): 
            ?>
                <a href="specialist.php?id=<?= $specialist['id'] ?>" class="member">
                    <?php if ($specialist['photo'] && file_exists($specialist['photo'])): ?>
                        <img src="<?= htmlspecialchars($specialist['photo']) ?>" alt="<?= htmlspecialchars($specialist['name']) ?>">
                    <?php else: ?>
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($specialist['name']) ?></h4>
                    <span><?= htmlspecialchars($specialist['specialization']) ?></span>
                </a>
            <?php endforeach; ?>
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
    </footer>
    <button class="scroll-top">↑</button>
    
    <script src="js/index.js"></script>
    <script src="js/common.js"></script>
</body>
</html>