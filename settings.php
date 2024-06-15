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
    $selectedMode = $_POST['kmode'];
    $stmt = $pdo->prepare('UPDATE users SET kids = ? WHERE id = ?');
    $stmt->execute([$selectedMode, $userId]);
    $_SESSION['kmode'] = $selectedMode;
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
<style>
#kids {
    color: green;
}
</style>
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
            <option value="liberatube" <?php echo ($currentMode === 'liberatube') ? 'selected' : ''; ?>>Liberatube collab</option>
            <option value="nothing" <?php echo ($currentMode === 'nothing') ? 'selected' : ''; ?>>De-bloated&trade;</option>
            <option value="opposite" <?php echo ($currentMode === 'opposite') ? 'selected' : ''; ?>>Opposite day - Become thankful for the colour scheme!</option>
            <option value="nature" <?php echo ($currentMode === 'nature') ? 'selected' : ''; ?>>Nature day - In celebration of Earth day!</option>
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
        <p></p>
        <?php echo "Kids mode is currently: <h1 style='color: #1877f2;'>" . $_SESSION['kmode'] . "</h1>";?>
        <p></p>
        <hr>
        <p></p>
        <select name="kmode" id="kmode">
            <option value="off" <?php echo ($currentMode === 'off') ? 'selected' : ''; ?>>Off</option>
            <option value="on" <?php echo ($currentMode === 'on') ? 'selected' : ''; ?>>On</option>
        </select>
        <p></p>
        <hr>
        <p></p>
        <button type="submit">Save Changes</button>
    </form>
    <br>
    <a href="../main.php">Go back to main page</a>
    </section>
</body>
</html>