<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../config.php';
session_start();

$pagenum = $_SESSION['page'];

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

function convertNameToLink($name) {
    return '<a href="../profiles/rprofiles.php?search=' . urlencode($name) . '">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</a>';
}

try {
    $messagenum = ($pagenum - 1) * $offsetnum;
    $messageId = isset($_GET['id']) ? intval($_GET['id']) : null;
    if ($messageId) {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp` FROM messages WHERE id = :id");
        $stmt->bindParam(':id', $messageId, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp` FROM messages ORDER BY `timestamp` DESC LIMIT 10 OFFSET :offset");
        $stmt->bindParam(':offset', $messagenum, PDO::PARAM_INT);
    }
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
            if ($kmode == 'on') {
                $forbiddenWords = json_decode(file_get_contents('bad-words.json'), true);
                $lowercase_message_text = strtolower($message);
                foreach ($forbiddenWords as $word) {
                    if (strpos($lowercase_message_text, strtolower($word)) !== false) {
                        $message = str_ireplace($word, '[CENSORED BY KIDS MODE] ', $message);
                    }
                }
            }
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
            echo "  <p style='font-size: 0.9em; color: #888; margin: 0 0 10px;'>Sent on: {$realdate}</p>";
            echo "<div style='color: #ccc; margin-bottom: 8px;'>";
            echo '👍: ' . ($counts['like'] ?? 0) . ' ';
            echo '👎: ' . ($counts['dislike'] ?? 0) . ' ';
            echo '❤️: ' . ($counts['love'] ?? 0) . ' ';
            echo '😲: ' . ($counts['shock'] ?? 0);
            echo "</div>";
            echo "<div style='display: flex; align-items: center; gap: 10px;'>";
            $dropdownId = "dropdown_" . $id;
            echo "<a href='../messages/reply.php?id=" . urlencode($id) . "' style='color: #4aa3ff; text-decoration: none;'><button>Reply</button></a>";
            echo '<div class="dropdown">
                  <button onclick="toggleDropdown(\'' . $dropdownId . '\')" class="dropbtn">React!</button>
                  <div id="' . $dropdownId . '" class="dropdown-content">
                    <a href="messages/react.php?id=' . $id . '&react=like">👍</a>
                    <a href="messages/react.php?id=' . $id . '&react=dislike">👎</a>
                    <a href="messages/react.php?id=' . $id . '&react=love">😍</a>
                    <a href="messages/react.php?id=' . $id . '&react=shock">😰</a>
                  </div>
                </div>';
            echo "</div>";
            echo "<hr style='border-top: 1px #ccc;'>";
            echo "</div>";
        }
    } else {
        echo "No messages.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<script>
function toggleDropdown(id) {
  const el = document.getElementById(id);
  el.classList.toggle("show");
}

window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>
<style>

.dropbtn {
  color: white;
  font-size: 14px;
  border: none;
  cursor: pointer;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  position: absolute;
  left: 100%;
  top: 0;
  background-color: #1877f2;
  overflow: hidden;
  border-radius: 5px !important;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  white-space: nowrap;
  opacity: 0;
  transform: translateX(-40px);
  pointer-events: none;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.dropdown-content.show {
  opacity: 1;
  transform: translateX(0);
  pointer-events: auto;
}


.dropdown-content a {
  display: inline-block;
  padding: 8px 12px;
  text-decoration: none;
  color: black;
}

.dropdown-content a:hover {
  background-color: #115293;
}
</style>
