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

// Kontrola, či užívateľ je prihlásený
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    $email = $_GET['email'];

    $questionId = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM questions WHERE id = :questionId AND user_email = :email");

        $stmt->bindParam(':questionId', $questionId);
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        echo $lang['record_deleted_success'];
    } catch(PDOException $e) {
        echo $lang['delete_error'] . $e->getMessage();
    }

}
?>
