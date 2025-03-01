<?php
include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function containsEmoji($string) {
    return preg_match('/[\p{So}\p{Cn}]/u', $string);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $defaultMode = 'light';
    $kidsv = isset($_POST['kidsm']) ? 'on' : 'off';

    if (strpos($username, ' ') !== false || strpos($username, ',') !== false) {
        session_start();
        $_SESSION['error_message'] = 'Error: Usernames cannot contain spaces or commas.';
        header('Location: erroreg.html');
        exit();
    }

    if (containsEmoji($username)) {
        session_start();
        $_SESSION['error_message'] = 'Error: Usernames cannot contain emojis.';
        header('Location: erroreg.html');
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, preferred_mode, following, kids) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$username, $password, $defaultMode, '', $kidsv]);

        $stmtProfile = $pdo->prepare('INSERT INTO profiles (username, pfp, bio) VALUES (?, ?, ?)');
        $stmtProfile->execute([$username, '../images/empty.webp', '']);

        header('Location: ../login/login.html');
        exit();
    } else {
        header('Location: erroreg.html');
        exit();
    }
}
?>
