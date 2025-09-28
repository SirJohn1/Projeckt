<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    $tasksFile = TASKS_DIR . $_SESSION['user_id'] . '.txt';
    
    if (file_exists($tasksFile)) {
        $lines = file($tasksFile, FILE_IGNORE_NEW_LINES);
        $newTasks = [];
        
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $task = explode('|', $line);
                if (isset($task[0]) && $task[0] != $taskId) {
                    $newTasks[] = $line;
                }
            }
        }
        
        
        if (empty($newTasks)) {
            unlink($tasksFile);
        } else {
            file_put_contents($tasksFile, implode("\n", $newTasks));
        }
    }
}

header('Location: index.php');
exit;
?>