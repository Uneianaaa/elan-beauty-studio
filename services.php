<?php
require_once 'config.php';

// Получаем все услуги из базы
$stmt = $pdo->query("SELECT * FROM services ORDER BY category, name");
$services = $stmt->fetchAll();

// Группируем услуги по категориям
$groupedServices = [];
foreach ($services as $service) {
    $category = $service['category'];
    if (!isset($groupedServices[$category])) {
        $groupedServices[$category] = [];
    }
    $groupedServices[$category][] = $service;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>

    <div class="cursor"></div>
    
    <header>
        <div class="logo">ÉLAN</div>
        <nav>
            <a href="index.php">Главная</a>
            <a href="services.php" class="active">Услуги</a>
            <a href="specialists.php">Специалисты</a>
            <a href="promotions.php">Акции</a>
            <a href="contacts.php">Контакты</a>
        </nav>
    </header>

    <section class="page-header">
        <h1>Наши <span>услуги</span></h1>
        <p>Мы предлагаем полный спектр beauty-услуг для вашего совершенства</p>
    </section>

    <!-- Навигация по категориям -->
    <section class="category-nav">
        <div class="category-container">
            <button class="category-btn active" data-category="all">Все услуги</button>
            <?php foreach (array_keys($groupedServices) as $category): ?>
                <button class="category-btn" data-category="<?= htmlspecialchars($category) ?>">
                    <?= htmlspecialchars($category) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Список услуг -->
    <section class="services-page">
        <div class="services-container">
            <?php foreach ($groupedServices as $category => $categoryServices): ?>
                <div class="category-section" data-category="<?= htmlspecialchars($category) ?>">
                    <h2 class="category-title"><?= htmlspecialchars($category) ?></h2>
                    <div class="services-grid">
                        <?php foreach ($categoryServices as $service): ?>
                            <div class="service-card">
                                <div class="service-image">
                                    <div class="service-icon">
                                        <?php
                                        // SVG иконки в зависимости от категории
                                        $icon = '';
                                        switch ($service['category']) {
                                            case 'Парикмахерские услуги':
                                                $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 8c-2 0-4 2-4 4s2 4 4 4 4-2 4-4-2-4-4-4z"/><path d="M20 12c0 4.4-3.6 8-8 8s-8-3.6-8-8 3.6-8 8-8 8 3.6 8 8z"/><path d="M12 4V2"/><path d="M12 22v-2"/><path d="M4 12H2"/><path d="M22 12h-2"/></svg>';
                                                break;
                                            case 'Окрашивание':
                                                $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>';
                                                break;
                                            case 'Ногтевой сервис':
                                                $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/><rect x="6" y="4" width="12" height="16" rx="2"/></svg>';
                                                break;
                                            case 'Косметология':
                                                $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M5 20v-2c0-4 3-7 7-7s7 3 7 7v2"/></svg>';
                                                break;
                                            case 'Макияж':
                                                $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="10" r="2"/><path d="M5 15c0-3 3-5 7-5s7 2 7 5"/><path d="M4 20h16"/></svg>';
                                                break;
                                            default:
                                                $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>';
                                        }
                                        echo $icon;
                                        ?>
                                    </div>
                                </div>
                                <div class="service-content">
                                    <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
                                    <p class="service-description"><?= htmlspecialchars($service['description']) ?></p>
                                    <div class="service-footer">
                                        <span class="service-price"><?= number_format($service['price'], 0, '', ' ') ?> ₽</span>
                                        <a href="service.php?id=<?= $service['id'] ?>" class="service-btn">Подробнее</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
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

    <script src="js/services.js"></script>
    <script src="js/common.js"></script>
</body>
</html>