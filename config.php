<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = 'localhost';
$db_name = 'messages';
$db_user = 'adamg';
$db_password = 'pirules';
$sacredwork = ''; // For a future unreleased update regarding verifying accounts 

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
