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

// Обработка формы регистрации
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация данных
    $requiredFields = [
        'last_name' => 'Фамилия',
        'first_name' => 'Имя',
        'phone' => 'Телефон',
        'address' => 'Адрес проживания',
        'email' => 'Электронная почта',
        'login' => 'Логин',
        'password' => 'Пароль',
        'confirm_password' => 'Подтверждение пароля'
    ];

    foreach ($requiredFields as $field => $name) {
        if (empty($_POST[$field])) {
            $errors[$field] = "Поле '$name' обязательно для заполнения";
        }
    }

    // Проверка email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Некорректный формат электронной почты";
    }

    // Проверка паролей
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors['confirm_password'] = "Пароли не совпадают";
    }

    // Проверка уникальности логина и email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Представители` WHERE `Логин` = ? OR `Электронная_почта` = ?");
        $stmt->execute([$_POST['login'], $_POST['email']]);
        if ($stmt->fetchColumn() > 0) {
            $errors['general'] = "Пользователь с таким логином или email уже существует";
        }
    }

    // Если ошибок нет, регистрируем
    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Регистрация представителя
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO `Представители` 
                (`Фамилия`, `Имя`, `Отчество`, `Телефон`, `Адрес_проживания`, `Электронная_почта`, `Логин`, `Пароль`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['last_name'],
                $_POST['first_name'],
                $_POST['middle_name'] ?? null,
                $_POST['phone'],
                $_POST['address'],
                $_POST['email'],
                $_POST['login'],
                $hashedPassword
            ]);

            $conn->commit();

            // Редирект на страницу успешной регистрации
            header("Location: registration_success.php");
            exit();

        } catch (Exception $e) {
            $conn->rollBack();
            $errors['general'] = "Ошибка при регистрации: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Регистрация - Спортивный центр</title>
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

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .registration-form {
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
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .success-message {
            color: var(--success-color);
            background-color: rgba(40, 167, 69, 0.1);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
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
            text-decoration: none;
        }

        .btn:hover {
            background: var(--hover-color);
        }

        .btn-block {
            display: block;
            width: 100%;
            padding: 12px;
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

<div class="container">
    <div class="registration-form">
        <h1>Регистрация представителя</h1>

        <?php if ($success): ?>
            <div class="success-message">
                Регистрация успешно завершена! Теперь вы можете <a href="login.php">войти в систему</a>.
            </div>
        <?php elseif (!empty($errors['general'])): ?>
            <div class="error" style="margin-bottom: 20px; text-align: center;">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="">
            <h2>Данные представителя</h2>

            <div class="form-group">
                <label for="last_name">Фамилия *</label>
                <input type="text" id="last_name" name="last_name" 
                    value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                <?php if (!empty($errors['last_name'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['last_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="first_name">Имя *</label>
                <input type="text" id="first_name" name="first_name" 
                    value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                <?php if (!empty($errors['first_name'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['first_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="middle_name">Отчество</label>
                <input type="text" id="middle_name" name="middle_name" 
                    value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="phone">Телефон *</label>
                <input type="tel" id="phone" name="phone" 
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="address">Адрес проживания *</label>
                <input type="text" id="address" name="address" 
                    value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
                <?php if (!empty($errors['address'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['address']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Электронная почта *</label>
                <input type="email" id="email" name="email" 
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="login">Логин *</label>
                <input type="text" id="login" name="login" 
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
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
                <label for="confirm_password">Подтверждение пароля *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-block">Зарегистрироваться</button>
            </div>
        </form>

        <div class="login-link">
            Уже зарегистрированы? <a href="input.php">Войдите в систему</a>
        </div>
        <?php endif; ?>
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
