<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        a {
            font-size: 20px;
        }
        #head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        #messages {
            text-align: center;
            margin: 20px;
        }
        img {
            max-width: 100%;
        }
    </style>
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" alt="Librebook Logo" style="height: 125px; width: 125px;">
        <h1 id="headl">Librebook</h1>
    </section>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();
    include '../cmode.php';

    function displayMessage($message) {
        echo '<section id="messages">';
        echo $message;
        echo '</section>';
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

        if (empty($searchTerm)) {
            displayMessage("Please enter a search term.");
        } else {
            $_SESSION['sterm'] = $searchTerm;
            $jsonFile = '../user-profiles.json';

            if (file_exists($jsonFile)) {
                $jsonData = file_get_contents($jsonFile);
                $userProfiles = json_decode($jsonData, true);

                if ($userProfiles === null && json_last_error() !== JSON_ERROR_NONE) {
                    displayMessage('<p>Error decoding JSON: ' . json_last_error_msg() . '</p>');
                } else {
                    $similarProfiles = [];

                    foreach ($userProfiles['users'] as $profile) {
                        similar_text(strtolower($profile['username']), strtolower($searchTerm), $similarity);
                        if (str_contains(strtolower($profile['username']), strtolower($searchTerm))) {
                            $similarProfiles[] = $profile;
                        }
                    }

                    if (empty($similarProfiles)) {
                        displayMessage("<h1>No similar users found or their accounts have been deleted.</h1>
                        <img src='../images/notfound.png' alt='image not found as well. its not your lucky day'>
                        <p>- LibreBot above</p>");
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
                displayMessage("JSON file not found.");
            }
        }
    }
    ?>
</body>
</html>
