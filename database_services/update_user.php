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

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"]) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['original_email'];
    $is_admin = isset($_POST['set_admin']) ? 1 : 0;
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET is_admin = :is_admin, password = :password WHERE email = :email");
            $stmt->bindParam(':password', $password_hash);
        } else {
            $stmt = $conn->prepare("UPDATE users SET is_admin = :is_admin WHERE email = :email");
        }

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':is_admin', $is_admin);

        $stmt->execute();

        header("location: ../manage_users.php?status=success_edited");
    } catch (PDOException $e) {
        header("location: edit_user.php?email=" . $email . "&status=error&message=" . urlencode($e->getMessage()));
    }
} else {
    header("location: ../manage_users.php?status=error&message=InvalidAccess");
}
?>