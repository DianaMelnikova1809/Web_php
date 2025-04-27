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

// Статические тренировки на неделю вперед
$trainings = [
    6 => ['Описание' => 'Тренировка для детей 5-6 лет', 'Дни_и_время' => 'Пн, Ср, Пт 16:00 - 17:00'],
    7 => ['Описание' => 'Тренировка для детей 7-8 лет', 'Дни_и_время' => 'Вт, Чт 17:15 - 18:15'],
    8 => ['Описание' => 'Подростковая тренировка 9-12 лет', 'Дни_и_время' => 'Вт, Чт 18:00 - 19:00'],
    5 => ['Описание' => 'Подростковая тренировка 13-17 лет', 'Дни_и_время' => 'Сб 10:00 - 11:00'],
    4 => ['Описание' => 'Тренировка для детей 5-6 лет (Дополнительная)', 'Дни_и_время' => 'Сб 12:00 - 13:00'],
    3 => ['Описание' => 'Тренировка для детей 7-8 лет (Дополнительная)', 'Дни_и_время' => 'Сб 13:15 - 14:15'],
];

$successMessage = "";
$errors = [];

// Обработка записи на дополнительную тренировку
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_additional_training'])) {
    $selected_children = $_POST['selected_children'] ?? []; // Массив с выбранными детьми
    $training_id = $_POST['training_id'] ?? null; // ID тренировки

    if (empty($selected_children)) {
        $errors[] = "Выберите хотя бы одного ребенка.";
    }

    if (empty($training_id) || !isset($trainings[$training_id])) {
        $errors[] = "Не выбрана тренировка.";
    }

    if (empty($errors)) {
        try {
            // Вставляем данные в таблицу Спортсмены_Тренировки
            $stmt = $conn->prepare("INSERT INTO `Спортсмены_Тренировки` (`Код_спортсмена`, `Код_тренировки`) VALUES (:kode_kod_sport, :kode_training)");
            foreach ($selected_children as $child_id) {
                $stmt->execute([
                    ':kode_kod_sport' => $child_id,
                    ':kode_training' => $training_id
                ]);
            }
            $successMessage = "Ребята успешно записаны на дополнительную тренировку!";
        } catch (PDOException $e) {
            $errors[] = "Ошибка при записи на тренировки: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Дополнительные тренировки</title>
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

        nav ul li a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            bottom: 0;
            left: 0;
            transition: width 0.3s;
        }

        nav ul li a:hover:after {
            width: 100%;
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: var(--hover-color);
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .form-group {
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.5rem;
            margin: 0 0 20px;
            color: var(--dark-color);
            font-weight: 700;
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
    <h1>Дополнительные тренировки</h1>

    <?php if (!empty($successMessage)): ?>
        <p class="success"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="training_id">Выберите тренировку *</label>
            <select id="training_id" name="training_id" required>
                <option value="">-- Выберите тренировку --</option>
                <?php foreach ($trainings as $id => $training): ?>
                    <option value="<?= htmlspecialchars($id) ?>">
                        <?= htmlspecialchars($training['Описание']) ?> (<?= htmlspecialchars($training['Дни_и_время']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <h3>Выберите детей для записи:</h3>
            <?php 
            // Получение всех детей текущего представителя
            $stmt = $conn->prepare("SELECT * FROM `Спортсмены` WHERE `Код_представителя` = ?");
            $stmt->execute([$currentRepresentative['Код_представителя']]);
            $children = $stmt->fetchAll();

            foreach ($children as $child): ?>
                <div>
                    <input type="checkbox" id="child_<?= htmlspecialchars($child['Код_спортсмена']) ?>" name="selected_children[]" value="<?= htmlspecialchars($child['Код_спортсмена']) ?>">
                    <label for="child_<?= htmlspecialchars($child['Код_спортсмена']) ?>"><?= htmlspecialchars($child['Фамилия'] . ' ' . $child['Имя']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-group">
            <button type="submit" name="enroll_additional_training" class="btn">Записать на дополнительную тренировку</button>
        </div>
    </form>
</main>

<footer>
    <div class="footer-container">
        <p>&copy; <?= date("Y"); ?> Спортивный центр. Все права защищены.</p>
        <a href="#">Политика конфиденциальности</a>
    </div>
</footer>

</body>
</html>
