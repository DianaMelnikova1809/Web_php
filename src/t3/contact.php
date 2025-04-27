<?php
require_once 'db_connection.php';

try {
    $conn->exec("
        CREATE TABLE IF NOT EXISTS temp_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            message VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4
    ");
} catch (PDOException $e) {
    die("Ошибка создания таблицы: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO temp_notifications (title, message) VALUES (:title, :message)");
        $stmt->execute([
            ':title' => $_POST['title'],
            ':message' => $_POST['message']
        ]);
    }

    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE temp_notifications SET title = :title, message = :message WHERE id = :id");
        $stmt->execute([
            ':id' => $_POST['id'],
            ':title' => $_POST['edit_title'],
            ':message' => $_POST['edit_message']
        ]);
    }

    if (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM temp_notifications WHERE id = :id");
        $stmt->execute([':id' => $_POST['id']]);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Контакты</title>
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
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: var(--dark-color);
            margin: 0 0 20px;
        }

        h1 {
            font-size: 2.5rem;
        }

        h2 {
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid var(--gray);
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="email"]:focus {
            border: 1px solid var(--primary-color);
            outline: none;
        }

        button {
            background: var(--primary-color);
            color: var(--white);
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: var(--hover-color);
        }

        footer {
            background: var(--dark-color);
            color: var(--white);
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
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
        <div class="content">
            <h1>Свяжитесь с нами</h1>
            <div class="form-group">
                <h2>Отправить сообщение</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="title">Ваше имя:</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Ваш email:</label>
                        <input type="text" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Сообщение:</label>
                        <input type="text" name="message" required>
                    </div>
                    <button type="submit" name="add">Отправить</button>
                </form>
            </div>
            <!-- Таблица с данными (удалена) -->
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <p>&copy; <?= date("Y"); ?> Спортивный центр. Все права защищены.</p>
            <a href="#">Политика конфиденциальности</a>
        </div>
    </footer>

    <script>
        // JavaScript код можно оставить, если необходимо добавление функционала
    </script>
</body>
</html>
