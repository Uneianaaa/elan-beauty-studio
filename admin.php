<?php
require_once 'config.php';

// Получаем все записи
$stmt = $pdo->query("
    SELECT a.*, s.name as service_name, sp.name as specialist_name 
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    JOIN specialists sp ON a.specialist_id = sp.id
    ORDER BY a.date DESC, a.time DESC
");
$appointments = $stmt->fetchAll();

// Получаем отзывы на модерации
$stmt = $pdo->prepare("
    SELECT r.*, sp.name as specialist_name 
    FROM reviews r
    JOIN specialists sp ON r.specialist_id = sp.id
    WHERE r.is_approved = FALSE
    ORDER BY r.created_at DESC
");
$stmt->execute();
$pending_reviews = $stmt->fetchAll();

// Получаем одобренные отзывы
$stmt = $pdo->prepare("
    SELECT r.*, sp.name as specialist_name 
    FROM reviews r
    JOIN specialists sp ON r.specialist_id = sp.id
    WHERE r.is_approved = TRUE
    ORDER BY r.review_date DESC
    LIMIT 20
");
$stmt->execute();
$approved_reviews = $stmt->fetchAll();

// Получаем сообщения из JSON-файла
$messages = [];
if (file_exists('messages.json')) {
    $json = file_get_contents('messages.json');
    $messages = json_decode($json, true) ?: [];
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Одобрение отзыва
    if (isset($_POST['approve_review'])) {
        $review_id = (int)$_POST['review_id'];
        $stmt = $pdo->prepare("UPDATE reviews SET is_approved = TRUE WHERE id = ?");
        $stmt->execute([$review_id]);
        header("Location: admin.php?success=review_approved");
        exit;
    }
    
    // Удаление отзыва
    if (isset($_POST['delete_review'])) {
        $review_id = (int)$_POST['review_id'];
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$review_id]);
        header("Location: admin.php?success=review_deleted");
        exit;
    }
    
    // Удаление записи
    if (isset($_POST['delete_appointment'])) {
        $appointment_id = (int)$_POST['appointment_id'];
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);
        header("Location: admin.php?success=appointment_deleted");
        exit;
    }
    
    // Отметить сообщение как прочитанное
    if (isset($_POST['mark_read'])) {
        $message_id = (int)$_POST['message_id'];
        $messages = [];
        if (file_exists('messages.json')) {
            $json = file_get_contents('messages.json');
            $messages = json_decode($json, true) ?: [];
        }
        
        foreach ($messages as &$msg) {
            if ($msg['id'] == $message_id) {
                $msg['is_read'] = true;
                break;
            }
        }
        
        file_put_contents('messages.json', json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: admin.php?success=message_read");
        exit;
    }
    
    // Удалить сообщение
    if (isset($_POST['delete_message'])) {
        $message_id = (int)$_POST['message_id'];
        $messages = [];
        if (file_exists('messages.json')) {
            $json = file_get_contents('messages.json');
            $messages = json_decode($json, true) ?: [];
        }
        
        foreach ($messages as $key => $msg) {
            if ($msg['id'] == $message_id) {
                unset($messages[$key]);
                break;
            }
        }
        
        $messages = array_values($messages);
        file_put_contents('messages.json', json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: admin.php?success=message_deleted");
        exit;
    }
}

$success_message = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'review_approved':
            $success_message = 'Отзыв успешно одобрен';
            break;
        case 'review_deleted':
            $success_message = 'Отзыв удалён';
            break;
        case 'appointment_deleted':
            $success_message = 'Запись удалена';
            break;
        case 'message_read':
            $success_message = 'Сообщение отмечено прочитанным';
            break;
        case 'message_deleted':
            $success_message = 'Сообщение удалено';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <div class="cursor"></div>
    
    <header>
        <div class="logo">ÉLAN <span class="admin-badge">Admin</span></div>
        <nav>
            <a href="index.php">На сайт</a>
            <a href="admin.php" class="active">Панель управления</a>
        </nav>
    </header>

    <section class="page-header">
        <h1>Админ-панель</h1>
        <p>Управление записями и отзывами</p>
    </section>

    <?php if ($success_message): ?>
        <div class="notification success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <section class="admin-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= count($appointments) ?></span>
                        <span class="stat-label">Всего записей</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 21v-2c0-2.8-2.2-5-5-5H9c-2.8 0-5 2.2-5 5v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= count($pending_reviews) ?></span>
                        <span class="stat-label">Отзывов на модерации</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M22 11.1C22 16.5 17.5 20 12 20S2 16.5 2 11.1 6.5 2 12 2s10 4.5 10 9.1z"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <span class="stat-number"><?= count($messages) ?></span>
                        <span class="stat-label">Сообщений</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Сообщения из формы обратной связи -->
    <section class="admin-section">
        <div class="container">
            <h2>Сообщения от клиентов</h2>
            
            <?php if (empty($messages)): ?>
                <p class="empty-state">Нет сообщений</p>
            <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($messages as $msg): ?>
                        <div class="review-item <?= !$msg['is_read'] ? 'unread' : '' ?>">
                            <div class="review-header">
                                <span class="review-name"><?= htmlspecialchars($msg['name']) ?></span>
                                <span class="review-specialist"><?= htmlspecialchars($msg['phone']) ?></span>
                                <span class="review-date"><?= htmlspecialchars($msg['date']) ?></span>
                            </div>
                            <p class="review-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                            <div class="review-actions">
                                <?php if (!$msg['is_read']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                    <button type="submit" name="mark_read" class="btn-approve">Отметить прочитанным</button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                    <button type="submit" name="delete_message" class="btn-delete" onclick="return confirm('Удалить сообщение?')">Удалить</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Отзывы на модерации -->
    <section class="admin-section">
        <div class="container">
            <h2>Отзывы на модерации</h2>
            
            <?php if (empty($pending_reviews)): ?>
                <p class="empty-state">Нет отзывов на модерации</p>
            <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($pending_reviews as $review): ?>
                        <div class="review-item pending">
                            <div class="review-header">
                                <span class="review-name"><?= htmlspecialchars($review['client_name']) ?></span>
                                <span class="review-specialist">К специалисту: <?= htmlspecialchars($review['specialist_name']) ?></span>
                                <span class="review-date"><?= date('d.m.Y', strtotime($review['review_date'])) ?></span>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
                            <div class="review-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                    <button type="submit" name="approve_review" class="btn-approve">Одобрить</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                    <button type="submit" name="delete_review" class="btn-delete" onclick="return confirm('Удалить отзыв?')">Удалить</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Записи -->
    <section class="admin-section">
        <div class="container">
            <h2>Все записи</h2>
            
            <?php if (empty($appointments)): ?>
                <p class="empty-state">Нет записей</p>
            <?php else: ?>
                <div class="appointments-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Клиент</th>
                                <th>Телефон</th>
                                <th>Услуга</th>
                                <th>Мастер</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?= date('d.m.Y', strtotime($appointment['date'])) ?></td>
                                    <td><?= htmlspecialchars($appointment['time']) ?></td>
                                    <td><?= htmlspecialchars($appointment['client_name']) ?></td>
                                    <td><?= htmlspecialchars($appointment['phone']) ?></td>
                                    <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                    <td><?= htmlspecialchars($appointment['specialist_name']) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                            <button type="submit" name="delete_appointment" class="btn-delete-small" onclick="return confirm('Удалить запись?')">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
        <div class="copyright">
            <p>© 2026 ÉLAN Beauty Studio. Все права защищены</p>
        </div>
    </footer>

    <script src="js/admin.js"></script>
</body>
</html>