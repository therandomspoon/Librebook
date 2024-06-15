<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include '../config.php';

session_start();
$userId = $_SESSION['user_id'];
$loginuser = $_SESSION['username'];
$searchusern = $_SESSION['searchTerm'];
echo $searchusern;
echo '  -  ';
echo $loginuser;
echo '  -  ';
echo $userId;

if (empty($loginuser)) {
    header("Location: notlogin.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Assuming the column representing the following is named 'following'
    $stmt = $pdo->prepare("UPDATE users SET following = 
                            CASE 
                                WHEN following LIKE CONCAT('%', ?, '%') THEN 
                                    TRIM(BOTH ', ' FROM REPLACE(CONCAT(', ', following, ', '), CONCAT(', ', ?, ', '), ', '))
                                ELSE 
                                    CONCAT(following, ', ', ?)
                            END 
                            WHERE username = ?");
    $stmt->execute([$loginuser, $loginuser, $loginuser, $searchusern]);
}

echo $searchTerm;
// Assign the value of $searchTerm to the 'sterm' session variable

header("Location: ../profiles/profiles.php?search=" . $searchusern);
exit();
?>
