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

$pagenum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$_SESSION['page'] = $pagenum;

include 'config.php';
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['currentpass'] = $user['password'];
if ($_SESSION['sudopassword'] != $_SESSION['currentpass']) {
    session_destroy();
    exit();
}
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

<div style="display: flex; align-items: flex-start; gap: 55px;">
    <div id="helloworld" style="position: sticky; align-self: flex-start;">
        <?php echo 'Welcome back ' . htmlspecialchars($username) . '!'; ?>
        <p></p>
        <a href="deleteyou.php">Delete your account</a>
        <a href="../settings.php" style="float: right;">Go to Settings</a>
        <p></p>
        <a href="../logout.php">Logout</a>
        <a href="../profiles/sprofile.php" style="float: right;">See my profile</a>
        <p></p>
        <a href="/messages/followmes.php">See what the people you follow are saying!</a>
        <p></p>
        <a href="/messages/dm.php">Your Direct Messages</a>
        <p></p>
        <section id="searchbar">
            <form action="../profiles/profiles.php" id="searchform" method="get">
                <input id="searchbut" type="text" placeholder="Search profiles.." name="search" style="max-width: 60%;">
                <button type="submit">Search!<i class="fa fa-search"></i></button>
            </form>
        </section>
        <hr>
        <form action="/messages/datemessages.php" id="searchform" method="get">
            <label for="date">Enter a date to search for:</label>
            <input type="date" id="date" name="date">
            <input type="submit">
        </form>
        <hr>
        <?php echo "Page: $pagenum<br>"; ?> 
        <br>
        <button type="button" onclick="prevPage()">Previous Page</button>
        <button type="button" onclick="nextPage()">Next Page</button><br>
    </div>

    <div style="flex-grow: 1; max-width: 900px;">
        <section id="messages">
            <h1>Let the world know what you're thinking</h1>
            <?php if (isset($_SESSION['replyto'])) { echo $_SESSION['replyto']; } ?>
            <form id="messageForm">
                <textarea id="message" name="message" rows="4" cols="50" required placeholder="let the world know what you're thinking"></textarea>
                <p></p>
                <button id="bootun" type="submit">Post</button>
                <hr>
                <button type="button" onclick="updateMessages()" style="margin-right: 10px;">Refresh messages!</button><button onclick='location="../messages/clearrep.php"'>Clear reply</button>
            </form>
            <br>
        </section>

        <section id="messages">
            <div id="success"></div>
            <div id="error"></div>
            <div id="messageList"></div>
        </section>
    </div>

    <section id="frlist">
        <h1>Your friends</h1>
        <?php

        try {
            $query = "SELECT username
                      FROM users
                      WHERE FIND_IN_SET(:search, REPLACE(following, ' ', '')) > 0";

            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':search', $username, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                foreach ($result as $row) {
                    echo '<a style="font-size: 20px;" href="../profiles/rprofiles.php?search=' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8') . '</a><br>';
                }
            } else {
                echo "No users found.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </section>
</div>

<script>
    var userID = <?php echo json_encode($username); ?>;

    function updateMessages() {
        $.ajax({
            type: "GET",
            url: "../messages/messages.php?page=" + <?php echo $pagenum; ?>,
            success: function (response) {
                $("#messageList").html(response);
                console.log('Messages loaded');
            },
            error: function (xhr, status, error) {
                $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
            }
        });
    }

    function nextPage() {
        const url = new URL(window.location.href);
        let page = parseInt(url.searchParams.get("page")) || 1;
        url.searchParams.set("page", page + 1);
        window.location.href = url.toString();
    }

    function prevPage() {
        const url = new URL(window.location.href);
        let page = parseInt(url.searchParams.get("page")) || 1;
        if (page > 1) {
            url.searchParams.set("page", page - 1);
            window.location.href = url.toString();
        }
    }

    $(document).ready(function () {
        $("#messageForm").submit(function (event) {
            event.preventDefault();
            var name = userID;
            var message_text = $("#message").val();
            $.ajax({
                type: "POST",
                url: "../messages/ksubmit.php",
                data: { name: name, message_text: message_text },
                success: function (response) {
                    $("#message").val("");
                    $("#success").text(response).fadeIn().delay(3000).fadeOut();
                    updateMessages();
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
                url: "../messages/ksubmit.php",
                data: { name: name, message_text: message_text, parent_id: parentMessageId },
                success: function (response) {
                    $("#success").text(response).fadeIn().delay(3000).fadeOut();
                    updateMessages();
                },
                error: function (xhr, status, error) {
                    $("#error").text("An error occurred: " + error).fadeIn().delay(3000).fadeOut();
                }
            });
        });

        updateMessages();
    });
</script>

<script>
function loadVideo(container) {
    var videoUrl = container.getAttribute('data-src');
    var videoElement = document.createElement('video');
    videoElement.setAttribute('controls', 'controls');
    videoElement.setAttribute('autoplay', 'autoplay');

    var sourceElement = document.createElement('source');
    sourceElement.setAttribute('src', videoUrl);
    sourceElement.setAttribute('type', 'video/mp4');

    videoElement.appendChild(sourceElement);
    container.innerHTML = '';
    container.appendChild(videoElement);
}
</script>

<script src="script.js"></script>
</body>
</html>

<?php include 'cmode.php'; ?>
