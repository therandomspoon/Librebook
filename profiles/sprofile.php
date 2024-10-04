<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $jsonFile = '../user-profiles.json';

    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $userProfiles = json_decode($jsonData, true);

        if ($userProfiles === null && json_last_error() !== JSON_ERROR_NONE) {
            echo '<p>Error decoding JSON: ' . json_last_error_msg() . '</p>';
        } else {
            $foundProfile = null;

            foreach ($userProfiles['users'] as $profile) {
                if ($profile['username'] === $username) {
                    $foundProfile = $profile;
                    break;
                }
            }

            if (!$foundProfile) {
                echo '<p>User profile not found</p>';
            }
        }
    } else {
        echo '<p>Error: User profiles file not found</p>';
    }
} else {
    header('Location: ../login.html');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPfp = isset($_POST['new_pfp']) ? $_POST['new_pfp'] : $foundProfile['pfp'];
    $newBio = isset($_POST['new_bio']) ? htmlspecialchars($_POST['new_bio']) : $foundProfile['bio'];
    
    foreach ($userProfiles['users'] as &$profile) {
        if ($profile['username'] === $username) {
            $profile['pfp'] = $newPfp;
            $profile['bio'] = $newBio;
            break;
        }
    }
    
    file_put_contents($jsonFile, json_encode($userProfiles, JSON_PRETTY_PRINT));
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
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
    <a href="../main.php">Take me back!</a>
    <section id="sendamess">
        <section id="messages">
            <h1>My Profile</h1>
            <img src="<?php echo $foundProfile['pfp']; ?>" alt="Profile Picture">
            <h1>Username: <?php echo $foundProfile['username']; ?></h1>
            <p>Bio: <?php echo $foundProfile['bio']; ?></p>
            <p>Registered Email: <?php echo $foundProfile['email']; ?></p>
            <h2>Edit Profile</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="new_pfp">New Profile Picture:</label><br>
                <?php echo "-- current pfp:" . $foundProfile['pfp']; ?>
                <p></p>
                <input style="width: 50%;" type="text" name="new_pfp" list="pfp_list" id="new_pfp" placeholder="Enter new profile picture URL" value="">
                <datalist id="pfp_list">
                    <option value="red default" data-path="../pfps/red.webp">red default</option>
                    <option value="blue default" data-path="../pfps/blue.webp">blue defaultT</option>
                    <option value="green default" data-path="../pfps/green.webp">green default</option>
                    <option value="yellow default" data-path="../pfps/yellow.webp">yellow default</option>
                    <option value="default" data-path="../pfps/empty.webp">default</option>
                </datalist>
            <br>
            <label for="new_bio">New Bio:</label>
            <br>
            <textarea style="max-height: 20%;" name="new_bio" id="new_bio" placeholder="Enter new bio" rows="4" cols="50"><?php echo $foundProfile['bio']; ?></textarea>
            <hr>
            <button type="submit">Update Profile</button>
            </form>
<script>
    document.getElementById('new_pfp').addEventListener('input', function () {
        var value = this.value;
        var options = document.getElementById('pfp_list').options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value === value) {
                this.value = options[i].getAttribute('data-path');
                break;
            }
        }
    });
</script>
        </section>
        <br></br>
    </section>
</body>
</html>
