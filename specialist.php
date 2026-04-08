<?php
require_once 'config.php';

// Получаем ID специалиста из адресной строки
$specialist_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($specialist_id <= 0) {
    header('Location: specialists.php');
    exit;
}

// Получаем информацию о специалисте
$stmt = $pdo->prepare("SELECT * FROM specialists WHERE id = ?");
$stmt->execute([$specialist_id]);
$specialist = $stmt->fetch();

if (!$specialist) {
    header('Location: specialists.php');
    exit;
}

// Получаем услуги, которые делает этот специалист
$stmt = $pdo->prepare("
    SELECT s.* FROM services s
    JOIN service_specialist ss ON s.id = ss.service_id
    WHERE ss.specialist_id = ?
    ORDER BY s.category, s.name
");
$stmt->execute([$specialist_id]);
$services = $stmt->fetchAll();

// Получаем отзывы из базы
$stmt = $pdo->prepare("
    SELECT * FROM reviews 
    WHERE specialist_id = ? AND is_approved = TRUE 
    ORDER BY review_date DESC 
    LIMIT 10
");
$stmt->execute([$specialist_id]);
$reviews = $stmt->fetchAll();

// Считаем средний рейтинг
$stmt = $pdo->prepare("
    SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
    FROM reviews 
    WHERE specialist_id = ? AND is_approved = TRUE
");
$stmt->execute([$specialist_id]);
$rating_stats = $stmt->fetch();
$avg_rating = $rating_stats['avg_rating'] ? round($rating_stats['avg_rating'], 1) : 'Новый';
$total_reviews = $rating_stats['total_reviews'] ?: 0;

// Получаем других специалистов (кроме текущего)
$stmt = $pdo->prepare("SELECT * FROM specialists WHERE id != ? LIMIT 3");
$stmt->execute([$specialist_id]);
$others = $stmt->fetchAll();

// Функция склонения слов
function getYearWord($years) {
    $years = $years % 100;
    if ($years >= 11 && $years <= 19) return 'лет';
    $last = $years % 10;
    if ($last == 1) return 'год';
    if ($last >= 2 && $last <= 4) return 'года';
    return 'лет';
}

// Сообщения
$success_message = isset($_GET['review_sent']) ? 'Спасибо за отзыв! Он появится после проверки модератором.' : '';
$error_message = isset($_GET['review_error']) ? htmlspecialchars($_GET['review_error']) : '';

// Текущая дата для календаря
$current_month = date('n');
$current_year = date('Y');
$month_names = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($specialist['name']) ?> | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/specialist.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>

    <div class="cursor"></div>
    
    <header>
        <div class="logo">ÉLAN</div>
        <nav>
            <a href="index.php">Главная</a>
            <a href="services.php">Услуги</a>
            <a href="specialists.php" class="active">Специалисты</a>
            <a href="promotions.php">Акции</a>
            <a href="contacts.php">Контакты</a>
        </nav>
    </header>

    <!-- Сообщения -->
    <?php if ($success_message): ?>
        <div class="notification success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="notification error"><?= $error_message ?></div>
    <?php endif; ?>

    <div class="breadcrumbs">
        <a href="index.php">Главная</a> / 
        <a href="specialists.php">Специалисты</a> / 
        <span><?= htmlspecialchars($specialist['name']) ?></span>
    </div>

    <!-- Основная информация о специалисте -->
    <section class="specialist-profile">
        <div class="profile-container">
            <div class="profile-image">
                <?php if ($specialist['photo'] && file_exists($specialist['photo'])): ?>
                    <img src="<?= htmlspecialchars($specialist['photo']) ?>" alt="<?= htmlspecialchars($specialist['name']) ?>">
                <?php else: ?>
                    <div class="profile-placeholder">
                        <span><?= mb_substr($specialist['name'], 0, 1) ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?= htmlspecialchars($specialist['name']) ?></h1>
                <p class="profile-specialization"><?= htmlspecialchars($specialist['specialization']) ?></p>
                
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-value"><?= $specialist['experience'] ?></span>
                        <span class="stat-label"><?= getYearWord($specialist['experience']) ?> опыта</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value"><?= count($services) ?></span>
                        <span class="stat-label">услуг</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value"><?= $avg_rating ?></span>
                        <span class="stat-label">рейтинг (<?= $total_reviews ?> отзывов)</span>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="#services" class="btn-secondary">Услуги мастера</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Услуги специалиста -->
    <section id="services" class="specialist-services">
        <div class="container">
            <h2>Услуги мастера</h2>
            <div class="services-list">
                <?php foreach ($services as $service): ?>
                    <div class="service-item">
                        <div class="service-info">
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <p><?= htmlspecialchars($service['description']) ?></p>
                        </div>
                        <div class="service-price">
                            <?= number_format($service['price'], 0, '', ' ') ?> ₽
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Форма записи -->
    <section id="booking" class="booking-section">
        <div class="container">
            <h2>Быстрая запись к <?= htmlspecialchars($specialist['name']) ?></h2>
            <div class="quick-booking-form">
                <form action="booking.php" method="GET">
                    <input type="hidden" name="specialist_id" value="<?= $specialist_id ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Выберите услугу</label>
                            <select name="service_id" required>
                                <option value="">Выберите услугу</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>">
                                        <?= htmlspecialchars($service['name']) ?> - <?= number_format($service['price'], 0, '', ' ') ?> ₽
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Предпочтительная дата</label>
                            <input type="date" name="date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary btn-block">Продолжить запись</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Отзывы -->
    <section class="reviews">
        <div class="container">
            <h2>Отзывы клиентов</h2>
            
            <?php if (empty($reviews)): ?>
                <p class="no-reviews">У этого специалиста пока нет отзывов. Будьте первым!</p>
            <?php else: ?>
                <div class="reviews-grid">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="review-name"><?= htmlspecialchars($review['client_name']) ?></span>
                                <span class="review-date"><?= date('d.m.Y', strtotime($review['review_date'])) ?></span>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="add-review">
                    <button class="btn-secondary" onclick="openReviewForm(<?= $specialist_id ?>)">Оставить отзыв</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Другие специалисты -->
    <?php if (!empty($others)): ?>
    <section class="other-specialists">
        <div class="container">
            <h2>Другие специалисты</h2>
            <div class="specialists-mini-grid">
                <?php foreach ($others as $other): ?>
                    <a href="specialist.php?id=<?= $other['id'] ?>" class="specialist-mini-card">
                        <div class="mini-image">
                            <?php if ($other['photo'] && file_exists($other['photo'])): ?>
                                <img src="<?= htmlspecialchars($other['photo']) ?>" alt="<?= htmlspecialchars($other['name']) ?>">
                            <?php else: ?>
                                <div class="mini-placeholder">
                                    <?= mb_substr($other['name'], 0, 1) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mini-info">
                            <h4><?= htmlspecialchars($other['name']) ?></h4>
                            <p><?= htmlspecialchars($other['specialization']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Форма добавления отзыва (модальное окно) -->
    <div id="reviewFormModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Оставить отзыв о <?= htmlspecialchars($specialist['name']) ?></h3>
            <form action="add-review.php" method="POST" class="review-form">
                <input type="hidden" name="specialist_id" value="<?= $specialist_id ?>">
                
                <div class="form-group">
                    <label>Ваше имя *</label>
                    <input type="text" name="client_name" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон (не обязательно)</label>
                    <input type="tel" name="client_phone">
                </div>
                
                <div class="form-group">
                    <label>Оценка *</label>
                    <div class="rating-input">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>" required>
                            <label for="star<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Ваш отзыв *</label>
                    <textarea name="review_text" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Отправить отзыв</button>
            </form>
        </div>
    </div>

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

    <script src="js/specialist.js"></script>
    <script src="js/common.js"></script>
</body>
</html>