<?php
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Информационная страница</title>
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

        .login-btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 8px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background: var(--hover-color);
            color: var(--white);
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

        p {
            margin: 0 0 15px;
            color: var(--secondary-color);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        ul {
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 10px;
            color: var(--secondary-color);
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

        img {
            max-width: 100%;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
        <div class="logo">Спортивный центр</div>
            <nav>
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="t3/about.php">О нас</a></li>
                    <li><a href="t3/services.php">Услуги</a></li>
                    <li><a href="t3/contact.php">Контакты</a></li>
                    <li><a href="t3/login.php" class="login-btn">Войти</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section>
            <h2>О нашей организации</h2>
            <p>
                Наша организация предоставляет высокий уровень услуг в области спорта. Мы предлагаем обучение плаванию, индивидуальные тренировки
                и спортивные мероприятия для всех возрастов. Присоединяйтесь к нам, чтобы узнать больше о том, как мы можем помочь вам достичь ваших спортивных целей!
            </p>
            <img src="swimming.webp" alt="Спортсмены на тренировке">
        </section>

        <section>
            <h2>Наши преимущества</h2>
            <ul>
                <li>Опытные тренеры с высоким уровнем квалификации</li>
                <li>Индивидуальный подход к каждому клиенту</li>
                <li>Современное оборудование и методики обучения</li>
            </ul>
        </section>

        <section>
            <h2>Контактная информация</h2>
            <p>Вы можете связаться с нами по следующим контактам:</p>
            <p>Телефон: +7 (495) 111-22-33</p>
            <p>Email: info@example.com</p>
            <p>Адрес: ул. Спортивная, 10, Москва</p>
        </section>

        <?php if (isset($poolData)): ?>
            <section>
                <h2>Информация о наших бассейнах</h2>
                <p>Адрес бассейна: <?php echo htmlspecialchars($poolData['Адрес_бассейна']); ?></p>
                <p>Телефон администратора: <?php echo htmlspecialchars($poolData['Телефон_администратора']); ?></p>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; 2025 Спортивный центр. Все права защищены.</p>
            <p><a href="privacy.php">Политика конфиденциальности</a></p>
        </div>
    </footer>
</body>
</html>