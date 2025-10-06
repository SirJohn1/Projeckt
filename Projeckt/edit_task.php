<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$task = null;

// Получаем задачу для редактирования
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    $tasksFile = TASKS_DIR . $_SESSION['user_id'] . '.txt';
    
    if (file_exists($tasksFile)) {
        $lines = file($tasksFile, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $taskData = explode('|', $line);
                if (isset($taskData[0]) && $taskData[0] == $taskId) {
                    $task = $taskData;
                    break;
                }
            }
        }
    }
    
    if (!$task) {
        $error = 'Задача не найдена';
    }
}

// Обработка сохранения изменений
if ($_POST && isset($_POST['save'])) {
    $taskId = $_POST['task_id'];
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($title)) {
        $tasksFile = TASKS_DIR . $_SESSION['user_id'] . '.txt';
        
        if (file_exists($tasksFile)) {
            $lines = file($tasksFile, FILE_IGNORE_NEW_LINES);
            $newTasks = [];
            
            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    $taskData = explode('|', $line);
                    if (isset($taskData[0]) && $taskData[0] == $taskId) {
                        // Обновляем задачу
                        $taskData[1] = $title;
                        $taskData[2] = $content;
                    }
                    $newTasks[] = implode('|', $taskData);
                }
            }
            
            if (file_put_contents($tasksFile, implode("\n", $newTasks)) !== false) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'Ошибка при сохранении задачи';
            }
        }
    } else {
        $error = 'Название задачи обязательно';
    }
}

// Если задача не найдена
if (!$task && !isset($_POST['save'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактировать задачу</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Редактировать задачу</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="task_id" value="<?= $task[0] ?>">
            
            <input type="text" name="title" placeholder="Название задачи" required 
                   value="<?= htmlspecialchars($task[1] ?? '') ?>">
            
            <textarea name="content" placeholder="Описание задачи"><?= htmlspecialchars($task[2] ?? '') ?></textarea>
            
            <div class="form-actions">
                <button type="submit" name="save">Сохранить</button>
                <a href="index.php" class="cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>