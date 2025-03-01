<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    echo "User is not logged in!";
    exit();
}

include '../config.php';

if (isset($_POST['name']) && isset($_POST['reciever']) && isset($_POST['message_text'])) {
    $name = htmlspecialchars($_POST['name']);
    $reciever = htmlspecialchars($_POST['reciever']);
    $message_text = htmlspecialchars($_POST['message_text']);
    
    if (!empty($name) && !empty($reciever) && !empty($message_text)) {
        $stmt = $pdo->prepare("INSERT INTO dm (sender, reciever, message, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt->execute([$name, $reciever, $message_text]);

        echo "Message sent successfully!";
    } else {
        echo "All fields are required!";
    }
} else {
    echo "Invalid data!";
}
?>
