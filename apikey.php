<?php
session_start();
include 'config.php';
include 'cmode.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$user_name = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createkey'])) {
    $apiKey = bin2hex(random_bytes(32));
    $sql = "UPDATE users SET apikey = :apikey WHERE username = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':apikey', $apiKey, PDO::PARAM_STR);
    $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $_SESSION['message'] = "A new API Key has been generated: " . htmlspecialchars($apiKey);
    } else {
        $_SESSION['message'] = "Error generating API key.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API settings</title>
    <script>
        function confirmAPI() {
            return confirm("Are you sure you want to create a new API key?");
        }
    </script>
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="messages">
        <h1>Librebook API settings.</h1>
        <h1>Control your API key for Librebook.</h1>
        <hr>
        <?php
        if (!empty($message)) {
            echo $message;
        }

        $sql = "SELECT apikey FROM users WHERE username = :user_name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo "<p>Your API Key is: " . htmlspecialchars($result['apikey']) . "</p>";
            } else {
                echo "<p>No API key found for this user.</p>";
            }
        } else {
            echo "<p>Error retrieving API key.</p>";
        }
        ?>
        <hr>
        <form method="post" onsubmit="return confirmAPI()">
            <input id="bu1" type="submit" name="createkey" value="Create New API Key">
        </form>
        <br>
        <a href="../main.php">Go back to main page</a>
    </section>
</body>
</html>
