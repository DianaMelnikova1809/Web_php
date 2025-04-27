<?php
session_start();

// Настройки подключения к базе данных
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

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получаем информацию о текущем представителе
$stmt = $conn->prepare("SELECT * FROM `Представители` WHERE `Код_представителя` = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentRepresentative = $stmt->fetch();

if (!$currentRepresentative) {
    die("Пользователь не найден.");
}

// Получаем всех спортсменов текущего представителя
$stmt = $conn->prepare("SELECT * FROM `Спортсмены` WHERE `Код_представителя` = ?");
$stmt->execute([$currentRepresentative['Код_представителя']]);
$children = $stmt->fetchAll();

// Получаем информацию о группах, в которых уже занимаются дети текущего представителя
$childrenGroups = [];
if (!empty($children)) {
    $childIds = array_column($children, 'Код_спортсмена');
    $placeholders = implode(',', array_fill(0, count($childIds), '?'));

    $stmt = $conn->prepare("
        SELECT g.Код_группы, g.Код_бассейна, b.Адрес_бассейна, s.Фамилия, s.Имя, s.Дата_рождения 
        FROM Спортсмены s
        JOIN Группы g ON s.Код_группы = g.Код_группы
        JOIN Бассейны b ON g.Код_бассейна = b.Код_бассейна
        WHERE s.Код_спортсмена IN ($placeholders)
    ");
    $stmt->execute($childIds);
    $childrenGroups = $stmt->fetchAll();
}

// Определение расписания групп
$groupSchedule = [
    1 => ['description' => 'Дети 5-6 лет', 'days' => ['Пн' => '16:00 - 17:00', 'Вт' => '16:00 - 17:00', 'Ср' => '16:00 - 17:00'], 'pool_id' => 1],
    2 => ['description' => 'Дети 7-8 лет', 'days' => ['Пн' => '17:15 - 18:15', 'Ср' => '17:15 - 18:15', 'Пт' => '17:15 - 18:15'], 'pool_id' => 1],
    3 => ['description' => 'Подростки 9-12 лет', 'days' => ['Вт' => '18:00 - 19:00', 'Чт' => '18:00 - 19:00'], 'pool_id' => 1],
    4 => ['description' => 'Подростки 13-17 лет', 'days' => ['Сб' => '10:00 - 11:00', 'Пт' => '18:00 - 19:00'], 'pool_id' => 1],
    5 => ['description' => 'Дети 5-6 лет', 'days' => ['Пн' => '16:00 - 17:00', 'Вт' => '16:00 - 17:00', 'Ср' => '16:00 - 17:00'], 'pool_id' => 2],
    6 => ['description' => 'Дети 7-8 лет', 'days' => ['Пн' => '17:15 - 18:15', 'Ср' => '17:15 - 18:15', 'Пт' => '17:15 - 18:15'], 'pool_id' => 2],
    7 => ['description' => 'Подростки 9-12 лет', 'days' => ['Вт' => '18:00 - 19:00', 'Чт' => '18:00 - 19:00'], 'pool_id' => 2],
    8 => ['description' => 'Подростки 13-17 лет', 'days' => ['Сб' => '10:00 - 11:00', 'Пт' => '18:00 - 19:00'], 'pool_id' => 2],
    9 => ['description' => 'Дети 5-6 лет', 'days' => ['Пн' => '16:00 - 17:00', 'Вт' => '16:00 - 17:00', 'Ср' => '16:00 - 17:00'], 'pool_id' => 3],
    10 => ['description' => 'Дети 7-8 лет', 'days' => ['Пн' => '17:15 - 18:15', 'Ср' => '17:15 - 18:15', 'Пт' => '17:15 - 18:15'], 'pool_id' => 3],
    11 => ['description' => 'Подростки 9-12 лет', 'days' => ['Вт' => '18:00 - 19:00', 'Чт' => '18:00 - 19:00'], 'pool_id' => 3],
    12 => ['description' => 'Подростки 13-17 лет', 'days' => ['Сб' => '10:00 - 11:00', 'Пт' => '18:00 - 19:00'], 'pool_id' => 3],
];


// Получаем список бассейнов
$stmt = $conn->query("SELECT Код_бассейна, Адрес_бассейна FROM `Бассейны`");
$pools = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка записи ребенка в группу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_child'])) {
    $child_id = $_POST['child_id'] ?? null;
    $group_id = $_POST['group_id'] ?? null;
    $pool_id = $_POST['pool_id'] ?? null;
    $errors = [];

    if (!$child_id) {
        $errors[] = "Не выбран ребенок.";
    }
    if (!$group_id) {
        $errors[] = "Не выбрана группа.";
    }
    if (!$pool_id) {
        $errors[] = "Не выбран бассейн.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE `Спортсмены` SET `Код_группы` = ? WHERE `Код_спортсмена` = ? AND `Код_представителя` = ?");
            $stmt->execute([$group_id, $child_id, $currentRepresentative['Код_представителя']]);
            $successMessage = "Ребенок успешно записан в группу!";
        } catch (PDOException $e) {
            $errors[] = "Ошибка при записи в группу: " . $e->getMessage();
        }
    }
}

// Обработка добавления нового ребенка
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_child'])) {
    $errors = [];
    $requiredFields = ['child_last_name', 'child_first_name', 'child_birth_date'];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = "Поле '$field' обязательно для заполнения.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO `Спортсмены` 
                (`Фамилия`, `Имя`, `Дата_рождения`, `Код_представителя`) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['child_last_name'],
                $_POST['child_first_name'],
                $_POST['child_birth_date'],
                $currentRepresentative['Код_представителя']
            ]);
            $successMessage = "Ребенок успешно добавлен!";
        } catch (PDOException $e) {
            $errors['general'] = "Ошибка при добавлении ребенка: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Личный кабинет - Спортивный центр</title>
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

        nav ul li a:hover {
            color: var(--primary-color);
        }

        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        section {
            background: var(--white);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 2.5rem;
            margin: 0 0 20px;
            color: var(--dark-color);
            font-weight: 700;
        }

        h2 {
            font-size: 1.8rem;
            margin: 0 0 20px;
            color: var(--dark-color);
            font-weight: 600;
        }

        .children-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }

        .child-card {
            flex: 1 1 calc(25% - 20px); /* 4 карточки в ряд с отступом */
            background: var(--light-color);
            border: 1px solid var(--gray);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        p {
            margin: 0 0 15px;
            color: var(--secondary-color);
            font-size: 1.1rem;
            line-height: 1.7;
        }
        /* Стили для выпадающих списков */
        select {
            width: 100%; /* Занимает всю ширину родительского контейнера */
            padding: 10px; /* Отступы внутри выпадающего списка */
            border: 1px solid var(--gray); /* Цвет границы */
            border-radius: 5px; /* Скругление углов */
            font-size: 1rem; /* Размер шрифта */
            color: var(--dark-color); /* Цвет текста */
            background-color: var(--light-color); /* Цвет фона */
            appearance: none; /* Убираем стандартный стиль */
            outline: none; /* Убираем обводку при фокусировке */
            transition: border-color 0.3s; /* Плавный переход для изменения цвета границы */
        }

        /* Стиль для кастомной стрелки */
        select:focus {
            border-color: var(--primary-color); /* Цвет границы при фокусировке */
        }

        /* Стиль заголовков для выпадающих списков */
        label {
            font-weight: bold; /* Жирное начертание */
            margin-bottom: 5px; /* Отступ снизу для разделения с выпадающим списком */
            display: block; /* Делаем label блочным элементом */
        }

        /* Общие стили для формы */
        .form-group {
            margin-bottom: 20px; /* Отступ между элементами формы */
        }


        .btn {
            color: var(--white);
            background: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-block;
            margin-top: 15px;
        }

        .btn:hover {
            background: var(--hover-color);
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

        /* Модальное окно */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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

            .child-card {
                flex: 1 1 calc(50% - 20px); /* 2 карточки в ряд на мобильных */
            }
        }
    </style>
    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Отменяем действие по умолчанию (переход по ссылке)
            if (confirm("Вы уверены, что хотите выйти?")) {
                window.location.href = "../"; // Переход на главную страницу при подтверждении
            }
        }

        function updateGroups() {
            const poolSelect = document.getElementById('pool_id');
            const groupSelect = document.getElementById('group_id');
            const selectedPool = poolSelect.value;
            const groups = <?= json_encode($groupSchedule) ?>;

            // Очистить текущие группы
            groupSelect.innerHTML = '<option value="">-- Выберите группу --</option>';

            // Заполнить доступные группы для выбранного бассейна
            for (const [groupId, groupInfo] of Object.entries(groups)) {
                if (groupInfo.pool_id == selectedPool) {
                    const option = document.createElement('option');
                    option.value = groupId;
                    option.textContent = `Группа №${groupId} (${groupInfo.description})`;
                    groupSelect.appendChild(option);
                }
            }
        }

        // Модальное окно для добавления нового ребенка
        function openModal() {
            document.getElementById('modal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('modal').style.display = "none";
        }
    </script>
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">Спортивный центр</div>
        <nav>
            <ul>
                <li><a href="index_new.php">Главная</a></li>
                <li><a href="about_new.php">О нас</a></li>
                <li><a href="services_new.php">Услуги</a></li>
                <li><a href="contact.php">Контакты</a></li>
                <li><a href="../" class="login-btn" onclick="confirmLogout(event)">Выйти</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
<h1>Текущие записи детей</h1>
    <section>
        <div class="children-list">
            <?php if (empty($childrenGroups)): ?>
                <p>Ваши дети пока не записаны ни в одну группу.</p>
            <?php else: ?>
                <?php foreach ($childrenGroups as $group): ?>
                    <div class="child-card">
                        <h3>Группа №<?= htmlspecialchars($group['Код_группы']) ?></h3>
                        <p><strong>Бассейн:</strong> <?= htmlspecialchars($group['Адрес_бассейна']) ?></p>
                        <p><strong>Ребенок:</strong> <?= htmlspecialchars($group['Фамилия'] . ' ' . $group['Имя']) ?></p>
                        <p><strong>Дата рождения:</strong> <?= date('d.m.Y', strtotime($group['Дата_рождения'])) ?></p>
                        <p><strong>Расписание группы:</strong></p>
                        <ul>
                            <?php 
                            $groupId = $group['Код_группы'];
                            if (isset($groupSchedule[$groupId])) {
                                foreach ($groupSchedule[$groupId]['days'] as $day => $time) {
                                    echo "<li>" . htmlspecialchars($day) . ": " . htmlspecialchars($time) . "</li>";
                                }
                            } 
                            ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section>
        <h2>Записать ребенка в группу</h2>
        <?php if (!empty($successMessage)): ?>
            <p><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="child_id">Выберите ребенка *</label>
                <select id="child_id" name="child_id" required>
                    <option value="">-- Выберите ребенка --</option>
                    <?php foreach ($children as $child): ?>
                        <option value="<?= htmlspecialchars($child['Код_спортсмена']) ?>">
                            <?= htmlspecialchars($child['Фамилия'] . ' ' . $child['Имя']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pool_id">Выберите бассейн *</label>
                <select id="pool_id" name="pool_id" required onchange="updateGroups()">
                    <option value="">-- Выберите бассейн --</option>
                    <?php foreach ($pools as $pool): ?>
                        <option value="<?= htmlspecialchars($pool['Код_бассейна']) ?>">
                            <?= htmlspecialchars($pool['Адрес_бассейна']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="group_id">Выберите группу *</label>
                <select id="group_id" name="group_id" required>
                    <option value="">-- Выберите группу --</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="enroll_child" class="btn">Записать в группу</button>
            </div>
            <a href="m-m.php" >Запись на дополнительную тренировку</a>
        </form>
        <button class="btn" onclick="openModal()">Добавить нового ребенка</button>
    </section>

    <!-- Модальное окно для добавления нового ребенка -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Добавить нового ребенка</h2>
            <?php if (!empty($successMessage)): ?>
                <p><?= htmlspecialchars($successMessage) ?></p>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="child_last_name">Фамилия *</label>
                    <input type="text" id="child_last_name" name="child_last_name" required>
                </div>
                <div class="form-group">
                    <label for="child_first_name">Имя *</label>
                    <input type="text" id="child_first_name" name="child_first_name" required>
                </div>
                <div class="form-group">
                    <label for="child_birth_date">Дата рождения *</label>
                    <input type="date" id="child_birth_date" name="child_birth_date" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_child" class="btn">Добавить ребенка</button>
                </div>
            </form>
        </div>
    </div>
</main>

<footer>
    <div class="footer-container">
        <p>&copy; <?= date("Y"); ?> Спортивный центр. Все права защищены.</p>
        <p><a href="privacy.php">Политика конфиденциальности</a></p>
    </div>
</footer>

</body>
</html>
