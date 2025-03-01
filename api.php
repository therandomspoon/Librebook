<?php
include 'config.php';
header('Content-Type: text/plain');
$userapi = $_GET['apikey'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE apikey = ?");
$stmt->execute([$userapi]);
$username = $stmt->fetchColumn();
echo $username . ",";


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

if ($_GET['function'] == 'send_message'){
    try {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $name = $username;
            $message_text = $_GET["message_text"];

            if (empty($name) || empty($message_text)) {
                echo "Sender name and message are required!";
            } else {
                $stmt = $pdo->prepare("SELECT kids FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $kids_mode = $stmt->fetchColumn();

                if ($kids_mode == 'on') {
                    $forbiddenWords = json_decode(file_get_contents('/messages/bad-words.json'), true);
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
} elseif ($_GET['function'] == 'getall_message') {
    $name = $username;
    $stmt = $pdo->prepare("SELECT message, timestamp FROM messages WHERE name = ? ORDER BY timestamp DESC");
    $stmt->execute([$username]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo $row['message'];
        echo " - ";
        echo $row['timestamp'];
        echo ",";
    }
} elseif ($_GET['function'] == 'people_you_follow') {
    try {
        $query = "SELECT username
                  FROM users
                  WHERE FIND_IN_SET(:sender, following)";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':sender', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            foreach ($result as $row) {
                $name = htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8');
                echo $name . ",";
            }
        } else {
            echo "You do not follow anyone.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} elseif ($_GET['function'] == 'dm_sendmessage') {
    $reciever = $_GET['recipient'];
    if (isset($username) && isset($_GET['recipient']) && isset($_GET['message'])) {
        $message_text = htmlspecialchars($_GET['message']);
    
        if (!empty($username) && !empty($reciever) && !empty($message_text)) {
            $stmt = $pdo->prepare("INSERT INTO dm (sender, reciever, message, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
            $stmt->execute([$username, $reciever, $message_text]);

            echo "Message sent successfully!";
        } else {
            echo "All fields are required!";
        }
    } else {
        echo "Invalid data!";
    }
}
?>