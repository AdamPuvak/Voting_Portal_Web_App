<?php
session_start();

require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $email = $_SESSION["email"];
    $data = json_decode(file_get_contents("php://input"), true);

    if($data === null) {
        echo "Chyba: Neplatné dáta";
        http_response_code(400);
        exit;
    }

    $questionId = $data['question_id'];
    $questionState = $data['question_state'];
    $questionStateByWord = '';
    if ($questionState === true || $questionState === "true") {
        $questionStateByWord = 'true';
    } else {
        $questionStateByWord = 'false';
    }

    try {
        $stmt = $conn->prepare("UPDATE questions SET is_active = :is_active WHERE id=:id AND user_email=:email");
        $stmt->bindParam(':id', $questionId);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':is_active', $questionStateByWord);
        $stmt->execute();

        echo json_encode(array("message" => "Stĺpec is_active bol úspešne aktualizovaný"));
        $conn->commit();

    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Chyba pri prístupe k databáze: " . $e->getMessage()));
    }
} else {
    echo "Chyba: Neplatná metóda";
    http_response_code(405);
}
?>
