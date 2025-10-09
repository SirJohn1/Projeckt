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
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, content, status) VALUES (?, ?, ?, 0)");
            $stmt->execute([$_SESSION['user_id'], $title, $content]);
            
            header('Location: index.php');
            exit;
            
        } catch(PDOException $e) {
            $error = 'Ошибка при создании задачи';
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
            <input type="text" name="title" placeholder="Название задачи" required 
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            <textarea name="content" placeholder="Описание задачи"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            <div class="form-actions">
                <button type="submit">Добавить</button>
                <a href="index.php" class="cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>