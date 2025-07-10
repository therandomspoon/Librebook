<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
} else {
    header('Location: ../index.php');
    exit();
}

$_SESSION['sui'] = 
include '../config.php';
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['currentpass'] = $user['password'];
if ($_SESSION['sudopassword'] != $_SESSION['currentpass']) { //* comparing the cooler password to the one in sql to see if their account still exists
    session_destroy();
} else {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <section id="head">
        <img src="/images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <div id="helloworld">
        <?php
        echo 'Welcome' . htmlspecialchars($username) . '!';
        ?>
        <p></p>
        <a href="deleteyou.php">Delete your account</a><a href="../settings.php" style="float: right;">Go to Settings</a>
        <p></p>
        <a href="../logout.php">Logout</a><a href="../profiles/sprofile.php" style="float: right;">See my profile</a>
        <p></p>
    </div>
    <br>
    <section id="messages">
        <?php echo $_SESSION['sui'] ?>
    </section>
    <br></br>
    </section>
    <script>
        var userID = <?php echo json_encode($username); ?>;
        function updateMessages() {
            $.ajax({
                type: "GET",
                url: "../messages/messages.php"<?php $messageId ?>,
                success: function (response) {
                    $("#messageList").html(response);
                    alert('Messages loaded');
                    console.log('Messages loaded');
                },
                error: function (xhr, status, error) {
                    $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
                }
            });
        }
        updateMessages();
        });
    </script>
    <script src="../script.js"></script>
</body>
</html>
<?php
}
?>
<?php
include '../cmode.php';
?>
