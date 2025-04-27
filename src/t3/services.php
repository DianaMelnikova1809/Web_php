<?php

$servername = "mysql";
$username = "root";
$password = "root";
$dbname = "Бассейн";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Получаем информацию о тренерах из базы данных
$stmtCoaches = $conn->query("SELECT * FROM Тренеры");
$coaches = $stmtCoaches->fetchAll();

// Индексируем тренеров по коду для быстрого доступа
$trainers = [];
foreach ($coaches as $coach) {
    $fullName = htmlspecialchars($coach['Фамилия'] . ' ' . $coach['Имя']);
    $trainers[$coach['Код_тренера']] = $fullName; 
}

// Получаем адреса бассейнов из базы данных
$stmtPools = $conn->query("SELECT Код_бассейна, Адрес_бассейна FROM Бассейны");
$pools = $stmtPools->fetchAll(PDO::FETCH_ASSOC);
$poolAddresses = [];
foreach ($pools as $pool) {
    $poolAddresses[$pool['Код_бассейна']] = htmlspecialchars($pool['Адрес_бассейна']);
}

// Статическое расписание для бассейнов
$schedule = [
    1 => [
        'Дети 5-6 лет' => [
            'days' => ['Пн' => '16:00 - 17:00', 'Вт' => '16:00 - 17:00', 'Ср' => '16:00 - 17:00'],
            'Код_тренера' => 1
        ],
        'Дети 7-8 лет' => [
            'days' => ['Пн' => '17:15 - 18:15', 'Ср' => '17:15 - 18:15', 'Пт' => '17:15 - 18:15'],
            'Код_тренера' => 1
        ],
        'Подростки 9-12 лет' => [
            'days' => ['Вт' => '18:00 - 19:00', 'Чт' => '18:00 - 19:00'],
            'Код_тренера' => 1
        ],
        'Подростки 13-17 лет' => [
            'days' => ['Сб' => '10:00 - 11:00', 'Пт' => '18:00 - 19:00'],
            'Код_тренера' => 1
        ],
    ],
    2 => [
        'Дети 5-6 лет' => [
            'days' => ['Пн' => '16:00 - 17:00', 'Вт' => '16:00 - 17:00', 'Ср' => '16:00 - 17:00'],
            'Код_тренера' => 2
        ],
        'Дети 7-8 лет' => [
            'days' => ['Пн' => '17:15 - 18:15', 'Ср' => '17:15 - 18:15', 'Пт' => '17:15 - 18:15'],
            'Код_тренера' => 2
        ],
        'Подростки 9-12 лет' => [
            'days' => ['Вт' => '18:00 - 19:00', 'Чт' => '18:00 - 19:00'],
            'Код_тренера' => 2
        ],
        'Подростки 13-17 лет' => [
            'days' => ['Сб' => '10:00 - 11:00', 'Пт' => '18:00 - 19:00'],
            'Код_тренера' => 2
        ],
    ],
    3 => [
        'Дети 5-6 лет' => [
            'days' => ['Пн' => '16:00 - 17:00', 'Вт' => '16:00 - 17:00', 'Ср' => '16:00 - 17:00'],
            'Код_тренера' => 3
        ],
        'Дети 7-8 лет' => [
            'days' => ['Пн' => '17:15 - 18:15', 'Ср' => '17:15 - 18:15', 'Пт' => '17:15 - 18:15'],
            'Код_тренера' => 3
        ],
        'Подростки 9-12 лет' => [
            'days' => ['Вт' => '18:00 - 19:00', 'Чт' => '18:00 - 19:00'],
            'Код_тренера' => 3
        ],
        'Подростки 13-17 лет' => [
            'days' => ['Сб' => '10:00 - 11:00', 'Пт' => '18:00 - 19:00'],
            'Код_тренера' => 3
        ],
    ]
];

// Обработка фильтров
$selectedPool = $_GET['pool'] ?? null;
$selectedAge = $_GET['age'] ?? null;

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Услуги</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --white: #ffffff;
            --gray: #e9ecef;
            --hover-color: #0b5ed7;
            --pool-title-color: #007bff;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: var(--white);
            color: var(--dark-color);
        }

        header {
            background: var(--white);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 5px 0;
            position: relative;
        }

        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--dark-color);
            font-weight: 700;
        }

        h2 {
            font-size: 1.8rem;
            margin: 20px 0 10px;
            color: var(--dark-color);
            font-weight: 600;
        }

        .pool-title {
            color: var(--pool-title-color);
            font-size: 1.5rem;
        }

        .schedule-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .schedule-item {
            background-color: var(--light-color);
            border: 1px solid var(--gray);
            border-radius: 5px;
            padding: 10px;
            width: calc(23% - 20px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            margin-bottom: 15px;
        }

        p {
            margin: 0 0 15px;
            color: var(--secondary-color);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        footer {
            background: var(--dark-color);
            color: var(--white);
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        footer a {
            color: var(--white);
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        /* Стили для фильтров */
        .filters {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .filter-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--gray);
            border-radius: 4px;
            font-size: 16px;
        }

        .filter-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .filter-button:hover {
            background-color: var(--hover-color);
        }

        .reset-button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-left: 10px;
        }

        .reset-button:hover {
            background-color: #5a6268;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                flex-direction: column;
                margin-top: 15px;
            }

            nav ul li {
                margin: 10px 0;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .schedule-item {
                width: calc(100% - 20px);
            }
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">Спортивный центр</div>
        <nav>
            <ul>
                <li><a href="../">Главная</a></li>
                <li><a href="about.php">О нас</a></li>
                <li><a href="services.php">Услуги</a></li>
                <li><a href="contact.php">Контакты</a></li>
                <li><a href="login.php" class="login-btn">Войти</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <h1>Наши Услуги</h1>

        <!-- Форма фильтров -->
        <div class="filters">
            <form method="GET" action="">
                <div class="filter-group">
                    <label for="pool">Выберите бассейн:</label>
                    <select id="pool" name="pool">
                        <option value="">Все бассейны</option>
                        <?php foreach ($poolAddresses as $code => $address): ?>
                            <option value="<?= $code ?>" <?= ($selectedPool == $code) ? 'selected' : '' ?>>
                                <?= $address ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="age">Выберите возрастную группу:</label>
                    <select id="age" name="age">
                        <option value="">Все возрастные группы</option>
                        <option value="5-6" <?= ($selectedAge == '5-6') ? 'selected' : '' ?>>Дети 5-6 лет</option>
                        <option value="7-8" <?= ($selectedAge == '7-8') ? 'selected' : '' ?>>Дети 7-8 лет</option>
                        <option value="9-12" <?= ($selectedAge == '9-12') ? 'selected' : '' ?>>Подростки 9-12 лет</option>
                        <option value="13-17" <?= ($selectedAge == '13-17') ? 'selected' : '' ?>>Подростки 13-17 лет</option>
                    </select>
                </div>
                
                <button type="submit" class="filter-button">Применить фильтры</button>
                <a href="services.php" class="reset-button">Сбросить</a>
            </form>
        </div>

        <section>
            <h2>Расписание Тренировок</h2>
            <?php 
            foreach ($schedule as $poolCode => $groups): 
                // Пропускаем бассейны, которые не соответствуют фильтру
                if ($selectedPool && $selectedPool != $poolCode) continue;
                
                $hasGroups = false;
                
                // Проверяем, есть ли группы, соответствующие фильтру возраста
                foreach ($groups as $group => $info) {
                    $ageMatches = true;
                    if ($selectedAge) {
                        $ageRange = '';
                        if (strpos($group, '5-6') !== false) $ageRange = '5-6';
                        elseif (strpos($group, '7-8') !== false) $ageRange = '7-8';
                        elseif (strpos($group, '9-12') !== false) $ageRange = '9-12';
                        elseif (strpos($group, '13-17') !== false) $ageRange = '13-17';
                        
                        if ($ageRange != $selectedAge) continue;
                    }
                    $hasGroups = true;
                    break;
                }
                
                if (!$hasGroups) continue;
            ?>
                <h3 class="pool-title"><?php echo "Бассейн на " . ($poolAddresses[$poolCode] ?? 'адрес не указан'); ?></h3>
                <div class="schedule-group">
                    <?php foreach ($groups as $group => $info): 
                        // Проверяем фильтр по возрасту
                        if ($selectedAge) {
                            $ageRange = '';
                            if (strpos($group, '5-6') !== false) $ageRange = '5-6';
                            elseif (strpos($group, '7-8') !== false) $ageRange = '7-8';
                            elseif (strpos($group, '9-12') !== false) $ageRange = '9-12';
                            elseif (strpos($group, '13-17') !== false) $ageRange = '13-17';
                            
                            if ($ageRange != $selectedAge) continue;
                        }
                    ?>
                        <div class="schedule-item">
                            <p><strong><?php echo htmlspecialchars($group); ?>:</strong></p>
                            <div>
                                <?php foreach ($info['days'] as $day => $time): ?>
                                    <p><?php echo "$day: $time"; ?></p>
                                <?php endforeach; ?>
                            </div>
                            <p><strong>Тренер:</strong> <?php echo $trainers[$info['Код_тренера']] ?? 'Информация о тренере отсутствует'; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
            <?php endforeach; ?>
            
            <?php 
            // Сообщение, если ничего не найдено
            $hasResults = false;
            foreach ($schedule as $poolCode => $groups) {
                if ($selectedPool && $selectedPool != $poolCode) continue;
                
                foreach ($groups as $group => $info) {
                    if ($selectedAge) {
                        $ageRange = '';
                        if (strpos($group, '5-6') !== false) $ageRange = '5-6';
                        elseif (strpos($group, '7-8') !== false) $ageRange = '7-8';
                        elseif (strpos($group, '9-12') !== false) $ageRange = '9-12';
                        elseif (strpos($group, '13-17') !== false) $ageRange = '13-17';
                        
                        if ($ageRange != $selectedAge) continue;
                    }
                    $hasResults = true;
                    break;
                }
                if ($hasResults) break;
            }
            
            if (!$hasResults): ?>
                <p>По выбранным критериям ничего не найдено. Попробуйте изменить параметры фильтрации.</p>
            <?php endif; ?>
        </section>
    </div>
</main>

<footer>
    <div class="footer-container">
        <p>&copy; <?= date("Y"); ?> Спортивный центр. Все права защищены.</p>
        <a href="#">Политика конфиденциальности</a>
    </div>
</footer>

</body>
</html>