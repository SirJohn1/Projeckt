<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_POST) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($title)) {
        $tasksFile = TASKS_DIR . $_SESSION['user_id'] . '.txt';
        
        
        $id = time();
        
        
        $status = '0'; 
        $taskData = "$id|$title|$content|$status\n";
        
        if (file_put_contents($tasksFile, $taskData, FILE_APPEND) !== false) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Ошибка при сохранении задачи';
        }
    } else {
        $error = 'Название задачи обязательно';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Добавить задачу</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить задачу</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="text" name="title" placeholder="Название задачи" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            <textarea name="content" placeholder="Описание задачи"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            <div class="form-actions">
                <button type="submit">Добавить</button>
                <a href="index.php" class="cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>