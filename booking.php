<?php
// Подключаем конфиг с БД
require_once 'config.php';

// Получаем параметры из URL
$selected_service = isset($_GET['service_id']) ? (int)$_GET['service_id'] : null;
$selected_specialist = isset($_GET['specialist_id']) ? (int)$_GET['specialist_id'] : null;
$selected_date = isset($_GET['date']) ? $_GET['date'] : null;
$selected_time = isset($_GET['time']) ? $_GET['time'] : null;
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Флаг успешной записи
$show_success = isset($_GET['success']) ? true : false;

// Определяем текущий шаг на основе выбранных данных
if ($selected_service && $selected_specialist && $selected_date && $selected_time) {
    $step = 4;
} elseif ($selected_service && $selected_specialist && $selected_date) {
    $step = 3;
} elseif ($selected_service && $selected_specialist) {
    $step = 3;
} elseif ($selected_service) {
    $step = 2;
} else {
    $step = 1;
}

// Все услуги для шага 1
$services = $pdo->query("SELECT * FROM services ORDER BY category, name")->fetchAll();

// Специалисты для выбранной услуги
if ($selected_service) {
    $stmt = $pdo->prepare("
        SELECT s.* FROM specialists s
        JOIN service_specialist ss ON s.id = ss.specialist_id
        WHERE ss.service_id = ?
        ORDER BY s.name
    ");
    $stmt->execute([$selected_service]);
    $available_specialists = $stmt->fetchAll();
}

// Информация о выбранной услуге
if ($selected_service) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$selected_service]);
    $service_info = $stmt->fetch();
}

// Информация о выбранном специалисте
if ($selected_specialist) {
    $stmt = $pdo->prepare("SELECT * FROM specialists WHERE id = ?");
    $stmt->execute([$selected_specialist]);
    $specialist_info = $stmt->fetch();
}

// Даты для календаря (ближайшие 14 дней)
$dates = [];
for ($i = 1; $i <= 14; $i++) {
    $date = strtotime("+$i days");
    $dates[] = [
        'value' => date('Y-m-d', $date),
        'display' => date('d.m', $date),
        'day' => date('D', $date),
        'full' => date('d.m.Y', $date)
    ];
}

// Обработка отправки формы на шаге 4
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $service_id = (int)$_POST['service_id'];
    $specialist_id = (int)$_POST['specialist_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    
    // Валидация
    if (empty($name)) $errors[] = 'Введите имя';
    if (empty($phone)) $errors[] = 'Введите телефон';
    if (!$service_id) $errors[] = 'Выберите услугу';
    if (!$specialist_id) $errors[] = 'Выберите специалиста';
    if (!$date) $errors[] = 'Выберите дату';
    if (!$time) $errors[] = 'Выберите время';
    
    // Сохраняем запись в БД
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO appointments (client_name, phone, service_id, specialist_id, date, time) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$name, $phone, $service_id, $specialist_id, $date, $time])) {
            header("Location: booking.php?success=1");
            exit;
        } else {
            $errors[] = 'Ошибка при сохранении записи';
        }
    }
}

// Иконки для категорий услуг
function getIconForCategory($category) {
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

// Склонение слова "год"
function getYearWord($years) {
    $years = $years % 100;
    if ($years >= 11 && $years <= 19) return 'лет';
    $last = $years % 10;
    if ($last == 1) return 'год';
    if ($last >= 2 && $last <= 4) return 'года';
    return 'лет';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Онлайн-запись | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/booking.css">
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

    <section class="page-header">
        <h1>Онлайн-<span>запись</span></h1>
        <!-- Индикаторы шагов -->
        <div class="booking-steps">
            <div class="step-indicator <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">
                <span class="step-number">1</span>
                <span class="step-label">Выбор услуги</span>
            </div>
            <div class="step-line <?= $step > 1 ? 'active' : '' ?>"></div>
            <div class="step-indicator <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">
                <span class="step-number">2</span>
                <span class="step-label">Выбор мастера</span>
            </div>
            <div class="step-line <?= $step > 2 ? 'active' : '' ?>"></div>
            <div class="step-indicator <?= $step >= 3 ? 'active' : '' ?> <?= $step > 3 ? 'completed' : '' ?>">
                <span class="step-number">3</span>
                <span class="step-label">Дата и время</span>
            </div>
            <div class="step-line <?= $step > 3 ? 'active' : '' ?>"></div>
            <div class="step-indicator <?= $step >= 4 ? 'active' : '' ?>">
                <span class="step-number">4</span>
                <span class="step-label">Подтверждение</span>
            </div>
        </div>
    </section>

    <section class="booking-content">
        <div class="container">
            <!-- Вывод ошибок -->
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- ШАГ 1: Выбор услуги -->
            <?php if ($step == 1): ?>
                <div class="booking-step active">
                    <h2>Шаг 1: Выберите услугу</h2>
                    <div class="services-booking-grid">
                        <?php foreach ($services as $service): ?>
                            <a href="?service_id=<?= $service['id'] ?>&step=2" class="service-booking-card">
                                <div class="service-booking-icon">
                                    <?= getIconForCategory($service['category']) ?>
                                </div>
                                <h3><?= htmlspecialchars($service['name']) ?></h3>
                                <p class="service-category"><?= htmlspecialchars($service['category']) ?></p>
                                <p class="service-booking-price"><?= number_format($service['price'], 0, '', ' ') ?> ₽</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ШАГ 2: Выбор мастера -->
            <?php if ($step == 2): ?>
                <div class="booking-step active">
                    <h2>Шаг 2: Выберите мастера</h2>
                    
                    <?php if ($selected_service && !empty($available_specialists)): ?>
                        <p class="step-info">Для услуги "<?= htmlspecialchars($service_info['name']) ?>"</p>
                        <div class="specialists-booking-grid">
                            <?php foreach ($available_specialists as $specialist): ?>
                                <a href="?service_id=<?= $selected_service ?>&specialist_id=<?= $specialist['id'] ?>&step=3" class="specialist-booking-card">
                                    <div class="specialist-booking-image">
                                        <?php if ($specialist['photo'] && file_exists($specialist['photo'])): ?>
                                            <img src="<?= htmlspecialchars($specialist['photo']) ?>" alt="<?= htmlspecialchars($specialist['name']) ?>">
                                        <?php else: ?>
                                            <div class="specialist-booking-placeholder">
                                                <?= mb_substr($specialist['name'], 0, 1) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <h3><?= htmlspecialchars($specialist['name']) ?></h3>
                                    <p class="specialist-spec"><?= htmlspecialchars($specialist['specialization']) ?></p>
                                    <p class="specialist-exp"><?= $specialist['experience'] ?> <?= getYearWord($specialist['experience']) ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-results">
                            Нет доступных мастеров для этой услуги. 
                            <a href="?step=1">Вернуться к выбору услуги</a>
                        </p>
                    <?php endif; ?>
                    
                    <div class="step-navigation">
                        <a href="?step=1" class="btn-secondary">Назад</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ШАГ 3: Выбор даты и времени -->
            <?php if ($step == 3): ?>
                <div class="booking-step active">
                    <h2>Шаг 3: Выберите дату и время</h2>
                    
                    <!-- Краткая сводка выбранного -->
                    <div class="booking-summary">
                        <div class="summary-item">
                            <span class="summary-label">Услуга:</span>
                            <span class="summary-value"><?= htmlspecialchars($service_info['name']) ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Мастер:</span>
                            <span class="summary-value"><?= htmlspecialchars($specialist_info['name']) ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Стоимость:</span>
                            <span class="summary-value price"><?= number_format($service_info['price'], 0, '', ' ') ?> ₽</span>
                        </div>
                    </div>

                    <form method="GET" action="booking.php" class="datetime-form">
                        <input type="hidden" name="service_id" value="<?= $selected_service ?>">
                        <input type="hidden" name="specialist_id" value="<?= $selected_specialist ?>">
                        <input type="hidden" name="step" id="step-input" value="3">
                        
                        <div class="datetime-selection" id="booking-calendar">
                            <!-- Выбор даты -->
                            <div class="date-selection">
                                <h3>Выберите дату</h3>
                                <div class="dates-grid">
                                    <?php foreach ($dates as $date): ?>
                                        <label class="date-card <?= $selected_date == $date['value'] ? 'selected' : '' ?>">
                                            <input type="radio" name="date" value="<?= $date['value'] ?>" 
                                                   <?= $selected_date == $date['value'] ? 'checked' : '' ?> 
                                                   required onchange="this.form.submit()">
                                            <span class="date-day"><?= $date['day'] ?></span>
                                            <span class="date-number"><?= $date['display'] ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Выбор времени (показывается только если выбрана дата) -->
                            <?php if ($selected_date): ?>
                                <div class="time-selection">
                                    <h3>Выберите время</h3>
                                    <div class="time-slots-grid">
                                        <?php
                                        $all_slots = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];
                                        foreach ($all_slots as $slot):
                                            $is_available = (rand(1, 10) > 3); // Временная заглушка
                                        ?>
                                            <label class="time-slot-card <?= !$is_available ? 'booked' : '' ?> <?= $selected_time == $slot ? 'selected' : '' ?>">
                                                <input type="radio" name="time" value="<?= $slot ?>" 
                                                       <?= !$is_available ? 'disabled' : '' ?> 
                                                       <?= $selected_time == $slot ? 'checked' : '' ?>
                                                       required onchange="document.getElementById('step-input').value='4'; this.form.submit()">
                                                <?= $slot ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="step-navigation">
                        <a href="?service_id=<?= $selected_service ?>&step=2" class="btn-secondary">Назад</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ШАГ 4: Подтверждение и ввод данных -->
            <?php if ($step == 4): ?>
                <?php
                // Проверяем наличие всех данных
                if (!$selected_service || !$selected_specialist || !$selected_date || !$selected_time) {
                    header('Location: booking.php?step=1');
                    exit;
                }
                ?>
                <div class="booking-step active">
                    <h2>Шаг 4: Подтверждение записи</h2>
                    
                    <div class="booking-confirm">
                        <!-- Левая колонка - детали записи -->
                        <div class="confirm-details">
                            <div class="confirm-item">
                                <div class="confirm-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 12c0 4.4-3.6 8-8 8s-8-3.6-8-8 3.6-8 8-8 8 3.6 8 8z"/>
                                        <path d="M12 8v4l3 3"/>
                                    </svg>
                                </div>
                                <div class="confirm-info">
                                    <div class="confirm-label">Услуга</div>
                                    <div class="confirm-value"><?= htmlspecialchars($service_info['name']) ?></div>
                                </div>
                            </div>
                            <div class="confirm-item">
                                <div class="confirm-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 21v-2c0-2.8-2.2-5-5-5H9c-2.8 0-5 2.2-5 5v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="confirm-info">
                                    <div class="confirm-label">Мастер</div>
                                    <div class="confirm-value"><?= htmlspecialchars($specialist_info['name']) ?></div>
                                </div>
                            </div>
                            <div class="confirm-item">
                                <div class="confirm-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </div>
                                <div class="confirm-info">
                                    <div class="confirm-label">Дата и время</div>
                                    <div class="confirm-value">
                                        <?= date('d.m.Y', strtotime($selected_date)) ?> в <?= htmlspecialchars($selected_time) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="confirm-item">
                                <div class="confirm-icon">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 14H12" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M10 12V8.2C10 8.0142 10 7.9213 10.0123 7.84357C10.0801 7.41567 10.4157 7.08008 10.8436 7.01231C10.9213 7 11.0142 7 11.2 7H13.5C14.8807 7 16 8.11929 16 9.5C16 10.8807 14.8807 12 13.5 12H10ZM10 12V17M10 12H9" stroke-width="1.5" stroke-linecap="round"/>
                                        <path d="M7 3.33782C8.47087 2.48697 10.1786 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 10.1786 2.48697 8.47087 3.33782 7" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="confirm-info">
                                    <div class="confirm-label">Стоимость</div>
                                    <div class="confirm-value price"><?= number_format($service_info['price'], 0, '', ' ') ?> ₽</div>
                                </div>
                            </div>
                        </div>

                        <!-- Правая колонка - форма с контактными данными -->
                        <div class="client-data-form">
                            <h3>Ваши контактные данные</h3>
                            
                            <form method="POST" action="booking.php">
                                <input type="hidden" name="confirm" value="1">
                                <input type="hidden" name="service_id" value="<?= $selected_service ?>">
                                <input type="hidden" name="specialist_id" value="<?= $selected_specialist ?>">
                                <input type="hidden" name="date" value="<?= htmlspecialchars($selected_date) ?>">
                                <input type="hidden" name="time" value="<?= htmlspecialchars($selected_time) ?>">
                                
                                <div class="form-group">
                                    <label for="name">Ваше имя *</label>
                                    <input type="text" id="name" name="name" required placeholder="Как к вам обращаться?">
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Телефон *</label>
                                    <input type="tel" id="phone" name="phone" required placeholder="+7 (___) ___-__-__">
                                </div>
                                
                                <div class="form-group">
                                    <label for="comment">Комментарий (не обязательно)</label>
                                    <textarea id="comment" name="comment" rows="3" placeholder="Ваши пожелания..."></textarea>
                                </div>
                                
                                <div class="step-navigation">
                                    <a href="?service_id=<?= $selected_service ?>&specialist_id=<?= $selected_specialist ?>&step=3" class="btn-secondary">Назад</a>
                                    <button type="submit" class="btn-primary">Подтвердить запись</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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

    <!-- Уведомление об успешной записи -->
    <?php if ($show_success): ?>
    <script>
        const notification = document.createElement('div');
        notification.className = 'notification success';
        notification.textContent = '✓ Спасибо! Вы успешно записаны. Мы отправили подтверждение на ваш телефон.';
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 25px;
            background: #4caf50;
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
                window.location.href = 'index.php';
            }, 300);
        }, 3000);
    </script>
    <?php endif; ?>

    <!-- Скролл к календарю если выбрана дата -->
    <script>
        <?php if ($step == 3 && $selected_date): ?>
            setTimeout(() => {
                const calendar = document.getElementById('booking-calendar');
                if (calendar) {
                    calendar.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }, 100);
        <?php endif; ?>
    </script>

    <script src="js/booking.js"></script>
    <script src="js/common.js"></script>
</body>
</html>