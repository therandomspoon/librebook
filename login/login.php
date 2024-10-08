<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['sudopassword'] = $user['password']; //* its password but cooler
        $_SESSION['kmode'] = $user['kids'];
        echo 'Login successful! Welcome, ' . htmlspecialchars($user['username']) . '!';
        header('Location: ../main.php');
    } else {
        header('Location: errorlog.html');
        exit();
    }
}
?>
