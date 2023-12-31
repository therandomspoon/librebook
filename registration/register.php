<?php
include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function containsEmoji($string) {
    $string = preg_replace('/[^\w\s.,!?]/', '', $string);
    return preg_match('/[^\x00-\x7F]/', $string);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    if (containsEmoji($username)) {
        echo 'Error: Usernames cannot contain emojis.';
        exit();
    }

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $password]);

        $jsonFile = '../user-profiles.json';

        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $userProfiles = json_decode($jsonData, true);

            $newUser = [
                'username' => $username,
                'pfp' => '../images/empty.webp',
                'bio' => '' 
            ];

            $userProfiles['users'][] = $newUser;
            file_put_contents($jsonFile, json_encode($userProfiles, JSON_PRETTY_PRINT));

            echo 'Registration successful!';
            header('Location: ../login/login.html');
            exit();
        } else {
            echo 'Error: User profiles file not found';
        }
    } else {
        header('Location: ../erros/erroreg.html');
    }
}
?>
