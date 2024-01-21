<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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

                $message = convertHashtagsToLinks($message);

                if (filter_var($message, FILTER_VALIDATE_URL) && 
                    (strpos($message, '.jpg') !== false || 
                     strpos($message, '.jpeg') !== false || 
                     strpos($message, '.png') !== false || 
                     strpos($message, '.webp') !== false)) {
                    echo "<div><b>" . $name . ":</b> <br> <img src='" . $message . "' alt='Image' style='max-width: 600px; height: 100%; max-height: 600px;'> <br> (Sent on: " . $timestamp . ")</div>";
                    echo "<hr>";
                } elseif (strpos($message, 'https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=') !== false) {
                    $videoId = extractVideoId($message);
                    if ($videoId) {
                        $videoUrl = "https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=" . $videoId . "&dl=dl&itag=18";
                        echo "<div><b>" . $name . ":</b> <br> <video controls><source src='" . $videoUrl . "' type='video/mp4'></video> <br> (Sent on: " . $timestamp . ")</div>";
                        echo "<hr>";
                    }
                } elseif (strpos($message, 'https://www.youtube.com/watch?v=') !== false) {
                    $videoId = extractVideoId($message);
                    if ($videoId) {
                        $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id=" . $videoId . "&dl=dl&itag=18";
                        echo "<div><b>" . $name . ":</b> <br> <video controls><source src='" . $videoUrl . "' type='video/mp4'></video> <br> (Sent on: " . $timestamp . ") </div>";
                        echo "<hr>";
                    }
                } elseif (strpos($message, 'https://lt.epicsite.xyz/watch/?v=') !== false) {
                    $videoId = extractVideoId($message);
                    if ($videoId) {
                        $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id=" . $videoId . "&dl=dl&itag=18";
                        echo "<div><b>" . $name . ":</b> <br> <video controls><source src='" . $videoUrl . "' type='video/mp4'></video> <br> (Sent on: " . $timestamp . ")</div>";
                        echo "<hr>";
                    }
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
# style='width: 211px; height: 148px;
?>
