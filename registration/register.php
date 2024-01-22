<?php
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function containsEmoji($string) {
    $string = preg_replace('/[^\w\s.,!?]/', '', $string);
    return preg_match('/[^\x00-\x7F]/', $string);
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // set default
    $defaultMode = 'light';

    if (containsEmoji($username)) {
        echo 'Error: Usernames cannot contain emojis.';
        exit();
    }

    if (!isValidEmail($email)) {
        echo 'Error: Invalid email address.';
        exit();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, preferred_mode) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $email, $password, $defaultMode]);

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
            echo 'Error: User profiles file not found';
        }
    } else {
        echo 'Error: Username or email already exists.';
    }
}
?>
