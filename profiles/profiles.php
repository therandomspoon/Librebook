<?php
session_start();
include '../cmode.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<style>
    #blading {
        border-radius: 50%;
        width: 150px;
        height: 150px;
    }
</style>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <a href="../main.php">Take me back!</a>
    <section id="sendamess">
<?php
include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function extractVideoId($url) {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $query);
    return isset($query['v']) ? $query['v'] : null;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $jsonFile = '../user-profiles.json';

    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $userProfiles = json_decode($jsonData, true);

        if ($userProfiles === null && json_last_error() !== JSON_ERROR_NONE) {
            echo '<p>Error decoding JSON: ' . json_last_error_msg() . '</p>';
        } else {
            $foundProfile = null;

            foreach ($userProfiles['users'] as $profile) {
                if ($profile['username'] === $searchTerm) {
                    $foundProfile = $profile;
                    break;
                }
            }

            if ($foundProfile) {
                echo '<meta property="og:title" content="' . htmlspecialchars($foundProfile['username']) . '">';
                echo '<meta property="og:description" content="' . htmlspecialchars($foundProfile['bio']) . '">';
                echo '<meta property="og:image" content="' . htmlspecialchars($foundProfile['pfp']) . '">';
                echo '<meta property="og:url" content="http://librebook.rf.gd/profiles/profiles.php?search=' . urlencode($foundProfile['username']) . '">';
                echo '<meta property="og:type" content="profile">';
                echo '<meta name="twitter:card" content="summary_large_image">';
                echo '<meta name="twitter:site" content="@thatrandomspoon">';

                echo '<section id="messages">';
                echo '<h1>Search result</h1>';
                echo '<img src="' . $foundProfile['pfp'] . '" alt="Profile Picture" id="blading">';
                echo '<h1>Username: ' . $foundProfile['username'] . '</h1>';
                echo '<p>Bio: ' . $foundProfile['bio'] . '</p>';
                echo '</section>';

                echo '<section id="messages">';

                try {
                    $escapedUsername = $foundProfile['username'];
                    $sql = "SELECT `name`, `message`, `timestamp`
                            FROM messages
                            WHERE `name` = :username
                            ORDER BY `timestamp` DESC";

                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':username', $escapedUsername, PDO::PARAM_STR);

                    if (!$stmt->execute()) {
                        throw new PDOException('Error in query execution.');
                    }

                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($rows) {
                        foreach ($rows as $row) {
                            $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                            $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
                            $timestamp = $row["timestamp"];

                            if (filter_var($message, FILTER_VALIDATE_URL) && 
                                (strpos($message, '.jpg') !== false || 
                                strpos($message, '.jpeg') !== false || 
                                strpos($message, '.png') !== false || 
                                strpos($message, '.webp') !== false)) {
                                echo "<div><b>" . $name . ":</b> <br> <img src='" . $message . "' alt='Image' style='max-width: 600px; height: 100%; max-height: 600px;'> <br> (Sent on: " . $timestamp . ")</div>";
                                echo "<hr>";
                            } elseif (strpos($message, 'https://ltbeta.epicsite.xyz/watch/?v=') !== false) {
                                $videoId = extractVideoId($message);
                                if ($videoId) {
                                    $videoUrl = "https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=" . $videoId . "&dl=dl&itag=18";
                                    echo "<div><b>" . $name . ":</b> <br> <video controls><source src='" . $videoUrl . "' type='video/mp4'></video> <br> (Sent on: " . $timestamp . ")</div>";
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
                } catch (PDOException $e) {
                    echo "Error executing query: " . $e->getMessage();
                }

                echo '</section>';
            } else {
                echo '<p>User not found</p>';
            }
        }
    } else {
        echo '<p>Error: User profiles file not found</p>';
    }
}
?>
    </section>
</body>
</html>