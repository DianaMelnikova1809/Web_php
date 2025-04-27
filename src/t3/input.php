<?php
session_start();
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

// Обработка формы входа
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка ввода
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if (empty($login)) {
        $errors['login'] = "Логин обязательное поле.";
    }
    
    if (empty($password)) {
        $errors['password'] = "Пароль обязательное поле.";
    }

    // Если ошибок нет, проверяем данные в базе
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM `Представители` WHERE `Логин` = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        // Сравниваем пароли напрямую (без хэширования)
        if ($user && $password === $user['Пароль']) {
            // Успешный вход, устанавливаем сессию
            $_SESSION['login'] = $login;
            $_SESSION['user_id'] = $user['Код_представителя'];
            header("Location: dashboard.php");
            exit();
        } else {
            $errors['general'] = "Неверный логин или пароль.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Вход - Спортивный центр</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Ваши стили остаются без изменений */
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

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .login-form {
            background: var(--white);
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 2rem;
            margin: 0;
            color: var(--dark-color);
            font-weight: 700;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--secondary-color);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .error {
            color: red;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
            display: inline-block;
        }

        .btn:hover {
            background: var(--hover-color);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
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
                <li><a href="input.php" class="login-btn">Вход</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <div class="login-form">
        <h1>Вход</h1>

        <?php if (!empty($errors['general'])): ?>
            <div class="error" style="margin-bottom: 20px; text-align: center;">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="login">Логин *</label>
                <input type="text" id="login" name="login" required 
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                <?php if (!empty($errors['login'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['login']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Пароль *</label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($errors['password'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Войти</button>
            </div>
        </form>

        <div class="login-link">
            <p>Не зарегистрированы? <a href="login.php">Создайте аккаунт</a></p>
        </div>
    </div>
</div>

<footer>
    <div class="footer-container">
        <p>&copy; 2025 Спортивный центр. Все права защищены.</p>
        <p><a href="privacy.php">Политика конфиденциальности</a></p>
    </div>
</footer>

</body>
</html>