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

include 'config.php';
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
        <img src="/images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <div id="helloworld">
        <?php
        echo 'Welcome back ' . htmlspecialchars($username) . '!';
        ?>
        <p></p>
        <a href="deleteyou.php">Delete your account</a><a href="../settings.php" style="float: right;">Go to Settings</a>
        <p></p>
        <a href="../logout.php">Logout</a><a href="../profiles/sprofile.php" style="float: right;">See my profile</a>
        <p></p>

    </div>
    <section id="searchbar">
        <form action="../profiles/profiles.php" id="searchform" method="get">
            <input id="searchbut" type="text" placeholder="Search profiles.." name="search">
            <button type="submit">Search!<i class="fa fa-search"></i></button>
        </form>
    </section>
    <br>
    <section id="messages">
        <h1>Sorry but the 'reply' function has been temporarily disabled due to various issues</h1>
        <form id="messageForm">
            <textarea id="message" name="message" rows="4" cols="50" required placeholder="let the world know what you're thinking"></textarea><p></p><button id="bootun" type="submit">Post</button>
            <hr>
            <button type="button" onclick="updateMessages()">Refresh messages!</button>
        </form>
        <br>
    </section>
    <section id="messages">
        <div id="success"></div>
        <div id="error"></div>
        <div id="messageList"></div>
    </section>
    <br></br>
    </section>
    <script>
        var userID = <?php echo json_encode($username); ?>;

        function updateMessages() {
            $.ajax({
                type: "GET",
                url: "../messages/messages.php",
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

        $(document).ready(function () {

            $("#messageForm").submit(function (event) {
                event.preventDefault();
                var name = userID;
                var message_text = $("#message").val();
                $.ajax({
                    type: "POST",
                    url: "../messages/submit.php",
                    data: { name: name, message_text: message_text },
                    success: function (response) {
                        $("#message").val("");
                        $("#success").text(response).fadeIn().delay(3000).fadeOut();
                        updateMessages(); // Manually update messages after posting
                    },
                    error: function (xhr, status, error) {
                        $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
                    }
                });
            });

            $(document).on('submit', '.replyForm', function (event) {
                event.preventDefault();
                var parentMessageId = $(this).data('parent-message-id');
                var name = userID;
                var message_text = $(this).find('input[name="reply"]').val();

                $.ajax({
                    type: "POST",
                    url: "../messages/submit.php",
                    data: { name: name, message_text: message_text, parent_id: parentMessageId },
                    success: function (response) {
                        $("#success").text(response).fadeIn().delay(3000).fadeOut();
                        updateMessages(); // Manually update messages after posting a reply
                    },
                    error: function (xhr, status, error) {
                        $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
                    }
                });
            });

            // Initial load of messages
            updateMessages();
        });
    </script>

    <script src="script.js"></script>
</body>
</html>
<?php
}
?>
<?php
include 'cmode.php';
?>
