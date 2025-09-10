<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config.php';

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
    return preg_replace('/#(\w+)/', '<a href="../messages/hashtag.php?tag=$1">#$1</a>', $message);
}

$messageId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$messageId) {
    header("Location: /main.php");
    exit();
}

$stmt = $pdo->prepare("SELECT `id`, `name`, `message`, `timestamp`
                       FROM messages
                       WHERE id = :id");
$stmt->bindParam(':id', $messageId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "Message not found.";
    exit();
}

$name = htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8');
$rawMessage = htmlspecialchars($row["message"], ENT_QUOTES, 'UTF-8');
$timestamp = $row["timestamp"];
$id = $row["id"];

$parsedMessage = extractID($rawMessage);
$parsedMessage = convertHashtagsToLinks($parsedMessage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librebook - Message from <?php echo $name; ?></title>
    <meta property="og:title" content="<?php echo htmlspecialchars($rawMessage); ?>">
    <meta property="og:description" content="Sent by <?php echo $name; ?> at <?php echo $timestamp; ?>">
    <meta property="og:url" content="https://librebook.co.uk/messages/spmessages.php?id=<?php echo $id; ?>">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@thatrandomspoon">
    <link rel="stylesheet" href="/css/blue.css">
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <section id="messages">
        <?php
        if (filter_var($rawMessage, FILTER_VALIDATE_URL) &&
            (str_ends_with($rawMessage, '.jpg') || str_ends_with($rawMessage, '.jpeg') || str_ends_with($rawMessage, '.png') || str_ends_with($rawMessage, '.webp'))) {
            echo "<div><b>{$name}:</b><br><img src='{$rawMessage}' style='max-width:600px;'><br>(Sent on: {$timestamp})</div><hr>";
        } else {
            echo "<div><b>{$name}:</b> {$parsedMessage} <br>(Sent on: {$timestamp})</div>";
        }
        ?>
    </section>
</body>
</html>
