<!DOCTYPE html>
<html lang="en">
<style>
    a {
        font-size: 20px;
    }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../cmode.php';
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    if (empty($searchTerm)) {
        echo '<section id="messages">';
        echo "Please enter a search term.";
        echo '</section>';
    } else {
        $_SESSION['sterm'] = $searchTerm;
        $jsonFile = '../user-profiles.json';

        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $userProfiles = json_decode($jsonData, true);

            if ($userProfiles === null && json_last_error() !== JSON_ERROR_NONE) {
                echo '<section id="messages">';
                echo '<p>Error decoding JSON: ' . json_last_error_msg() . '</p>';
                echo '</section>';
            } else {
                $similarProfiles = [];

                foreach ($userProfiles['users'] as $profile) {
                    similar_text(strtolower($profile['username']), strtolower($searchTerm), $similarity);
                    if ($similarity >= 70) {
                        $similarProfiles[] = $profile;
                    }
                }

                if (empty($similarProfiles)) {
                    echo '<section id="messages">';
                    echo "<h1 style='text-align: center'>No similar users found or their accounts have been deleted.</h1>";
                    echo '<img src="../images/notfound.png" alt="image not found as well. its not your lucky day" style="display: block; margin-left: auto; margin-right: auto; max-width: 100%;">';
                    echo '  - LibreBot above';
                    echo '</section>';
                } else {
                    echo '<section id="messages">';
                    echo '<h1>Similar User Profiles</h1>';
                    foreach ($similarProfiles as $profile) {
                        echo "<a href='../profiles/rprofiles.php?search=" . urlencode($profile['username']) . "'>" . htmlspecialchars($profile['username']) . "</a><br>";
                    }
                    echo '</section>';
                }
            }
        } else {
            echo '<section id="messages">';
            echo "JSON file not found.";
            echo '</section>';
        }
    }
}
?>

</body>
</html>
