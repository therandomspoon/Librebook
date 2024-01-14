<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../config.php'; 

try {
    $conn = $pdo->query("SELECT `name`, `message`, `timestamp`
                        FROM messages
                        ORDER BY `timestamp` DESC");

    if ($conn) {
        $result = $conn->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            foreach ($result as $row) {
                $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
                $timestamp = $row["timestamp"];

                if (filter_var($message, FILTER_VALIDATE_URL) && 
                    (strpos($message, '.jpg') !== false || 
                     strpos($message, '.jpeg') !== false || 
                     strpos($message, '.png') !== false || 
                     strpos($message, '.webp') !== false)) {
                    echo "<div><b>" . $name . ":</b> <br> <img src='" . $message . "' alt='Image' style='width: 211px; height: 148px;'> <br> (Sent on: " . $timestamp . ")</div>";
                } else {
                    echo "<div><b>" . $name . ":</b> " . $message . " (Sent on: " . $timestamp . ")</div>";
                    echo "<hr>";
                }
            }
        } else {
            echo "No messages.";
        }
    } else {
        echo "Error executing query.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
