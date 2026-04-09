<?php
// Файл для сохранения сообщений из формы обратной связи
require_once 'config.php';

$messages_file = 'messages.json';

// Загружаем существующие сообщения
$messages = [];
if (file_exists($messages_file)) {
    $json = file_get_contents($messages_file);
    $messages = json_decode($json, true) ?: [];
}

// Получаем данные из формы
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name)) $errors[] = 'Введите имя';
if (empty($phone)) $errors[] = 'Введите телефон';
if (empty($message)) $errors[] = 'Введите сообщение';

if (empty($errors)) {
    // Добавляем новое сообщение
    $new_message = [
        'id' => time(),
        'name' => $name,
        'phone' => $phone,
        'message' => $message,
        'date' => date('Y-m-d H:i:s'),
        'is_read' => false
    ];
    
    array_unshift($messages, $new_message); // Добавляем в начало
    
    // Сохраняем в файл
    file_put_contents($messages_file, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    header("Location: contacts.php?success=1");
    exit;
} else {
    $error_msg = implode(', ', $errors);
    header("Location: contacts.php?error=" . urlencode($error_msg));
    exit;
}
?>