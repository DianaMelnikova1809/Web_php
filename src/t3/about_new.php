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

// Получаем информацию о бассейнах
$stmtPools = $conn->query("SELECT * FROM Бассейны");
$pools = $stmtPools->fetchAll();

// Получаем информацию о тренерах
$stmtCoaches = $conn->query("SELECT * FROM Тренеры");
$coaches = $stmtCoaches->fetchAll();

// Массив с изображениями тренеров
$coachImages = [
    'Иванов Петр' => 'ivanov.png',
    'Смирнова Ольга' => 'smirnova.jpg',
    'Кузнецов Алексей' => 'kuznetsov.png',
];

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <title>О нас</title>
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

        .carousel {
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .carousel ul {
            display: flex;
            padding: 0;
            margin: 0;
            transition: transform 0.5s ease;
        }

        .carousel li {
            list-style: none;
            padding: 10px;
        }

        .carousel-controls {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }

        .carousel-button {
            background: rgba(255, 255, 255, 0.5);
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
            width: 50px; /* Ширина кнопки */
            height: 50px; /* Высота кнопки */
        }

        .carousel-button:hover {
            background: rgba(255, 255, 255, 0.8);
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
                <li><a href="index_new.php">Главная</a></li>
                <li><a href="about_new.php">О нас</a></li>
                <li><a href="services_new.php">Услуги</a></li>
                <li><a href="contact_new.php">Контакты</a></li>
                <li><a href="dashboard.php" class="login-btn">Личный кабинет</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <h1>О нас</h1>

        <section>
            <h2>Наши бассейны</h2>
            <?php foreach ($pools as $pool): ?>
                <div class="item">
                    <h3><?php echo htmlspecialchars($pool['Адрес_бассейна']); ?></h3>
                    <p>Телефон администратора: <?php echo htmlspecialchars($pool['Телефон_администратора']); ?></p>
                </div>
            <?php endforeach; ?>
        </section>

        <section>
            <h2>Наши тренеры</h2>
            <?php foreach ($coaches as $coach): ?>
                <div class="item" style="display: flex; align-items: center;">
                    <?php
                    $fullName = htmlspecialchars($coach['Фамилия'] . ' ' . $coach['Имя']);
                    $imageSrc = 'images/default_coach.jpg';
                    if (array_key_exists($fullName, $coachImages)) {
                        $imageSrc = $coachImages[$fullName];
                    }
                    ?>
                    <img src="<?php echo $imageSrc; ?>" alt="Фото <?php echo $fullName; ?>" class="coach-photo">
                    <div>
                        <h3><?php echo $fullName; ?></h3>
                        <p>Телефон: <?php echo htmlspecialchars($coach['Телефон']); ?></p>
                        <p>Email: <?php echo htmlspecialchars($coach['Электронная_почта']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section>
            <h2>Галерея</h2>
            <div class="carousel">
                <ul id="imageCarousel">
                    <li><img src="photo1.jpg" alt="Описание изображения 1"></li>
                    <li><img src="photo2.jpg" alt="Описание изображения 2"></li>
                    <li><img src="photo3.jpg" alt="Описание изображения 3"></li>
                    <li><img src="photo4.jpg" alt="Описание изображения 4"></li>
                    <li><img src="photo5.jpg" alt="Описание изображения 5"></li>
                </ul>
                <div class="carousel-controls">
                    <button class="carousel-button" onclick="moveSlide(-1)">&#10094;</button>
                    <button class="carousel-button" onclick="moveSlide(1)">&#10095;</button>
                </div>
            </div>
        </section>

    </div>
</main>

<footer>
    <div class="footer-container">
        <p>&copy; <?= date("Y"); ?> Спортивный центр. Все права защищены.</p>
        <a href="#">Политика конфиденциальности</a>
    </div>
</footer>

<script>
    let currentIndex = 0;

    function showSlides() {
        const slides = document.querySelectorAll('#imageCarousel li');
        slides.forEach((slide, index) => {
            slide.style.display = (index === currentIndex) ? 'block' : 'none';
        });
    }

    function moveSlide(direction) {
        const slides = document.querySelectorAll('#imageCarousel li');
        currentIndex = (currentIndex + direction + slides.length) % slides.length;
        showSlides();
    }

    document.addEventListener('DOMContentLoaded', showSlides);
</script>

</body>

</html>
