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
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $defaultMode = 'light';

    if (containsEmoji($username)) {
        echo 'Error: Usernames cannot contain emojis.';
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, preferred_mode, following) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $password, $defaultMode, '']);

        $stmtProfile = $pdo->prepare('INSERT INTO profiles (username, pfp, bio) VALUES (?, ?, ?)');
        $stmtProfile->execute([$username, '../images/empty.webp', '']);

        echo 'Registration successful!';
        header('Location: ../login/login.html');
        exit();
    } else {
        header('Location: ../errors/erroreg.html');
        exit();
    }
}
?>
