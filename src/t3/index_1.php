<?php
require_once 'db_connection.php';

// Статистика для главной страницы
$stats = [
    'sportsmen' => $conn->query("SELECT COUNT(*) FROM Спортсмены")->fetchColumn(),
    'trainers' => $conn->query("SELECT COUNT(*) FROM Тренеры")->fetchColumn(),
    'groups' => $conn->query("SELECT COUNT(*) FROM Группы")->fetchColumn(),
    'workouts' => $conn->query("SELECT COUNT(*) FROM Тренировки")->fetchColumn(),
    'pools' => $conn->query("SELECT COUNT(*) FROM Бассейны")->fetchColumn()
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Система управления бассейном</title>
    <meta charset="utf-8">
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #ff6b6b;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7ff;
            color: #333;
            line-height: 1.6;
        }

        .menu {
            background: white;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .menu a {
            display: inline-block;
            padding: 12px 20px;
            text-decoration: none;
            color: var(--dark-color);
            border-radius: 50px;
            background: linear-gradient(to right, #f5f7ff, #e8ecff);
            font-weight: 600;
            transition: var(--transition);
            border: 1px solid rgba(74, 107, 255, 0.2);
        }

        .menu a:hover {
            background: linear-gradient(to right, var(--primary-color), #6a8bff);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(74, 107, 255, 0.2);
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }

        h1, h2, h3 {
            color: var(--dark-color);
            margin-top: 0;
        }

        h1 {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 25px;
            display: inline-block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 2.5em;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .stat-label {
            color: var(--dark-color);
            font-size: 1.1em;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content {
            animation: fadeIn 0.5s ease-out;
        }

        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 10px;
            }
            
            .menu a {
                width: 100%;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="menu">
        <a href="index.php">Главная</a>
        <a href="sportsmen.php">Спортсмены</a>
        <a href="trainers.php">Тренеры</a>
        <a href="workouts.php">Тренировки</a>
        <a href="groups.php">Группы</a>
        <a href="representatives.php">Представители</a>
        <a href="pools.php">Бассейны</a>
        <a href="notifications.php">Уведомления</a>
    </div>
    
    <div class="content">
        <h1>Добро пожаловать в систему управления бассейном</h1>
        <p>Выберите нужный раздел в меню выше.</p>
        
        <h2>Статистика бассейна:</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['sportsmen'] ?></div>
                <div class="stat-label">Спортсменов</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['trainers'] ?></div>
                <div class="stat-label">Тренеров</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['groups'] ?></div>
                <div class="stat-label">Групп</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['workouts'] ?></div>
                <div class="stat-label">Тренировок</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['pools'] ?></div>
                <div class="stat-label">Бассейнов</div>
            </div>
        </div>
    </div>
</body>
</html>