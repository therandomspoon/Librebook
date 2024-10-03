<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../cmode.php';
include '../config.php';
$_SESSION['mostrecent'] = isset($_GET['id']) ? $_GET['id'] : '';

$sui = isset($_SESSION['sui']) ? $_SESSION['sui'] : null;
$oname = isset($_SESSION['oname']) ? $_SESSION['oname'] : null;
$omessage = isset($_SESSION['omessage']) ? $_SESSION['omessage'] : null;
$otime = isset($_SESSION['otimestamp']) ? $_SESSION['otimestamp'] : null;
$oid = isset($_SESSION['oid']) ? $_SESSION['oid'] : null;

function extractVideoId($url) {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $query);
    return isset($query['v']) ? $query['v'] : null;
}

function extractID($string) {
    $symbolPosition = strpos($string, '[#@');
    if ($symbolPosition !== false) {
        $substringAfterSymbol = substr($string, $symbolPosition);
        $semicolonPosition = strpos($substringAfterSymbol, ';');
        if ($semicolonPosition !== false) {
            $numbers = substr($substringAfterSymbol, 3, $semicolonPosition - 3);
            $numbers = preg_replace("/[^0-9]/", "", $numbers);
            $replacement = "<a href='../messages/spmessages.php/?id=$numbers'>Reply to</a>";
            $string = substr_replace($string, $replacement, $symbolPosition, $semicolonPosition + 1);
        }
    }

    return $string;
}

function convertHashtagsToLinks($message) {
    $pattern = '/#(\w+)/';
    $messageWithLinks = preg_replace($pattern, '<a href="../messages/hashtag.php?tag=$1">#$1</a>', $message);
    return $messageWithLinks;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        #blading {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <div id="helloworld">
    <a href="../main.php">Take me back!</a>
    </div>
    <section id="sendamess">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

        if (empty($searchTerm)) {
            echo '<section id="messages">';
            echo "Please enter a search term.";
            echo '</section>';
        } else {
            $_SESSION['sterm'] = $searchTerm;
            $jsonFile = '../user-profiles.json';

            if (file_exists($jsonFile)) {
                $jsonData = file_get_contents($jsonFile);
                $userProfiles = json_decode($jsonData, true);

                if ($userProfiles === null && json_last_error() !== JSON_ERROR_NONE) {
                    echo '<section id="messages">';
                    echo '<p>Error decoding JSON: ' . json_last_error_msg() . '</p>';
                    echo '</section>';
                } else {
                    $foundProfile = null;

                    foreach ($userProfiles['users'] as $profile) {
                        if (stripos($profile['username'], $searchTerm) !== false) {
                            $foundProfile = $profile;
                            $_SESSION['searchTerm'] = $searchTerm;
                            break;
                        }
                    }

                    if ($foundProfile === null) {
                        echo '<section id="messages">';
                        echo "<h1 style='text-align: center'>User does not exist or their account has been deleted.</h1>";
                        echo '<img src="../images/notfound.png" alt="image not found as well. its not your lucky day" style="display: block; margin-left: auto; margin-right: auto;">';
                        echo '</section>';
                    }
                }
            } else {
                echo '<section id="messages">';
                echo "JSON file not found.";
                echo '</section>';
            }

            if ($foundProfile) {
                echo '<meta property="og:title" content="' . htmlspecialchars($foundProfile['username']) . '">';
                echo '<meta property="og:description" content="' . htmlspecialchars($foundProfile['bio']) . '">';
                echo '<meta property="og:image" content="' . htmlspecialchars($foundProfile['pfp']) . '">';
                echo '<meta property="og:url" content="http://librebook.co.uk/profiles/rprofiles.php?search=' . urlencode($foundProfile['username']) . '">';
                echo '<meta property="og:type" content="profile">';
                echo '<meta name="twitter:card" content="summary_large_image">';
                echo '<meta name="twitter:site" content="@thatrandomspoon">';

                echo '<section id="messages">';
                echo '<h1>Search result</h1>';
                echo '<img src="' . htmlspecialchars($foundProfile['pfp']) . '" alt="Profile Picture" id="blading">';
                echo '<h1>Username: ' . htmlspecialchars($foundProfile['username']) . '</h1>';
                echo '<p>Bio: ' . htmlspecialchars($foundProfile['bio']) . '</p>';
                echo '<form method="post" action="follow.php">';
                echo '<input type="submit" value="Follow/Unfollow" />';
                echo '</form>';

                $stmt = $pdo->prepare("SELECT following FROM users WHERE username = ?");
                $stmt->execute([$foundProfile['username']]);
                $followers = $stmt->fetchColumn();

                if ($followers !== null) {
                    $followersList = explode(',', $followers);
                    echo "Followers for " . htmlspecialchars($foundProfile['username']) . ": <br>";
                    foreach ($followersList as $follower) {
                        echo htmlspecialchars($follower) . '<br>';
                    }
                } else {
                    echo "User " . htmlspecialchars($foundProfile['username']) . " has no followers.";
                }

                echo '</section>';

                echo '<section id="messages">';
                try {
                    $messageId = isset($_GET['id']) ? intval($_GET['id']) : null;

                    if ($messageId) {
                        $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp`
                                            FROM messages
                                            WHERE name = :name
                                            ORDER BY `timestamp` DESC");

                        $stmt->bindParam(':name', $foundProfile['username'], PDO::PARAM_STR);
                        $stmt->execute();

                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp`
                                            FROM messages
                                            WHERE name = :name
                                            ORDER BY `timestamp` DESC");

                        $stmt->bindParam(':name', $foundProfile['username'], PDO::PARAM_STR);
                        $stmt->execute();

                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    if ($result) {
                        foreach ($result as $row) {
                            $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                            $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
                            $timestamp = $row["timestamp"];
                            $id = $row["id"];
                            
                            $message = extractID($message);
                            $message = convertHashtagsToLinks($message);

                            if (filter_var($message, FILTER_VALIDATE_URL) && 
                                (strpos($message, '.jpg') !== false || 
                                strpos($message, '.jpeg') !== false || 
                                strpos($message, '.png') !== false || 
                                strpos($message, '.webp') !== false)) {
                                echo "<div><b>{$name}:</b> <br> <img src='{$message}' alt='Image' style='max-width: 600px; height: 100%; max-height: 600px;'> <br> (Sent on: {$timestamp})</div>";
                                echo "<hr>";
                            } elseif (strpos($message, 'https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=') !== false) {
                                $videoId = extractVideoId($message);
                                if ($videoId) {
                                    $videoUrl = "https://ltbeta.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                                    echo "<div><b>{$name}:</b> <br> <video controls><source src='{$videoUrl}' type='video/mp4'></video> <br> (Sent on: {$timestamp})</div>";
                                    echo "<hr>";
                                }
                            } elseif (strpos($message, 'https://www.youtube.com/watch?v=') !== false) {
                                $videoId = extractVideoId($message);
                                if ($videoId) {
                                    $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                                    echo "<div><b>{$name}:</b> <br> <video controls><source src='{$videoUrl}' type='video/mp4'></video> <br> (Sent on: {$timestamp})</div>";
                                    echo "<hr>";
                                }
                            } elseif (strpos($message, 'https://lt.epicsite.xyz/watch/?v=') !== false) {
                                $videoId = extractVideoId($message);
                                if ($videoId) {
                                    $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                                    echo "<div><b>{$name}:</b> <br> <video controls><source src='{$videoUrl}' type='video/mp4'></video> <br> (Sent on: {$timestamp})</div>";
                                    echo "<hr>";
                                }
                            } elseif (strstr($message, "|| reply - ")) {
                                echo "<div><b>{$name}:</b> Reply to <a href='/messages/spmessages.php/?id=". $oid . "'>" . $oname . "</a> {$message} (Sent on: {$timestamp})</div>";
                                echo " <a href='../messages/reply.php?name=" . urlencode($name) . "&message=" . urlencode($message) . "&timestamp=" . urlencode($timestamp) . "&id=" . urlencode($id) . "'>Reply</a>";
                                echo "<hr>";
                            } else {
                                echo "<div><b>{$name}:</b> {$message} (Sent on: {$timestamp})</div>";
                                echo " <a href='../messages/reply.php?name=" . urlencode($name) . "&message=" . urlencode($message) . "&timestamp=" . urlencode($timestamp) . "&id=" . urlencode($id) . "'>Reply</a>";
                                echo "<hr>";
                            }
                        }
                    } else {
                        echo "No messages.";
                    }
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                }
            }
        }
    }
    ?>
    </section>  
</body>
</html>
