<?php
require_once 'db_connection.php';

// Получение параметров фильтрации
$group_filter = isset($_GET['group']) ? $_GET['group'] : '';
$trainer_filter = isset($_GET['trainer']) ? $_GET['trainer'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Базовый SQL запрос
$sql = "SELECT t.*, g.Код_группы, g.Количество_человек, 
               tr.Фамилия AS Тренер_Фамилия, tr.Имя AS Тренер_Имя,
               b.Адрес_бассейна
        FROM Тренировки t
        LEFT JOIN Группы g ON t.Код_группы = g.Код_группы
        LEFT JOIN Тренеры tr ON t.Код_тренера = tr.Код_тренера
        LEFT JOIN Бассейны b ON g.Код_бассейна = b.Код_бассейна
        WHERE 1=1";

// Добавление фильтров
if ($group_filter) {
    $sql .= " AND t.Код_группы = :group";
}
if ($trainer_filter) {
    $sql .= " AND t.Код_тренера = :trainer";
}
if ($date_from) {
    $sql .= " AND t.Дата >= :date_from";
}
if ($date_to) {
    $sql .= " AND t.Дата <= :date_to";
}

$sql .= " ORDER BY t.Дата DESC, t.Время DESC";

$stmt = $conn->prepare($sql);

// Привязка параметров фильтрации
if ($group_filter) {
    $stmt->bindParam(':group', $group_filter);
}
if ($trainer_filter) {
    $stmt->bindParam(':trainer', $trainer_filter);
}
if ($date_from) {
    $stmt->bindParam(':date_from', $date_from);
}
if ($date_to) {
    $stmt->bindParam(':date_to', $date_to);
}

$stmt->execute();
$workouts = $stmt->fetchAll();

// Получение списка групп для фильтра
$groups_sql = "SELECT Код_группы, Количество_человек FROM Группы ORDER BY Код_группы";
$groups = $conn->query($groups_sql)->fetchAll();

// Получение списка тренеров для фильтра
$trainers_sql = "SELECT Код_тренера, Фамилия, Имя FROM Тренеры ORDER BY Фамилия, Имя";
$trainers = $conn->query($trainers_sql)->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Тренировки</title>
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

        .workout-time {
            font-weight: 600;
            color: var(--dark-color);
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
        <h1>Тренировки бассейна</h1>
        
        <div class="filters">
            <form method="GET">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Группа:</label>
                        <select name="group">
                            <option value="">Все группы</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['Код_группы'] ?>" 
                                        <?= $group_filter == $group['Код_группы'] ? 'selected' : '' ?>>
                                    Группа <?= $group['Код_группы'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Тренер:</label>
                        <select name="trainer">
                            <option value="">Все тренеры</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer['Код_тренера'] ?>"
                                        <?= $trainer_filter == $trainer['Код_тренера'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($trainer['Фамилия'] . ' ' . $trainer['Имя']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Дата от:</label>
                        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label>Дата до:</label>
                        <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="apply-button">Применить фильтры</button>
                    <a href="workouts.php" class="reset-button">Сбросить</a>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Группа</th>
                    <th>Тренер</th>
                    <th>Бассейн</th>
                    <th>Участников</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workouts as $workout): ?>
                    <tr>
                        <td><?= htmlspecialchars($workout['Дата']) ?></td>
                        <td class="workout-time"><?= htmlspecialchars($workout['Время']) ?></td>
                        <td><?= $workout['Код_группы'] ? 'Группа ' . htmlspecialchars($workout['Код_группы']) : 'Индивидуальная' ?></td>
                        <td><?= $workout['Тренер_Фамилия'] ? htmlspecialchars($workout['Тренер_Фамилия'] . ' ' . $workout['Тренер_Имя']) : 'Не назначен' ?></td>
                        <td><?= htmlspecialchars($workout['Адрес_бассейна']) ?></td>
                        <td><?= $workout['Количество_человек'] ?? 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>