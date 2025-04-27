<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Регистрация завершена - Спортивный центр</title>
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
            background-color: var(--light-color);
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

        .success-message {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--dark-color);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.2rem;
            margin: 20px 0;
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
                <li><a href="input.php" class="login-btn">Войти</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h1>Регистрация завершена!</h1>
    <div class="success-message">
        Регистрация успешно завершена! Теперь вы можете <a href="input.php">войти в систему</a>.
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
