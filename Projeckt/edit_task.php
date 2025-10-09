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
    try {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        $task = $stmt->fetch();
        
        if (!$task) {
            $error = 'Задача не найдена';
        }
    } catch(PDOException $e) {
        $error = 'Ошибка при загрузке задачи';
    }
}

// Обработка сохранения изменений
if ($_POST && isset($_POST['save'])) {
    $taskId = $_POST['task_id'];
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($title)) {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, content = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $taskId, $_SESSION['user_id']]);
            
            header('Location: index.php');
            exit;
            
        } catch(PDOException $e) {
            $error = 'Ошибка при сохранении задачи';
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
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            
            <input type="text" name="title" placeholder="Название задачи" required 
                   value="<?= htmlspecialchars($task['title']) ?>">
            
            <textarea name="content" placeholder="Описание задачи"><?= htmlspecialchars($task['content']) ?></textarea>
            
            <div class="form-actions">
                <button type="submit" name="save">Сохранить</button>
                <a href="index.php" class="cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>