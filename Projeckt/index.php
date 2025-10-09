<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Обработка отметки выполнения
if (isset($_POST['toggle_status'])) {
    $taskId = $_POST['task_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE tasks SET status = 1 - status WHERE id = ? AND user_id = ?");
        $stmt->execute([$taskId, $_SESSION['user_id']]);
        header('Location: index.php');
        exit;
    } catch(PDOException $e) {
        $error = 'Ошибка при обновлении задачи';
    }
}

// Получаем задачи пользователя
try {
    $filter = $_GET['filter'] ?? 'all';
    $sort = $_GET['sort'] ?? 'newest';
    
    $sql = "SELECT * FROM tasks WHERE user_id = ?";
    
    // Фильтрация
    if ($filter === 'completed') {
        $sql .= " AND status = 1";
    } elseif ($filter === 'active') {
        $sql .= " AND status = 0";
    }
    
    // Сортировка
    if ($sort === 'alphabet') {
        $sql .= " ORDER BY title ASC";
    } else {
        $sql .= " ORDER BY created_at DESC";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $tasks = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = 'Ошибка при загрузке задач';
    $tasks = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Мои задачи</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Мои задачи</h1>
            <div class="user-info">
                Привет, <?= htmlspecialchars(getUsername()) ?>! 
                <a href="logout.php" class="logout">Выйти</a>
            </div>
        </header>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Фильтры и сортировка -->
        <div class="filters">
            <form method="get" class="filter-form">
                <select name="filter">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Все задачи</option>
                    <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Активные</option>
                    <option value="completed" <?= $filter === 'completed' ? 'selected' : '' ?>>Выполненные</option>
                </select>
                
                <select name="sort">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Сначала новые</option>
                    <option value="alphabet" <?= $sort === 'alphabet' ? 'selected' : '' ?>>По алфавиту</option>
                </select>
                
                <button type="submit">Применить</button>
            </form>
        </div>

        <!-- Список задач -->
        <div class="tasks">
            <?php if (empty($tasks)): ?>
                <p>Задач пока нет</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task <?= $task['status'] ? 'completed' : '' ?>">
                        <form method="post" class="status-form">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <button type="submit" name="toggle_status" class="status-btn <?= $task['status'] ? 'completed' : '' ?>">
                                <?= $task['status'] ? '✅' : '⏳' ?>
                            </button>
                        </form>
                        
                        <div class="task-content">
                            <h3><?= htmlspecialchars($task['title']) ?></h3>
                            <p><?= htmlspecialchars($task['content']) ?></p>
                            <small>Создано: <?= date('d.m.Y H:i', strtotime($task['created_at'])) ?></small>
                        </div>
                        
                        <div class="task-actions">
                            <a href="edit_task.php?id=<?= $task['id'] ?>" class="edit">Редактировать</a>
                            <a href="delete_task.php?id=<?= $task['id'] ?>" class="delete" onclick="return confirm('Удалить задачу?')">Удалить</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="add_task.php" class="add-btn">+ Добавить задачу</a>
    </div>
</body>
</html>