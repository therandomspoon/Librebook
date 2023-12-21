<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'config.php'; 

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
        $message_text = htmlspecialchars($_POST["message_text"], ENT_QUOTES, 'UTF-8');

        if (empty($name) || empty($message_text)) {
            echo "Sender name and message are required!";
        } else {
            $sql = "INSERT INTO messages (`name`, `message`, `timestamp`) VALUES (?, ?, CURRENT_TIMESTAMP)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $message_text, PDO::PARAM_STR);
            $stmt->execute();
            echo "Message sent successfully!";
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
