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
} catch(PDOException $e) {
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

$stmt = $conn->query("SELECT * FROM temp_notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatDateRussian($dateString) {
    $date = new DateTime($dateString);
    return $date->format('H:i d-m-Y');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Уведомления</title>
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

h1 {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
    margin-bottom: 25px;
    display: inline-block;
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

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
    background: #ffffff;
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #eaeef2;
}

th {
    background-color: #4a6bff;
    color: white;
    font-weight: 600;
}

tr:nth-child(even) {
    background-color: #f8fafc;
}

tr:hover {
    background-color: #f0f4f8;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input {
    padding: 10px;
    border: 1px solid #d1d8e0;
    border-radius: 6px;
    width: 100%;
    max-width: 320px;
    transition: border 0.2s ease;
}

.form-group input:focus {
    border-color: #4a6bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(74, 107, 255, 0.2);
}

button {
    padding: 10px 18px;
    background: #4a6bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

button:hover {
    background: #3a5bef;
    transform: translateY(-1px);
}

button[type="reset"] {
    background: #ff6b6b;
}

button[type="reset"]:hover {
    background: #ff5252;
}

.edit-form {
    margin-top: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #eaeef2;
}

button.edit-button {
    background: #ffc107;
    color: #2c3e50;
}

button.edit-button:hover {
    background: #ffab00;
}

button.cancel-button {
    background: #95a5a6;
}

button.cancel-button:hover {
    background: #7f8c8d;
}

button.save-button {
    background: #28a745;
}

button.save-button:hover {
    background: #218838;
}

button.delete-button {
    background: #dc3545;
}

button.delete-button:hover {
    background: #c82333;
}

.edit-row {
    display: none;
    background-color: #fff8e8;
    padding: 12px;
    border-radius: 6px;
    margin-top: 12px;
    border: 1px solid #ffeeba;
}

.edit-row input {
    margin-bottom: 12px;
    padding: 10px;
    border: 1px solid #d1d8e0;
    border-radius: 6px;
    width: 100%;
}

.buttons-group {
    display: flex;
    gap: 8px;
}

.status-active {
    color: #28a745;
    font-weight: 500;
}

.status-maintenance {
    color: #ffc107;
    font-weight: 500;
}

.status-out_of_order {
    color: #dc3545;
    font-weight: 500;
}

.membership-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 500;
}

.membership-active {
    background: #e6f7ea;
    color: #28a745;
}

.membership-expired {
    background: #feeaea;
    color: #dc3545;
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
        <h1>Управление уведомлениями</h1>
        
        <!-- Форма добавления -->
        <div class="form-group">
            <h2>Добавить уведомление</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Заголовок:</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Сообщение:</label>
                    <input type="text" name="message" required>
                </div>
                <button type="submit" name="add">Добавить</button>
            </form>
        </div>

        <!-- Таблица с данными -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Заголовок</th>
                    <th>Сообщение</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notification): ?>
                    <tr id="row-<?= $notification['id'] ?>">
                        <td><?= htmlspecialchars($notification['id']) ?></td>
                        <td><?= htmlspecialchars($notification['title']) ?></td>
                        <td><?= htmlspecialchars($notification['message']) ?></td>
                        <td><?= htmlspecialchars(formatDateRussian($notification['created_at'])) ?></td>
                        <td class="buttons-group">
                            <button type="button" class="edit-button" onclick="showEditForm(<?= $notification['id'] ?>)">Редактировать</button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $notification['id'] ?>">
                                <button type="submit" name="delete" class="delete-button" onclick="return confirm('Удалить уведомление?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                    <tr id="edit-row-<?= $notification['id'] ?>" class="edit-row">
                        <td colspan="5">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $notification['id'] ?>">
                                <label>Заголовок:</label>
                                <input type="text" name="edit_title" value="<?= htmlspecialchars($notification['title']) ?>" required>
                                <label>Сообщение:</label>
                                <input type="text" name="edit_message" value="<?= htmlspecialchars($notification['message']) ?>" required>
                                <div class="buttons-group">
                                    <button type="submit" name="update" class="save-button">Сохранить</button>
                                    <button type="button" class="cancel-button" onclick="hideEditForm(<?= $notification['id'] ?>)">Отмена</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showEditForm(id) {
            document.getElementById('edit-row-' + id).style.display = 'table-row';
        }
        
        function hideEditForm(id) {
            document.getElementById('edit-row-' + id).style.display = 'none';
        }
    </script>
</body>
</html>