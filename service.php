<?php
require_once 'config.php';

// Получаем ID услуги из адресной строки
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($service_id <= 0) {
    header('Location: services.php');
    exit;
}

// Получаем информацию об услуге
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: services.php');
    exit;
}

// Получаем специалистов, которые делают эту услугу
$stmt = $pdo->prepare("
    SELECT s.* FROM specialists s
    JOIN service_specialist ss ON s.id = ss.specialist_id
    WHERE ss.service_id = ?
    ORDER BY s.name
");
$stmt->execute([$service_id]);
$specialists = $stmt->fetchAll();

// Получаем похожие услуги (из той же категории)
$stmt = $pdo->prepare("
    SELECT * FROM services 
    WHERE category = ? AND id != ? 
    LIMIT 3
");
$stmt->execute([$service['category'], $service_id]);
$similar_services = $stmt->fetchAll();

// Функция для форматирования цены
function formatPrice($price) {
    return number_format($price, 0, '', ' ') . ' ₽';
}

// Функция для получения SVG-иконки по категории
function getServiceIcon($category) {
    switch ($category) {
        case 'Парикмахерские услуги':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 8c-2 0-4 2-4 4s2 4 4 4 4-2 4-4-2-4-4-4z"/><path d="M20 12c0 4.4-3.6 8-8 8s-8-3.6-8-8 3.6-8 8-8 8 3.6 8 8z"/></svg>';
        case 'Окрашивание':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>';
        case 'Ногтевой сервис':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="6" y="4" width="12" height="16" rx="2"/><path d="M8 8h8"/><path d="M8 12h8"/><path d="M8 16h5"/></svg>';
        case 'Косметология':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M5 20v-2c0-4 3-7 7-7s7 3 7 7v2"/></svg>';
        case 'Макияж':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="10" r="2"/><path d="M5 15c0-3 3-5 7-5s7 2 7 5"/><path d="M4 20h16"/></svg>';
        default:
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($service['name']) ?> | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/service.css">
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

    <div class="breadcrumbs">
        <a href="index.php">Главная</a> / 
        <a href="services.php">Услуги</a> / 
        <a href="services.php?category=<?= urlencode($service['category']) ?>"><?= htmlspecialchars($service['category']) ?></a> / 
        <span><?= htmlspecialchars($service['name']) ?></span>
    </div>

    <!-- Детальная информация об услуге -->
    <section class="service-detail">
        <div class="service-container">
            <div class="service-header">
                <div class="service-icon-large">
                    <?= getServiceIcon($service['category']) ?>
                </div>
                <div class="service-title-section">
                    <h1 class="service-title"><?= htmlspecialchars($service['name']) ?></h1>
                    <p class="service-category"><?= htmlspecialchars($service['category']) ?></p>
                </div>
                <div class="service-price-large">
                    <?= formatPrice($service['price']) ?>
                </div>
            </div>

            <div class="service-content">
                <div class="service-description-section">
                    <h2>Описание услуги</h2>
                    <p class="service-full-description"><?= nl2br(htmlspecialchars($service['description'])) ?></p>
                    
                    <div class="service-features">
                        <h3>Что входит в услугу:</h3>
                        <ul>
                            <li>Консультация мастера</li>
                            <li>Использование профессиональных материалов</li>
                            <li>Рекомендации по уходу</li>
                            <li>Гарантия качества</li>
                        </ul>
                    </div>
                </div>

                <div class="service-sidebar">
                    <div class="booking-card">
                        <h3>Записаться на услугу</h3>
                        <form action="booking.php" method="GET" class="quick-booking">
                            <input type="hidden" name="service_id" value="<?= $service_id ?>">
                            
                            <div class="form-group">
                                <label>Выберите мастера</label>
                                <select name="specialist_id" required>
                                    <option value="">Любой мастер</option>
                                    <?php foreach ($specialists as $specialist): ?>
                                        <option value="<?= $specialist['id'] ?>">
                                            <?= htmlspecialchars($specialist['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Предпочтительная дата</label>
                                <input type="date" name="date" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <button type="submit" class="btn-primary btn-block">Записаться онлайн</button>
                        </form>
                        <p class="booking-note">или позвоните нам: +7 (900) 123-45-67</p>
                    </div>

                    <div class="service-info-card">
                        <h4>Информация</h4>
                        <ul>
                            <li>Время: 1-2 часа</li>
                            <li>Опыт мастера: от 3 лет</li>
                            <li>Гарантия: 100%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Специалисты, выполняющие услугу -->
    <?php if (!empty($specialists)): ?>
    <section class="service-specialists">
        <div class="container">
            <h2>Специалисты, выполняющие эту услугу</h2>
            <div class="specialists-grid">
                <?php foreach ($specialists as $specialist): ?>
                    <a href="specialist.php?id=<?= $specialist['id'] ?>" class="specialist-card-mini">
                        <div class="specialist-mini-image">
                            <?php if ($specialist['photo'] && file_exists($specialist['photo'])): ?>
                                <img src="<?= htmlspecialchars($specialist['photo']) ?>" alt="<?= htmlspecialchars($specialist['name']) ?>">
                            <?php else: ?>
                                <div class="specialist-mini-placeholder">
                                    <?= mb_substr($specialist['name'], 0, 1) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="specialist-mini-info">
                            <h4><?= htmlspecialchars($specialist['name']) ?></h4>
                            <p><?= htmlspecialchars($specialist['specialization']) ?></p>
                            <span class="experience"><?= $specialist['experience'] ?> <?= getYearWord($specialist['experience']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Похожие услуги -->
    <?php if (!empty($similar_services)): ?>
    <section class="similar-services">
        <div class="container">
            <h2>Похожие услуги</h2>
            <div class="similar-grid">
                <?php foreach ($similar_services as $similar): ?>
                    <a href="service.php?id=<?= $similar['id'] ?>" class="similar-card">
                        <div class="similar-icon">
                            <?= getServiceIcon($similar['category']) ?>
                        </div>
                        <h4><?= htmlspecialchars($similar['name']) ?></h4>
                        <p class="similar-price"><?= formatPrice($similar['price']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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

    <script src="js/service.js"></script>
</body>
</html>

<?php
// Вспомогательная функция для склонения слова "год"
function getYearWord($years) {
    $years = $years % 100;
    if ($years >= 11 && $years <= 19) return 'лет';
    $last = $years % 10;
    if ($last == 1) return 'год';
    if ($last >= 2 && $last <= 4) return 'года';
    return 'лет';
}
?>