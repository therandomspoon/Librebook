<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link rel="stylesheet" href="../css/mainsite.css">
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
                    $stmt->execute();

                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($rows) {
                        foreach ($rows as $row) {
                            $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
                            $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
                            $timestamp = $row["timestamp"];

                            if (filter_var($message, FILTER_VALIDATE_URL) && (strpos($message, '.jpg') !== false || strpos($message, '.jpeg') !== false || strpos($message, '.png') !== false)) {
                                echo "<div><b>" . $name . ":</b> <br> <img src='" . $message . "' alt='Image' style='width: 211px; height: 148px;'> <br> (Sent on: " . $timestamp . ")</div>";
                            } else {
                                echo "<div><b>" . $name . ":</b> " . $message . " (Sent on: " . $timestamp . ")</div>";
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