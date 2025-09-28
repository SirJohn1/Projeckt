<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        // Читаем существующих пользователей
        $users = [];
        if (file_exists(USERS_FILE)) {
            $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                $users[] = explode('|', $line);
            }
        }
        
        // Проверяем нет ли такого пользователя
        foreach ($users as $user) {
            if ($user[0] === $username) {
                $error = 'Пользователь уже существует';
                break;
            }
        }
        
        if (!$error) {
            // Сохраняем нового пользователя
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            file_put_contents(USERS_FILE, "$username|$hashedPassword\n", FILE_APPEND);
            $success = 'Регистрация успешна! <a href="login.php">Войти</a>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Регистрация</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="post">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>