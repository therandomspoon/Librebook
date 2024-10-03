<?php
include '../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verifyCode = $_POST['verify_code'] ?? '';
    if ($verifyCode == $_SESSION['verify_code'] && time() < $_SESSION['verify_code_expiration']) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password, preferred_mode, following, kids) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $_SESSION['vusername'],
                $_SESSION['vemail'],
                $_SESSION['password'],
                $_SESSION['defaultMode'],
                '',
                $_SESSION['defaultKids']
            ]);

            $jsonFile = '../user-profiles.json';
            if (file_exists($jsonFile)) {
                $jsonData = file_get_contents($jsonFile);
                $userProfiles = json_decode($jsonData, true);

                $newUser = [
                    'username' => $_SESSION['vusername'],
                    'pfp' => '../images/empty.webp',
                    'bio' => '',
                    'preferred_mode' => $_SESSION['defaultMode']
                ];

                $userProfiles['users'][] = $newUser;
                file_put_contents($jsonFile, json_encode($userProfiles, JSON_PRETTY_PRINT));
                $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->execute([$_SESSION['vusername']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['sudopassword'] = $user['password']; //* its password but cooler
                $_SESSION['kmode'] = $user['kids'];
                header('Location: ../main.php');
                exit();
            } else {
                $_SESSION['message'] = 'Critical error: user profiles JSON file not found.';
                header('Location: error.php');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['message'] = 'Database operation failed: ' . htmlspecialchars($e->getMessage());
            header('Location: error.php');
            exit();
        }
    } else {
        $_SESSION['message'] = 'Invalid or expired verification code.';
        header('Location: error.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: error.php');
    exit();
}
