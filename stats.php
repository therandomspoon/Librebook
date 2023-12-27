<?php
include 'config.php';

$hitCounterFile = 'mainhitcounter.txt';
$hitCount = (int)file_get_contents($hitCounterFile);
file_put_contents($hitCounterFile, $hitCount);
$usersQuery = $pdo->query("SELECT COUNT(*) as userCount FROM users");
$userData = $usersQuery->fetch(PDO::FETCH_ASSOC);
$userCount = $userData['userCount'];
$messagesQuery = $pdo->query("SELECT COUNT(*) as messageCount FROM messages");
$messageData = $messagesQuery->fetch(PDO::FETCH_ASSOC);
$messageCount = $messageData['messageCount'];
$mostCommonUsernameQuery = $pdo->query("SELECT name, COUNT(*) as nameCount FROM messages GROUP BY name ORDER BY nameCount DESC LIMIT 1");
$mostCommonUsernameData = $mostCommonUsernameQuery->fetch(PDO::FETCH_ASSOC);
$mostCommonUsername = $mostCommonUsernameData['name'];
$mostCommonUsernameCount = $mostCommonUsernameData['nameCount'];
?>
<!DOCTYPE html>
<html lang="en">
<style>
    #sendamess, #messages {
        font-size: 20px;
    }
    img {
        width: 600px;
        height: 500px;
    }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/mainsite.css">
</head>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="sendamess">
        <section id="messages">
            <h1>This instance of Librebook's stats</h1>
            <p>If the hits exceed 50,000 hits in 24 hours, then the site shall be shut down by the host of the site</p>
            <?php
            echo "<p>Total Users: $userCount</p>";
            echo "<p>Total Messages: $messageCount</p>";
            echo "<p>Main.php (messages page) Hits: $hitCount</p>";
            echo "<p>User who sent the most messages: $mostCommonUsername (Count: $mostCommonUsernameCount)</p>";
            ?>
        </section>
        <br></br>
    </section>
    <div class="creditbar">
        <a href="../masthead.html" id="excempta">Masthead</a>
    </div>
</body>
</html>
