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
        <img src="/images/librebook1.png" style="height: 125px; width: 125px; float: right;">
        <h1 id="headl">Librebook</h1>
    </section>
    <section id="messages">
        <h1>Librebook Data-center.</h1>
        <h1>Control your data on Librebook.</h1>
        <hr>
        <button id="openPopup">Librebook's privacy policy</button>
        <hr>
        <form method="post" onsubmit="return confirmDelete()">
            <input id="bu1" type="submit" name="delete_messages" value="Delete All Messages">
        </form>
        <br>
        <div id="myPopup" class="popup">
            <div class="popup-content">
                <span class="close" id="closePopup">&times;</span>
                <h1 style="color: #1877f2;">Librebook's Privacy Policy</h1>
                <h1>Effective Date: 21/09/2024</h1>
                <h1>Version - 1</h1>
                <h1>1. Introduction</h1>
                <p>Welcome to Librebook, a social media platform with a core commitment to transparency and safeguarding your privacy. This Privacy Policy explains how we collect, use, and disclose your personal information, as well as your rights in relation to that information. By using Librebook, you consent to the terms outlined herein. This policy applies to all users, irrespective of geographic location.</p>
                <h1>2. Information We Collect</h1>
                <p>At Librebook, we are devoted to collecting only the minimal amount of data necessary for the proper functioning of our services. This data is broken into the following categories:</p>
                <h1>2.1. Personal Information</h1>
                <ul>
                    <li><p>Username: Required for account creation and user identification.</p></li>
                    <li><p>Password: Encrypted and stored securely to protect account security.</p></li>
                    <li><p>Email Address: Used for account verification and password recovery.</p></li>
                </ul>
                <h1>2.2. Non-Personal Information</h1>
                <p>Librebook also collects certain non-personally identifiable information that assists in enhancing your experience and maintaining security, including:</p>
                <ul>
                    <li><p>Message History: Stored unless deleted by the user in accordance with privacy laws (e.g., GDPR).</p></li>
                    <li><p>Profile Picture URLs: Retained unless manually removed or replaced.</p></li>
                </ul>
                <h1>2.3. Cookies and Session Data</h1>
                <p>We employ essential cookies to:</p>
                <ul>
                    <li><p>Keep users logged into their accounts.</p></li>
                    <li><p>Save user preferences, such as themes or site display settings.</p></li>
                    <li><p>Ensure the platform functions optimally and securely.</p></li>
                </ul>
                <h1>3. How We Use Your Information</h1>
                <p>Librebook uses collected data to:</p>
                <ul>
                    <li><p>Maintain the functionality of the platform.</p></li>
                    <li><p>Secure accounts and provide necessary user support.</p></li> 
                    <li><p>Analyse usage to enhance features and improve user experience.</p></li> 
                </ul>
                <p>We will never sell or rent your personal information to third parties.</p>
            <h1>4. Legal Compliance and Disclosure</h1>
            <p>While we are committed to protecting your privacy, Librebook may be required by law to disclose personal information in response to legal processes, such as:</p>
            <ul>
                <li><p>Judicial orders issued under the Investigatory Powers Act (IPA) 2016.</p></li>
                <li><p>Requests under the General Data Protection Regulation (GDPR) Article 23.</p></li>
                <li><p>Other applicable legal obligations, including the Stored Communications Act (SCA).</p></li>
            </ul>
            <h1>5. User Rights</h1>
            <p>You, as a Librebook user, have full control over your personal information and account data. Your rights include:</p>
            <ul>
                <li><p>Account Deletion: Permanently delete your account and all associated data.</p></li>
                <li><p>Message Deletion: Remove your entire message history.</p></li>
                <li><p>Data Access & Portability: Request a copy of your data (to be made available in an upcoming update).</p></li>
            </ul>

            <h1>6. Data Security</h1>
            <p>Librebook prioritizes the security of your personal information, implementing a variety of measures to safeguard data, including:</p>
            <ul>
                <li><p>Encryption: All traffic to and from Librebook is encrypted using industry-standard SSL protocols (via Cloudflare).</p></li>
                <li><p>SQL Injection Protection: Our messaging and registration systems employ advanced measures to prevent database attacks.</p></li>
            </ul>

            <h1>7. Data Retention</h1>
            <p>Librebook retains user data for as long as necessary to provide services or until the user opts to delete their account or message history. Users may request deletion at any time.</p>

            <h1>8. Children's Privacy</h1>
            <p>Librebook takes the privacy and protection of minors very seriously. We offer a dedicated Kids Mode, introduced in Librebook Update 4.5, which provides additional privacy and safety features for younger users. Librebook is committed to not sharing any personal information of minors without express parental consent.</p>

            <h1>9. Policy Updates</h1>
            <p>This Privacy Policy will evolve alongside Librebook’s features. Each significant update to the platform will likely result in an amendment to the policy. The most recent revision date will always be visible at the top of this document.</p>

            <h1>10. Contact Information</h1>
            <p>If you have any questions or concerns regarding this Privacy Policy or your data on Librebook, please contact us at:</p>
            <p><strong>Email:</strong> support@librebook.co.uk</p>
            <p>Please note that while we aim to respond to inquiries, replies are not guaranteed.</p>
            </div>
        </div>
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
