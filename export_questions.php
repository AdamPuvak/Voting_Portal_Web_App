<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(401);
    exit;
}

$email = $_SESSION["email"];

try {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        $stmt = $conn->prepare("SELECT * FROM questions");
    } else {
        $stmt = $conn->prepare("SELECT * FROM questions WHERE user_email=:email");
        $stmt->bindParam(':email', $email);
    }
    $stmt->execute();

    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $json_questions = json_encode($questions);

    header('Content-Type: application/json');

    header('Content-Disposition: attachment; filename="questions.json"');

    echo $json_questions;


} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Chyba pri prístupe k databáze: " . $e->getMessage()));
}
?>
