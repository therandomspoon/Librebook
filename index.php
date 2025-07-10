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
    <title>Librebook</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/blue.css">
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
        <img src="../images/librebook1.png" style="max-width: 100%; height: auto; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <section id="sendamess">
        <section id="messages">
            <h1>Welcome to Librebook !</h1>
            <hr>
            <p>For security and functionality reasons you must login or register to use our site (sorry) but do not fret we dont collect data apart from your username and password so you can login and message!</p>
            <a href="../registration/register.html">register</a>
            <p></p>
            <a href="../login/login.html">login</a>
        </section>
    </section>
    <section id="sendamess">
        <section id="messages">
            <h1>What is Librebook ?</h1>
            <hr>
            <video controls>
                <source src="https://lt.epicsite.xyz/videodata/non-hls.php?id=L0E6S--2Zt4&dl=dl&itag=18" type='video/mp4'>
            </video>
        </section>
        <br></br> 
    </section>
    <br><br>
    <div class="creditbar">
        <p>Librebook was created by therandomspoon</p>
    </div>
</body>
