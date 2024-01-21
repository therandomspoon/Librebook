<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

include 'config.php';
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedMode = $_POST['mode'];
    $stmt = $pdo->prepare('UPDATE users SET preferred_mode = ? WHERE id = ?');
    $stmt->execute([$selectedMode, $userId]);
    $_SESSION['preferred_mode'] = $selectedMode;
}
$stmt = $pdo->prepare('SELECT preferred_mode FROM users WHERE id = ?');
$stmt->execute([$userId]);
$currentMode = $stmt->fetchColumn();
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
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="messages">
    <h1>User Settings</h1>
    <hr>
    <form method="post" action="settings.php">
        <p></p>
        <label for="mode">Preferred Mode:</label>
        <select name="mode" id="mode">
            <option value="light" <?php echo ($currentMode === 'light') ? 'selected' : ''; ?>>Light</option>
            <option value="dark" <?php echo ($currentMode === 'dark') ? 'selected' : ''; ?>>Dark</option>
            <option value="blue" <?php echo ($currentMode === 'blue') ? 'selected' : ''; ?>>Blue</option>
        </select>
        <p></p>
        <hr>
        <p></p>
        <a href="deleteyou.php" style="color: red;">Delete your account</a>
        <p></p>
        <hr>
        <p></p>
        <a href="../logout.php">Logout</a>
        <p></p>
        <hr>
        <p></p>
        <a href="../profiles/sprofile.php">Edit my profile</a>
        <p></p>
        <hr>
        <button type="submit">Save Changes</button>
    </form>
    <br>
    <a href="../main.php">Go back to main page</a>
    </section>
</body>
</html>
