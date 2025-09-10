<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

include 'config.php';

$user_name = $_SESSION['username'];

if (isset($_POST['delete_messages'])) {
    $sql = "DELETE FROM messages WHERE name = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "All your messages have been deleted successfully.";
    } else {
        echo "Error deleting messages.";
    }
} elseif (isset($_POST['delete_specmessage']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM messages WHERE name = :user_name AND id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('The specified message has been deleted successfully.');</script>";
        unset($id);
    } else {
        echo "<script>alert('Error deleting the specified message.');</script>";
        unset($id);
    }
}

?>

<?php
include 'cmode.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <style>
        .popup {
            display: none; 
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .popup-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            color: black;
            width: 80%;
            border: 2px #1877f2;
            border-radius: 8px;
            padding: 5px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .close {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        #bu1 {
            padding: 10px;
            background-color: #FF3131; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        #bu1:hover {
            background-color: #8B0000; 
        }
    </style>
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="messages">
        <h1>Librebook Data-center.</h1>
        <h1>Control your data on Librebook.</h1>
        <hr>
        <a id="openPopup" href="lpp1.pdf"><button>Librebook's privacy policy</button></a>
        <br>
        <br>
        <form method="post" onsubmit="return confirmDelete()">
            <input id="bu1" type="submit" name="delete_messages" value="Delete All Messages">
        </form>
        <br>
        <a href="../main.php">Go back to main page</a>
    </section>
    <section id="messages">
        <h1>Your messages on Librebook</h1>
        <?php
function extractVideoId($url) {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $query);
    return isset($query['v']) ? $query['v'] : null;
}

function convertNameToLink($name) {
    return '<a href="../profiles/rprofiles.php?search=' . urlencode($name) . '">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</a>';
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
        try {
            $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp`, `nsfw` FROM messages WHERE name = :name ORDER BY `timestamp` DESC");
            $stmt->bindParam(':name', $user_name, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                foreach ($result as $row) {
                    $id = $row["id"];
                    $counts = [];
                    $query = "SELECT reaction, COUNT(*) AS total FROM reactions WHERE messageid = ? GROUP BY reaction";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$id]);
                    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $counts[$r['reaction']] = $r['total'];
                    }
                    $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                    $nameLink = convertNameToLink($name);
                    $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
                    $timestamp = $row["timestamp"];
                    $message = extractID($message);
                    $message = convertHashtagsToLinks($message);
                    $kmode = $_SESSION['kmode'] ?? 'off';
                    $isNSFW = $row["nsfw"] ?? 0;
                    
                    $vStmt = $pdo->prepare("SELECT verified FROM users WHERE username = ?");
                    $vStmt->execute([$name]);
                    $vRow = $vStmt->fetch(PDO::FETCH_ASSOC);
                    $userIsVerified = $vRow["verified"] ?? 0;

                    $verifiedBadge = '';
                    if ($userIsVerified) {
                        $verifiedBadge = ' <img src="/images/verified2.svg" alt="Verified" style="vertical-align: middle; max-width: 18px; max-height: 18px;">';
                    }

                    $nameLink = '<a href="../profiles/rprofiles.php?search=' . urlencode($name) . '">' . $name . '</a>' . $verifiedBadge;


                    $realdate = date("l M j, Y h:i:s A", strtotime($timestamp));
                    echo "<div style='border-radius: 8px; margin-bottom: 12px; color: #ccc; font-family: sans-serif;'>";
                    if (filter_var($message, FILTER_VALIDATE_URL) && 
                        (strpos($message, '.jpg') !== false || strpos($message, '.jpeg') !== false || strpos($message, '.png') !== false || strpos($message, '.webp') !== false)) {
                        echo "  <p style='font-size: 18px; margin: 0 0 8px;'><b style='color: #4aa3ff;'>{$nameLink}:</b><br><img src='{$message}' alt='Image' style='max-width: auto; height: auto; max-height: auto;'></p>";
                    }
                    elseif (strpos($message, 'https://ltbeta.epicsite.xyz/videodata/non-hls.php?id=') !== false) {
                        $videoId = extractVideoId($message);
                        if ($videoId) {
                            $videoUrl = "https://ltbeta.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                            echo "  <p style='font-size: 18px; margin: 0 0 8px;'><b style='color: #4aa3ff;'>{$nameLink}:</b><br><video controls loading='lazy' poster='http://i.ytimg.com/vi/{$videoId}/mqdefault.jpg'><source src='{$videoUrl}' type='video/mp4'></video></p>";
                        }
                    }
                    elseif (strpos($message, 'https://www.youtube.com/watch?v=') !== false || strpos($message, 'https://lt.epicsite.xyz/watch/?v=') !== false) {
                        $videoId = extractVideoId($message);
                        if ($videoId) {
                            $videoUrl = "https://lt.epicsite.xyz/videodata/non-hls.php?id={$videoId}&dl=dl&itag=18";
                            echo "  <p style='font-size: 18px; margin: 0 0 8px;'><b style='color: #4aa3ff;'>{$nameLink}:</b><br><video controls loading='lazy' poster='http://i.ytimg.com/vi/{$videoId}/mqdefault.jpg'><source src='{$videoUrl}' type='video/mp4'></video></p>";
                        }
                    }
                    else {
                        echo "  <p style='font-size: 18px; margin: 0 0 8px;'><b style='color: #4aa3ff;'>{$nameLink}:</b> {$message}</p>";
                    }
                    if ($isNSFW) {
                         echo "<p style='color:red;'>NSFW</p>";
                    }
                    echo "  <p style='font-size: 12px; color: gray;'>Posted on: {$realdate}</p>";
                    echo "  <form method='post' onsubmit='return confirm(\"Are you sure you want to delete this message? This action cannot be undone.\");'>";
                    echo "      <input type='hidden' name='id' value='{$id}'>";
                    echo "      <input type='submit' id='bu1' name='delete_specmessage' value='Delete This Message'>";
                    echo "  </form>";
                    echo "<hr>";
                }
            } else {
                echo "No messages.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        ?>
    <script>
        var popup = document.getElementById("myPopup");
        var btn = document.getElementById("openPopup");
        var span = document.getElementById("closePopup");

        btn.onclick = function() {
            popup.style.display = "block";
        }

        span.onclick = function() {
            popup.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == popup) {
                popup.style.display = "none";
            }
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete all your messages? This action cannot be undone.");
        }
    </script>
</body>
</html>