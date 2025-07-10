<?php
session_start();
include '../config.php'; // Include your PDO config

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $foundProfile = null;

    // Retrieve profile using PDO
    $stmt = $pdo->prepare("SELECT username, pfp, bio, bimg FROM profiles WHERE username = ?");
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
    $newBimg = isset($_POST['new_bimg']) ? $_POST['new_bimg'] : $foundProfile['bimg'];
    $newBio = isset($_POST['new_bio']) ? htmlspecialchars($_POST['new_bio']) : $foundProfile['bio'];
    $procolour = isset($_POST['procolour']) ? htmlspecialchars($_POST['procolour']) : 'libreblue';
    $stmt = $pdo->prepare("UPDATE profiles SET pfp = ?, bio = ?, PROCOLOUR = ?, bimg = ? WHERE username = ?");
    $stmt->execute([$newPfp, $newBio, $procolour, $newBimg, $username]);

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
    #blading {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid white;
        position: absolute;
        bottom: -75px;
        left: 20px;
        z-index: 2;
        background-color: white;
    }

    .profile-header {
        position: relative;
        max-width: 900px;
        margin: 0 auto 90px auto;
    }

    #banner {
        width: 100% !important;
        max-width: 900px !important;
        max-height: 450px !important;
        height: auto !important;
        display: block;
        border-radius: 0%;
    }
</style>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <div id="helloworld" style="position: sticky; align-self: flex-start; float: left;">
        <h1>Return to the main page</h1>
        <button onclick='location="../main.php"'>Take me back!</button>
    </div>
    <section id="sendamess">
        <section id="messages">
            <h1>My Profile</h1>
            <div class="profile-header">
            <img id="banner" src="<?php echo htmlspecialchars($foundProfile['bimg']);  ?>" alt="Banner">
            <img src="<?php echo $foundProfile['pfp']; ?>" alt="Profile Picture" id="blading">
            </div>
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
                <label for="new_pfp">New Profile Banner URL:</label><br>
                <input type="text" name="new_bimg" id="new_pfp" placeholder="Enter new profile banner URL" value="<?php echo $foundProfile['bimg']; ?>">
                <br>
                <button type="submit">Update Profile</button>
            </form>
            <hr>
            <h1>Profile colour</h1>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="procolour">Select your desired profile color:</label>
                <input type="color" id="procolour" name="procolour" value="#ff0000"><br><br>
                <button type="submit">Update Profile Colour</button>
            </form>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="procolour" value="None">
                <button type="submit">Reset Profile Colour</button>
            </form>
        </section>
        <br></br>
    </section>
</body>
</html>
