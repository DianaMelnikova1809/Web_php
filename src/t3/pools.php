<?php
require_once 't3/db_connection.php';

// Получение параметров сортировки
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'Код_бассейна';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Получение списка бассейнов
$sql = "SELECT b.*, COUNT(g.Код_группы) AS groups_count
        FROM Бассейны b
        LEFT JOIN Группы g ON b.Код_бассейна = g.Код_бассейна
        GROUP BY b.Код_бассейна
        ORDER BY $sort $order";

$stmt = $conn->prepare($sql);
$stmt->execute();
$pools = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Бассейны - Система управления бассейном</title>
    <meta charset="utf-8">
    <style>
        /* Общие стили из предыдущих страниц */
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

        .menu a:hover, .menu a.active {
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

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 25px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        th a {
            text-decoration: none;
            color: white;
        }

        tr:hover {
            background-color: rgba(74, 107, 255, 0.05);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            background: #e6f7ea;
            color: #28a745;
        }

        .pool-info {
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .pool-info h3 {
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .pool-info p {
            margin-bottom: 10px;
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
            
            th, td {
                padding: 10px;
                font-size: 0.9em;
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
        <a href="pools.php" class="active">Бассейны</a>
        <a href="notifications.php">Уведомления</a>
    </div>
    
    <div class="content">
        <h1>Бассейны</h1>
        
        <div class="pool-info">
            <h3>Общая информация</h3>
            <p>Всего бассейнов: <?= count($pools) ?></p>
            <p>Общее количество групп: <?= array_sum(array_column($pools, 'groups_count')) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        <a href="?sort=Код_бассейна&order=<?= $sort === 'Код_бассейна' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">
                            № Бассейна
                        </a>
                    </th>
                    <th>
                        <a href="?sort=Адрес_бассейна&order=<?= $sort === 'Адрес_бассейна' && $order === 'ASC' ? 'DESC' : 'ASC' ?>">
                            Адрес
                        </a>
                    </th>
                    <th>Телефон</th>
                    <th>Кол-во групп</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pools as $pool): ?>
                    <tr>
                        <td><?= htmlspecialchars($pool['Код_бассейна']) ?></td>
                        <td><?= htmlspecialchars($pool['Адрес_бассейна']) ?></td>
                        <td><?= htmlspecialchars($pool['Телефон_администратора']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($pool['groups_count']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>