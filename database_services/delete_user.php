<?php
session_start();

require_once '../config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require '../languages/' . $_SESSION['lang'] . '.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Získanie ID používateľa z URL
$userEmail = isset($_GET['email']) ? $_GET['email'] : null;

if ($userEmail) {
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE email = :email");
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();

        header("location: ../manage_users.php?status=success_deleted");
    } catch (PDOException $e) {
        header("location: ../manage_users.php?status=error&message=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../manage_users.php?status=error&message=InvalidEmail");
}

?>