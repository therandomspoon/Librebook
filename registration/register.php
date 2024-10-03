<?php
include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

function containsEmoji($string) {
    return preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{2300}-\x{23FF}\x{2B50}\x{1F004}\x{1F0CF}\x{2B06}\x{2934}\x{2935}\x{25AA}\x{25AB}\x{25FE}\x{2B1B}\x{2B1C}\x{25FD}\x{25FB}\x{2B50}\x{1F004}\x{1F0CF}]/u', $string);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $defaultMode = 'light';
    $defaultKids = 'off';

    $_SESSION['vusername'] = $_POST['username'];
    $_SESSION['vemail'] = $_POST['email'];
    $_SESSION['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $_SESSION['defaultMode'] = 'light';
    $_SESSION['defaultKids'] = 'off';

    if (containsEmoji($username)) {
        echo 'Error: Usernames cannot contain emojis.';
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $usernameCount = $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $emailCount = $stmt->fetchColumn();

    if ($usernameCount == 0 && $emailCount == 0) {
        // Redirect to email authentication
        header('Location: emailauth.php');
        exit();
    } else {
        // Error redirect if username or email already exists
        if ($usernameCount > 0) {
            echo 'Error: Username already exists.';
        } elseif ($emailCount > 0) {
            echo 'Error: Email already exists.';
        }
        header('Location: ../errors/erroreg.html');
        exit();
    }
}
?>
