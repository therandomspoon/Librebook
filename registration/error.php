<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="../css/mainsite.css">
</head>
<style>
#ourmessage {
    text-align: center;
    background-color: #f44336; 
    color: white;
    padding: 20px;
    border-radius: 8px;
    font-size: 24px;
    margin: 20px auto; 
    max-width: 50%; 
    top:125px;
}
</style>
<body>
    <section id="head">
        <img src="../images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <br>
    <h1 id="ourmessage">Dear users, we are recently experiencing problems regarding our SMTP server where there is a daily limit of outgoing emails. Until we repair this only a certain amount of verification trials can be complete a day so some users may indeed miss out on registration. If you are on this page and your error reads 'Email sending failed. Error: SMTP Error: data not accepted.' then you are indeed one of those few victims. We apologise profusely for any inconveniences caused and you may rest knowing that sorting out this situation is Librebook's current top priority. Thank you.</h1>
    <br>
    <section id="sendamess">
        <section id="messages">
            <p><?php echo isset($_SESSION['message']) ? htmlspecialchars($_SESSION['message']) : 'An unknown error occurred.'; ?></p>
            <a href="../index.php">Go back</a>
        </section>
        <br></br>
    </section>
</body>
</html>
<?php
unset($_SESSION['message']);
?>
