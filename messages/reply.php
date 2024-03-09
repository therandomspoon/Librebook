<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

// Check if the "id" parameter is set in the URL
if (isset($_GET['id'])) {
    // Get the value of the "id" parameter and assign it to a variable
    $id = $_GET['id'];

    // Now, you can use the $id variable as needed
    echo "The value of 'id' is: " . $id;
} else {
    echo "No 'id' parameter found in the URL.";
}

include '../config.php';

function extractVideoId($url) {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $query);
    return isset($query['v']) ? $query['v'] : null;
}

function convertHashtagsToLinks($message) {
    $pattern = '/#(\w+)/';
    $messageWithLinks = preg_replace($pattern, '<a href="../messages/hashtag.php?tag=$1">#$1</a>', $message);
    return $messageWithLinks;
}

try {
    $messageId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($messageId) {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp`
                            FROM messages
                            WHERE id = :id
                            ORDER BY `timestamp` DESC");

        $stmt->bindParam(':id', $messageId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            foreach ($result as $row) {
                $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
                $timestamp = $row["timestamp"];
                $id = $row["id"];

                $message = convertHashtagsToLinks($message);


                $_SESSION['replyto'] = "[#@". $id ."; -- ";
                header('Location:../main.php');
            }
        } else {
            echo "No message found with the specified ID.";
        }
    } else {
        echo "No ID parameter provided.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
/*
function convertHashtagsToLinks($message) {
    $pattern = '/#(\w+)/';
    $messageWithLinks = preg_replace($pattern, '<a href="../messages/hashtag.php?tag=$1">#$1</a>', $message);
    return $messageWithLinks;
}
*/
?>
