<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../config.php';

session_start();
$userId = $_SESSION['user_id'] ?? null;
$loginuser = $_SESSION['username'] ?? null;
$searchusern = $_SESSION['searchTerm'] ?? null;

if (empty($loginuser)) {
    header("Location: notlogin.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$searchusern]);
    $resultofsearch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resultofsearch) {
        die("Error: User not found.");
    }

    $blockID = $resultofsearch['id'];
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM blocked WHERE blockedID = ? AND userID = ?');
    $stmt->execute([$blockID, $userId]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO blocked (blockedID, userID, date) VALUES (?, ?, CURRENT_TIMESTAMP)");
            $stmt->execute([$blockID, $userId]);
            echo "<p>Block success</p>";
            header("Location: ../profiles/rprofiles.php?search=" . urlencode($searchusern));
            exit();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM blocked WHERE blockedID = ? AND userID = ?");
            $stmt->execute([$blockID, $userId]);
            echo "<p>Unblock success</p>";
            header("Location: ../profiles/rprofiles.php?search=" . urlencode($searchusern));
            exit();
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>
