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

        $jsonFile = '../user-profiles.json';

        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $userProfiles = json_decode($jsonData, true);

            $newUser = [
                'username' => $username,
                'pfp' => '../images/empty.webp',
                'bio' => '',
                'preferred_mode' => $defaultMode
            ];

            $userProfiles['users'][] = $newUser;
            file_put_contents($jsonFile, json_encode($userProfiles, JSON_PRETTY_PRINT));

            echo 'Registration successful!';
            header('Location: ../login/login.html');
            exit();
        } else {
            header('Location: ../errors/erroreg.html');
        }
    } else {
        header('Location: ../errors/erroreg.html');
    }
}
?>
