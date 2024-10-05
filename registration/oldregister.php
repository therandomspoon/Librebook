<?php
include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function containsEmoji($string) {
    // Remove non-word characters and check for emojis
    $string = preg_replace('/[^\w\s.,!?]/', '', $string);
    return preg_match('/[^\x00-\x7F]/', $string);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password from POST
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $defaultMode = 'light';
    $defaultPfp = '../images/empty.webp'; // Default profile picture
    $defaultBio = '';  // Default empty bio

    // Check if username contains emoji
    if (containsEmoji($username)) {
        echo 'Error: Usernames cannot contain emojis.';
        exit();
    }

    // Check if the username already exists in the users table
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insert new user into the users table
        $stmt = $pdo->prepare('INSERT INTO users (username, password, preferred_mode, following) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $password, $defaultMode, '']);

        // Insert new user profile into the profiles table
        $stmtProfile = $pdo->prepare('INSERT INTO profiles (username, pfp, bio) VALUES (?, ?, ?)');
        $stmtProfile->execute([$username, $defaultPfp, $defaultBio]);

        echo 'Registration successful!';
        header('Location: ../login/login.html');
        exit();
    } else {
        // Username already exists
        header('Location: ../errors/erroreg.html');
    }
}
?>
