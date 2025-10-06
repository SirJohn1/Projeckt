<?php
require_once 'config.php';

// Если не авторизован - на логин
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Обработка отметки выполнения
if (isset($_POST['toggle_status'])) {
    $taskId = $_POST['task_id'];
    $tasksFile = TASKS_DIR . $_SESSION['user_id'] . '.txt';
    
    if (file_exists($tasksFile)) {
        $lines = file($tasksFile, FILE_IGNORE_NEW_LINES);
        $newTasks = [];
        
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $task = explode('|', $line);
                if (isset($task[0]) && $task[0] == $taskId) {
                    // Меняем статус
                    $task[3] = $task[3] == '1' ? '0' : '1';
                }
                $newTasks[] = implode('|', $task);
            }
        }
        
        file_put_contents($tasksFile, implode("\n", $newTasks));
    }
    header('Location: index.php');
    exit;
}

// Получаем задачи пользователя
$tasksFile = TASKS_DIR . $_SESSION['user_id'] . '.txt';
$tasks = [];

if (file_exists($tasksFile)) {
    $lines = file($tasksFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (!empty(trim($line))) {
            $taskData = explode('|', $line);
            // Проверяем что задача имеет правильный формат (4 элемента)
            if (count($taskData) === 4) {
                $tasks[] = $taskData;
            }
        }
    }
}

// Фильтрация
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'newest';

if ($filter === 'completed') {
    $tasks = array_filter($tasks, function($task) {
        return isset($task[3]) && $task[3] === '1';
    });
} elseif ($filter === 'active') {
    $tasks = array_filter($tasks, function($task) {
        return isset($task[3]) && $task[3] === '0';
    });
}

// Сортировка (только если есть задачи)
if (!empty($tasks)) {
    if ($sort === 'alphabet') {
        usort($tasks, function($a, $b) {
            return strcmp($a[1] ?? '', $b[1] ?? '');
        });
    } else {
        usort($tasks, function($a, $b) {
            return ($b[0] ?? 0) - ($a[0] ?? 0);
        });
    }
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
                    <?php if (isset($task[1]) && isset($task[2]) && isset($task[3])): ?>
                        <div class="task <?= $task[3] ? 'completed' : '' ?>">
                            <form method="post" class="status-form">
                                <input type="hidden" name="task_id" value="<?= $task[0] ?>">
                                <button type="submit" name="toggle_status" class="status-btn <?= $task[3] ? 'completed' : '' ?>">
                                    <?= $task[3] ? '✅' : '⏳' ?>
                                </button>
                            </form>
                            
                            <div class="task-content">
                                <h3><?= htmlspecialchars($task[1]) ?></h3>
                                <p><?= htmlspecialchars($task[2]) ?></p>
                            </div>
                            
                            <div class="task-actions">
                                <a href="edit_task.php?id=<?= $task[0] ?>" class="edit">Редактировать</a>
                                <a href="delete_task.php?id=<?= $task[0] ?>" class="delete" onclick="return confirm('Удалить задачу?')">Удалить</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="add_task.php" class="add-btn">+ Добавить задачу</a>
    </div>
</body>
</html>