<?php
require_once 'config.php';


if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        
        if (file_exists(USERS_FILE)) {
            $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                list($fileUsername, $filePassword) = explode('|', $line);
                
                if ($fileUsername === $username && password_verify($password, $filePassword)) {
                    $_SESSION['user_id'] = $username;
                    $_SESSION['username'] = $username;
                    header('Location: index.php');
                    exit;
                }
            }
        }
        $error = 'Неверное имя пользователя или пароль';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Вход</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        
        <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>