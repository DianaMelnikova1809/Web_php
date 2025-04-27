<?php

require_once 'db_connection.php';

// Получение параметров сортировки и фильтрации
$sort = isset($_GET['sort']) ? $_GET['sort'] : 's.Фамилия';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$sportsman_filter = isset($_GET['sportsman']) ? $_GET['sportsman'] : '';

// Базовый SQL запрос
$sql = "SELECT p.*, s.Фамилия AS Спортсмен_Фамилия, s.Имя AS Спортсмен_Имя 
        FROM Представители p
        LEFT JOIN Спортсмены s ON p.Код_спортсмена = s.Код_спортсмена
        WHERE 1=1";

// Добавление фильтров
if (!empty($sportsman_filter)) {
    $sql .= " AND p.Код_спортсмена = :sportsman";
}

// Добавление сортировки
$sql .= " ORDER BY $sort $order";

$stmt = $conn->prepare($sql);

// Привязка параметров фильтрации
if (!empty($sportsman_filter)) {
    $stmt->bindParam(':sportsman', $sportsman_filter);
}

$stmt->execute();
$representatives = $stmt->fetchAll();

// Получение списка спортсменов для фильтра
$sportsmen_sql = "SELECT Код_спортсмена, Фамилия, Имя FROM Спортсмены ORDER BY Фамилия, Имя";
$sportsmen = $conn->query($sportsmen_sql)->fetchAll();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Представители - Система управления бассейном</title>
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

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
            background: #e6f7ea;
            color: #28a745;
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
        <a href="representatives.php" class="active">Представители</a>
        <a href="pools.php">Бассейны</a>
        <a href="notifications.php">Уведомления</a>
    </div>

    <div class="content">
        <h1>Представители спортсменов</h1>

        <div class="filters">
            <form method="GET">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Спортсмен:</label>
                        <select name="sportsman">
                            <option value="">Все спортсмены</option>
                            <?php foreach ($sportsmen as $sportsman): ?>
                                <option value="<?= $sportsman['Код_спортсмена'] ?>"
                                    <?= $sportsman_filter == $sportsman['Код_спортсмена'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sportsman['Фамилия'] . ' ' . $sportsman['Имя']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="apply-button">Применить фильтры</button>
                    <a href="representatives.php" class="reset-button">Сбросить</a>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        <a href="?sort=Фамилия&order=<?= $sort === 's.Фамилия' && $order === 'ASC' ? 'DESC' : 'ASC' ?>&sportsman=<?= $sportsman_filter ?>">
                            Фамилия
                        </a>
                    </th>
                    <th>
                        <a href="?sort=Имя&order=<?= $sort === 's.Имя' && $order === 'ASC' ? 'DESC' : 'ASC' ?>&sportsman=<?= $sportsman_filter ?>">
                            Имя
                        </a>
                    </th>
                    <th>Отчество</th>
                    <th>Телефон</th>
                    <th>Адрес</th>
                    <th>Спортсмен</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($representatives as $representative): ?>
                <tr>
                    <td><?= htmlspecialchars($representative['Фамилия']) ?></td>
                    <td><?= htmlspecialchars($representative['Имя']) ?></td>
                    <td><?= htmlspecialchars($representative['Отчество']) ?></td>
                    <td><?= htmlspecialchars($representative['Телефон']) ?></td>
                    <td><?= htmlspecialchars($representative['Адрес_проживания']) ?></td>
                    <td>
                        <?php if ($representative['Код_спортсмена']): ?>
                            <?= htmlspecialchars($representative['Спортсмен_Фамилия'] . ' ' . $representative['Спортсмен_Имя']) ?>
                        <?php else: ?>
                            Не назначен
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>
