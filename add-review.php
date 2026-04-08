<?php
// Подключаем конфиг с БД
require_once 'config.php';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialist_id = (int)$_POST['specialist_id'];
    $client_name = trim($_POST['client_name']);
    $client_phone = trim($_POST['client_phone'] ?? '');
    $rating = (int)$_POST['rating'];
    $review_text = trim($_POST['review_text']);
    
    $errors = [];
    
    if (empty($client_name)) $errors[] = 'Введите имя';
    if ($rating < 1 || $rating > 5) $errors[] = 'Поставьте оценку';
    if (empty($review_text)) $errors[] = 'Напишите отзыв';
    
    // Сохраняем в БД
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (specialist_id, client_name, client_phone, rating, review_text, review_date, is_approved) 
            VALUES (?, ?, ?, ?, ?, CURDATE(), FALSE)
        ");
        
        if ($stmt->execute([$specialist_id, $client_name, $client_phone, $rating, $review_text])) {
            // Успешно - редирект с параметром success
            header("Location: specialist.php?id=$specialist_id&review_sent=1");
            exit;
        } else {
            $error_message = 'Ошибка при сохранении отзыва';
            header("Location: specialist.php?id=$specialist_id&review_error=" . urlencode($error_message));
            exit;
        }
    } else {
        // Ошибки валидации
        $error_message = implode('\\n', $errors);
        header("Location: specialist.php?id=$specialist_id&review_error=" . urlencode($error_message));
        exit;
    }
}

// Если не POST - на главную
header('Location: index.php');
exit;
?>