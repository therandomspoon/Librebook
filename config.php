<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = 'sql207.infinityfree.com';
$db_name = 'if0_36414508_librebooktecopa';
$db_user = 'if0_36414508';
$db_password = 'ZaWPEfgCWEF';
$sacredwork = ''; // For a future unreleased update regarding verifying accounts 

$offsetnum = 10; // the number of messages loaded per page

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
