<?php
session_start();

require_once 'config.php';

function generateUniqueCode($length = 5)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCharacter = $characters[rand(0, $charactersLength - 1)];
        $code .= $randomCharacter;
    }
    return $code;
}

function isUniqueCodeInDatabase($uniqueCode, $connection)
{
    try {
        $stmt = $connection->prepare("SELECT * FROM questions WHERE id=:id");

        $stmt->bindParam(':id', $uniqueCode);

        $stmt->execute();

        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Chyba pri vyhľadávaní záznamu: " . $e->getMessage();
        return false;
    }
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Získanie údajov z formulára
    $question = $_POST["question1"];
    $subject = $_POST["subject1"];
    $creationDate = $_POST["creationDate1"];
    $questionType = $_POST["questionType1"];
    $isOpen = $_POST["isOpen1"];
    $numOfAnswers = $_POST["numOfAnswers1"];

    $email = $_POST["userEmail"];

    // Získanie počtu správnych odpovedí
    $numOfCorrectAnswers = 0;
    for ($i = 1; $i <= $numOfAnswers; $i++) {
        if (isset($_POST["correctAnswer" . $i])) {
            $numOfCorrectAnswers++;
        }
    }

    try {
        do {
            // Generovanie unikátneho kódu
            $uniqueID = generateUniqueCode();

            // Kontrola, či taký kód už existuje v databáze
            $codeExist = isUniqueCodeInDatabase($uniqueID, $conn);
        } while ($codeExist);

        $stmt = $conn->prepare("INSERT INTO questions (id, question, subject, creation_date, question_type, is_active, user_email, right_answers)
                               VALUES (:uniqueID, :question, :subject, :creationDate, :questionType, :isOpen, :email, :numOfCorrectAnswers)");

        $stmt->bindParam(':uniqueID', $uniqueID);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':questionType', $questionType);
        $stmt->bindParam(':isOpen', $isOpen);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':numOfCorrectAnswers', $numOfCorrectAnswers);

        $stmt->execute();

        echo "Záznam bol úspešne vložený do databázy.";

        $stmt = $conn->prepare("INSERT INTO defined_answers (answer, answer_to_question, is_right, count) VALUES (:answer, :questionID, :isRight, 0)");

        for ($i = 1; $i <= $numOfAnswers; $i++) {
            $answer = $_POST["answer" . $i];
            $isRight = isset($_POST["correctAnswer" . $i]) ? 'true' : 'false';

            $stmt->bindParam(':answer', $answer);
            $stmt->bindParam(':questionID', $uniqueID);
            $stmt->bindParam(':isRight', $isRight);

            $stmt->execute();
        }


        echo "Záznamy odpovedí boli úspešne vložené do databázy.";

    } catch (PDOException $e) {
        echo "Chyba pri vkladaní záznamu: " . $e->getMessage();
    }

} else {
    echo "Chyba: formulár nebol správne odoslaný.";
}
