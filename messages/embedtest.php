<?php
// Simple database connection — update this to your actual config
include '../config.php';

$messageId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$messageId) {
    header("Location: /main.php");
    exit();
}

$stmt = $pdo->prepare("SELECT id, name, message, timestamp FROM messages WHERE id = :id");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librebook Message from <?php echo $name; ?></title>
    
    <!-- Essential Open Graph Meta Tags -->
    <meta property="og:title" content="Message from <?php echo $name; ?>">
    <meta property="og:description" content="<?php echo $rawMessage; ?>">
    <meta property="og:url" content="https://librebook.co.uk/messages/public-message.php?id=<?php echo $id; ?>">
    <meta property="og:type" content="article">
    
    <!-- Optional: Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Message from <?php echo $name; ?>">
    <meta name="twitter:description" content="<?php echo $rawMessage; ?>">
</head>
<body>
    <h1>Message from <?php echo $name; ?></h1>
    <p><?php echo nl2br($rawMessage); ?></p>
    <p><small>Sent at: <?php echo $timestamp; ?></small></p>
</body>
</html>
