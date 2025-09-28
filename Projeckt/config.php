<?php
session_start();

// Настройки
define('USERS_FILE', 'data/users.txt');
define('TASKS_DIR', 'data/tasks/');

// Создаем папки если их нет
if (!file_exists('data')) mkdir('data');
if (!file_exists(TASKS_DIR)) mkdir(TASKS_DIR);

// Функция для проверки авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Функция получения имени пользователя
function getUsername() {
    return $_SESSION['username'] ?? 'Гость';
}
?>