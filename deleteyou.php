<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameToDelete = $_SESSION['username'];

    $stmtDeleteMessages = $pdo->prepare('DELETE FROM messages WHERE name = ?');
    $stmtDeleteMessages->execute([$usernameToDelete]);

    $stmtDeleteUser = $pdo->prepare('DELETE FROM users WHERE username = ?');
    $stmtDeleteUser->execute([$usernameToDelete]);

    $jsonFile = '../user-profiles.json';

    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $userProfiles = json_decode($jsonData, true);

        if (isset($userProfiles['users'])) {
            foreach ($userProfiles['users'] as $index => $userProfile) {
                if ($userProfile['username'] === $usernameToDelete) {
                    array_splice($userProfiles['users'], $index, 1);
                    break;
                }
            }
        }

        file_put_contents($jsonFile, json_encode($userProfiles, JSON_PRETTY_PRINT));
    }

    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<?php
include 'cmode.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
</head>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="sendamess">
        <section id="messages">
            <h1>Sorry to see you go <?php echo  $_SESSION['username']; ?>!</h1>
            <h2>Delete Your Account</h2>
            <p>Are you sure you want to delete your account? It will be unrecoverable!</p>
            <form method="post" action="">
                <input type="submit" value="Delete Account">
            </form>
            <hr>
            <a href="main.php">No, take me back!</a>
        </section>
        <br><br>
    </section>
</body>
</html>
