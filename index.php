<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: main.php');
    exit();
} else {
}
?>

<!DOCTYPE html>
<html lang="en">
<style>
    #sendamess, #messages {
        font-size: 20px;
    }
    img {
        width: 600px;
        height: 500px;
    }
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/mainsite.css">
    <meta property="og:title" content="Librebook">
    <meta property="og:description" content="Librebook is the free, secure, not selling your data social media solution made by the therandomspoon.">
    <meta property="og:image" content="http://librebook.rf.gd/images/librebookb.png">
    <meta property="og:url" content="http://librebook.rf.gd/">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@thatrandomspoon">
</head>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="sendamess">
        <section id="messages">
            <h1>Welcome to Librebook !</h1>
            <hr>
            <p>For security and functionality reasons you must login or register to use our site (sorry) but do not fret we dont collect data apart from your username and password and email so you can login and message!</p>
            <p>We had to recently add emails recently due to the problem of easy to make alt accounts (really sorry). Your emails are not and will not be visible to the public or sold to any third parties.</p>
            <a href="../registration/register.html">register</a>
            <p></p>
            <a href="../login/login.html">login</a>
        </section>
        <br></br>
    </section>
    <br><br>
    <section id="sendamess">
        <section id="messages">
            <h1>What is Librebook ?</h1>
            <hr>
            <video controls>
                <source src="https://lt.epicsite.xyz/watch/?v=L0E6S--2Zt4" type='video/mp4'>
            </video>
        </section>
        <br></br> 
    </section>
    <br><br>
    <section id="sendamess">
        <section id="messages">
            <h1>Librebook news !</h1>
            <hr>
            <h1>Post 1: Librebook X Liberatube collab!</h1>
            <p>On the 15th of January 2024 Librebook offered to collaborate with Liberatube by allowing Liberatube videos to be sent on Librebook and the video would load. This update was deployed on the pre-alpha release of Librebook and is active to this date. It will soon be published to the github for everyone to incorporate in their versions! This is the first update to Librebook allowing for videos to be sent via Librebook and is a major milestone on the Librebook roadmap. Happy messaging!</p>
        </section>
    </section>
    <div class="creditbar">
        <a href="../masthead.html" id="excempta">Masthead</a>
    </div>
</body>