<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
unset($_SESSION['replyto']);
header("Location: ../main.php");
?>