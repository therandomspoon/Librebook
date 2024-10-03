<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

include '../config.php';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
        $message_text = $_POST["message_text"];

        if (empty($name) || empty($message_text)) {
            echo "Sender name and message are required!";
        } else {
            $username = $_SESSION['username'];
            $stmt = $pdo->prepare("SELECT kids FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $kids_mode = $stmt->fetchColumn();

            if ($kids_mode == 'on') {
                $forbiddenWords = json_decode(file_get_contents('bad-words.json'), true);
                $lowercase_message_text = strtolower($message_text);
                foreach ($forbiddenWords as $word) {
                    if (strpos($lowercase_message_text, strtolower($word)) !== false) {
                        echo "Warning! Your message contained language not allowed in kids mode! You can bypass this by disabling it in settings";
                        return;
                    }
                }
            }

            if (isset($_SESSION['replyto'])) {
                $message_text = $_SESSION['replyto'] . $message_text;
            }

            $sql = "INSERT INTO messages (`name`, `message`, `timestamp`) VALUES (?, ?, CURRENT_TIMESTAMP)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $message_text, PDO::PARAM_STR);
            $stmt->execute();
            echo "Message sent successfully!";
            unset($_SESSION['replyto']);
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
