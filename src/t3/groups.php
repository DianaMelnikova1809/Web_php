<?php

require_once 'db_connection.php';

// Получение параметров сортировки и фильтрации
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'g.Код_группы';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$pool_filter = isset($_GET['pool']) ? $_GET['pool'] : '';

$sql = "SELECT g.*, COUNT(DISTINCT sg.Код_спортсмена) AS actual_count, b.Адрес_бассейна
        FROM Группы g
        LEFT JOIN Спортсмены sg ON g.Код_группы = sg.Код_группы
        LEFT JOIN Бассейны b ON g.Код_бассейна = b.Код_бассейна
        LEFT JOIN Бассейны_Тренеры bt ON b.Код_бассейна = bt.Код_бассейна
        WHERE 1=1";

if (!empty($pool_filter)) {
    $sql .= " AND g.Код_бассейна = :pool";
}

$sql .= " GROUP BY g.Код_группы";

$sql .= " ORDER BY $sort $order";

$stmt = $conn->prepare($sql);

if (!empty($pool_filter)) {
    $stmt->bindParam(':pool', $pool_filter);
}

$stmt->execute();
$groups = $stmt->fetchAll();


// Получение списка бассейнов для фильтра
$pools_sql = "SELECT Код_бассейна, Адрес_бассейна FROM Бассейны ORDER BY Адрес_бассейна";
$pools = $conn->query($pools_sql)->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Группы</title>
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

        .filters {
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .filters select, 
        .filters input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            background-color: white;
            transition: var(--transition);
        }

        .filters select:focus, 
        .filters input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.2);
        }

        button, .button {
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .apply-button {
            background: var(--primary-color);
            color: white;
        }

        .apply-button:hover {
            background: #3a5bef;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(74, 107, 255, 0.3);
        }

        .reset-button {
            background: var(--light-color);
            color: var(--dark-color);
            border: 1px solid #ddd;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .reset-button:hover {
            background: #e2e6ea;
        }
        .filter-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .count-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            background: #e6f7ea;
            color: #28a745;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content {
            animation: fadeIn 0.5s ease-out;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                gap: 15px;
            }
            
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
        <a href="pools.php">Бассейны</a>
        <a href="notifications.php">Уведомления</a>
    </div>
    
    <div class="content">
        <h1>Группы бассейна</h1>

        <div class="filters">
            <form method="GET">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Бассейн:</label>
                        <select name="pool">
                            <option value="">Все бассейны</option>
                            <?php foreach ($pools as $pool): ?>
                                <option value="<?= $pool['Код_бассейна'] ?>"
                                        <?= $pool_filter == $pool['Код_бассейна'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pool['Адрес_бассейна']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="apply-button">Применить фильтры</button>
                    <a href="groups.php" class="reset-button">Сбросить</a>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        <a href="?sort=Код_группы&order=<?= $sort === 'g.Код_группы' && $order === 'ASC' ? 'DESC' : 'ASC' ?>&pool=<?= $pool_filter ?>">
                            Группа
                        </a>
                    </th>
                    <th>
                        <a href="?sort=Количество_человек&order=<?= $sort === 'Количество_человек' && $order === 'ASC' ? 'DESC' : 'ASC' ?>&pool=<?= $pool_filter ?>">
                            Макс. участников
                        </a>
                    </th>
                    <th>
                        <a href="?sort=actual_count&order=<?= $sort === 'actual_count' && $order === 'ASC' ? 'DESC' : 'ASC' ?>&pool=<?= $pool_filter ?>">
                            Факт. участников
                        </a>
                    </th>
                    <th>
                        <a href="?sort=Адрес_бассейна&order=<?= $sort === 'Адрес_бассейна' && $order === 'ASC' ? 'DESC' : 'ASC' ?>&pool=<?= $pool_filter ?>">
                            Бассейн
                        </a>
                    </th>
                    <th>Тренировки</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group):
                    // Получаем количество тренировок для каждой группы
                    $workouts_count = $conn->query("SELECT COUNT(*) FROM Тренировки WHERE Код_группы = " . $group['Код_группы'])->fetchColumn();
                ?>
                    <tr>
                        <td>Группа <?= htmlspecialchars($group['Код_группы']) ?></td>
                        <td><?= htmlspecialchars($group['Количество_человек']) ?></td>
                        <td><span class="count-badge"><?= htmlspecialchars($group['actual_count']) ?></span></td>
                        <td><?= htmlspecialchars($group['Адрес_бассейна']) ?></td>
                        <td><?= $workouts_count ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>