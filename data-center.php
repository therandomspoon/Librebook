<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

include 'config.php';

$user_name = $_SESSION['username'];

if (isset($_POST['delete_messages'])) {
    $sql = "DELETE FROM messages WHERE name = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "All your messages have been deleted successfully.";
    } else {
        echo "Error deleting messages.";
    }
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
    <title>User Settings</title>
    <style>
        /* Existing styles */
        .popup {
            display: none; 
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .popup-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            color: black;
            width: 80%;
            border: 2px #1877f2;
            border-radius: 8px;
            padding: 5px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .close {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        #bu1 {
            padding: 10px;
            background-color: #FF3131; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        #bu1:hover {
            background-color: #8B0000; 
        }
    </style>
</head>
<body>
    <section id="head">
        <img src="/images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="messages">
        <h1>Librebook Data-center.</h1>
        <h1>Control your data on Librebook.</h1>
        <hr>
        <a id="openPopup" href="lpp1.pdf">Librebook's privacy policy</button>
        <hr>
        <form method="post" onsubmit="return confirmDelete()">
            <input id="bu1" type="submit" name="delete_messages" value="Delete All Messages">
        </form>
        <br>
        <br>
        <a href="../main.php">Go back to main page</a>
    </section>

    <script>
        var popup = document.getElementById("myPopup");
        var btn = document.getElementById("openPopup");
        var span = document.getElementById("closePopup");

        btn.onclick = function() {
            popup.style.display = "block";
        }

        span.onclick = function() {
            popup.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == popup) {
                popup.style.display = "none";
            }
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete all your messages? This action cannot be undone.");
        }
    </script>
</body>
</html>