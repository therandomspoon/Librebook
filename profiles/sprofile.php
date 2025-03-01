<?php
session_start();
include '../config.php'; // Include your PDO config

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $foundProfile = null;

    // Retrieve profile using PDO
    $stmt = $pdo->prepare("SELECT username, pfp, bio FROM profiles WHERE username = ?");
    $stmt->execute([$username]);
    $foundProfile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$foundProfile) {
        echo '<p>User profile not found</p>';
    }
} else {
    header('Location: ../login.html');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPfp = isset($_POST['new_pfp']) ? $_POST['new_pfp'] : $foundProfile['pfp'];
    $newBio = isset($_POST['new_bio']) ? htmlspecialchars($_POST['new_bio']) : $foundProfile['bio'];

    // Update profile using PDO
    $stmt = $pdo->prepare("UPDATE profiles SET pfp = ?, bio = ? WHERE username = ?");
    $stmt->execute([$newPfp, $newBio, $username]);

    if ($stmt->rowCount() > 0) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo '<p>Error updating profile</p>';
    }
}
?>

<?php
include '../cmode.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<style>
    img {
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
    <br>
    <div id="helloworld">
        <a href="../main.php">Take me back!</a>
    </div>
    <section id="sendamess">
        <section id="messages">
            <h1>My Profile</h1>
            <img src="<?php echo $foundProfile['pfp']; ?>" alt="Profile Picture">
            <h1>Username: <?php echo $foundProfile['username']; ?></h1>
            <p>Bio: <?php echo $foundProfile['bio']; ?></p>
            <h2>Edit Profile</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="new_pfp">New Profile Picture URL:</label><br>
                <input type="text" name="new_pfp" id="new_pfp" placeholder="Enter new profile picture URL" value="<?php echo $foundProfile['pfp']; ?>">
                <br>
                <label for="new_bio">New Bio:</label>
                <br>
                <textarea name="new_bio" id="new_bio" placeholder="Enter new bio" rows="4" cols="50"><?php echo $foundProfile['bio']; ?></textarea>
                <br>
                <button type="submit">Update Profile</button>
            </form>
        </section>
        <br></br>
    </section>
</body>
</html>
