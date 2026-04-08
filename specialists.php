<?php
require_once 'config.php';

// Получаем всех специалистов из базы
$stmt = $pdo->query("SELECT * FROM specialists ORDER BY specialization, name");
$specialists = $stmt->fetchAll();

// Группируем по специализации для фильтра (без изменения исходного массива)
$groupedSpecialists = [];
foreach ($specialists as $specialist) {
    $specialization = $specialist['specialization'];
    // Разбиваем специализации через запятую
    $specs = explode(',', $specialization);
    foreach ($specs as $spec) {
        $spec = trim($spec);
        if (!isset($groupedSpecialists[$spec])) {
            $groupedSpecialists[$spec] = [];
        }
        // Добавляем ID специалиста, чтобы не дублировать
        if (!in_array($specialist['id'], array_column($groupedSpecialists[$spec], 'id'))) {
            $groupedSpecialists[$spec][] = $specialist;
        }
    }
}

// Для каждого специалиста получаем количество услуг, которые он делает
foreach ($specialists as $key => $specialist) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM service_specialist WHERE specialist_id = ?");
    $stmt->execute([$specialist['id']]);
    $result = $stmt->fetch();
    $specialists[$key]['services_count'] = $result['count'];
}

// Вспомогательная функция для склонения слова "год"
function getYearWord($years) {
    $years = $years % 100;
    if ($years >= 11 && $years <= 19) {
        return 'лет';
    }
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
    <title>Специалисты | ÉLAN Beauty Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/specialists.css">
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

    <section class="page-header">
        <h1>Наши <span>специалисты</span></h1>
        <p>Профессионалы с опытом работы, которые знают о красоте всё</p>
    </section>

    <!-- Фильтр по специализациям -->
    <section class="filter-section">
        <div class="filter-container">
            <button class="filter-btn active" data-filter="all">Все мастера</button>
            <?php foreach (array_keys($groupedSpecialists) as $specialization): ?>
                <button class="filter-btn" data-filter="<?= htmlspecialchars($specialization) ?>">
                    <?= htmlspecialchars($specialization) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Сетка специалистов -->
    <section class="specialists-page">
        <div class="specialists-grid">
            <?php foreach ($specialists as $specialist): ?>
                <div class="specialist-card" data-specializations="<?= htmlspecialchars($specialist['specialization']) ?>">
                    <div class="specialist-image">
                        <?php if ($specialist['photo'] && file_exists($specialist['photo'])): ?>
                            <img src="<?= htmlspecialchars($specialist['photo']) ?>" alt="<?= htmlspecialchars($specialist['name']) ?>">
                        <?php else: ?>
                            <div class="specialist-placeholder">
                                <span><?= mb_substr($specialist['name'], 0, 1) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="specialist-experience">
                            <?= $specialist['experience'] ?> <?= getYearWord($specialist['experience']) ?>
                        </div>
                    </div>
                    <div class="specialist-content">
                        <h3 class="specialist-name"><?= htmlspecialchars($specialist['name']) ?></h3>
                        <p class="specialist-specialization"><?= htmlspecialchars($specialist['specialization']) ?></p>
                        <div class="specialist-stats">
                            <div class="stat">
                                <span class="stat-value"><?= $specialist['services_count'] ?></span>
                                <span class="stat-label">услуг</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">★</span>
                                <span class="stat-label">4.9</span>
                            </div>
                        </div>
                        <div class="specialist-footer">
                            <a href="specialist.php?id=<?= $specialist['id'] ?>" class="specialist-btn">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Секция "Как мы работаем" -->
    <section class="how-we-work">
        <h2>Как мы работаем</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Выберите мастера</h3>
                <p>Ознакомьтесь с портфолио и выберите понравившегося специалиста</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Запишитесь онлайн</h3>
                <p>Выберите удобное время и оставьте заявку на сайте</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Приходите в салон</h3>
                <p>Посетите салон в назначенное время и получите желаемый результат</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Наслаждайтесь результатом</h3>
                <p>Радуйтесь обновлению и возвращайтесь снова</p>
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
    </footer>

    <script src="js/specialists.js"></script>
    <script src="js/common.js"></script>
</body>
</html>