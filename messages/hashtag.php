<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    header('Location: ../index.php');
    exit();
}
?>
<?php
// so for some stupid reason the code always breaks unless its in its own 'container' of sorts dont know why and probably wont fix - therandomspoon
include '../cmode.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <div id="helloworld">
        <?php
        echo 'Welcome back ' . htmlspecialchars($username) . '!';
        ?>
        <p></p>
        <a href="../deleteyou.php">Delete your account</a><a href="../settings.php" style="float: right;">Go to Settings</a>
        <p></p>
        <a href="../logout.php">Logout</a><a href="../profiles/sprofile.php" style="float: right;">See my profile</a>
        <p></p>
        <a href="../main.php">Take me back!</a>
    </div>
</body>
<?php
include '../config.php';

try {
    $hashtag = isset($_GET['tag']) ? $_GET['tag'] : '';

    echo "<h1 style='text-align: center;'>Librebook hashtag: #$hashtag</h1>";

    $query = "SELECT `name`, `message`, `timestamp`
              FROM messages
              WHERE `message` LIKE :hashtag
              ORDER BY `timestamp` DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':hashtag', "%#$hashtag%", PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        foreach ($result as $row) {
            $name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
            $message = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
            $timestamp = $row["timestamp"];
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
            $message = extractID($message);
            echo "<section id='messages'>";
            echo "<div><b>" . $name . ":</b> " . $message . " (Sent on: " . $timestamp . ")</div>";
            echo "<hr>";
            echo "</section>";
        }
    } else {
        echo "No messages with this hashtag.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
