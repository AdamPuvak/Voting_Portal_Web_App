<?php
session_start();

require_once 'config.php';

function generateUniqueCode($length = 5) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCharacter = $characters[rand(0, $charactersLength - 1)];
        $code .= $randomCharacter;
    }
    return $code;
}

function isUniqueCodeInDatabase($uniqueCode, $connection) {
    try {
        $stmt = $connection->prepare("SELECT * FROM questions WHERE id=:id");
        
        $stmt->bindParam(':id', $uniqueCode);

        $stmt->execute();

        $rowCount = $stmt->rowCount();

        // Ak je počet riadkov viac ako 0, záznam existuje
        if ($rowCount > 0) {
            return true;
        } else {
            return false;
        }
    } catch(PDOException $e) {
        echo "Chyba pri vyhľadávaní záznamu: " . $e->getMessage();
        return false;
    }
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST["question"];
    $subject = $_POST["subject"];
    $creationDate = $_POST["creationDate"];
    $questionType = $_POST["questionType"];
    $isOpen = $_POST["isOpen"];
    $answerDisplay = $_POST["answerDisplay"];

    $email = $_POST["userEmail"];

    try {
        do {
            $uniqueID = generateUniqueCode();
    
            $codeExist = isUniqueCodeInDatabase($uniqueID, $conn);
        } while ($codeExist);
    
        $stmt = $conn->prepare("INSERT INTO questions (id, question, subject, creation_date, question_type, is_active, answers_display, user_email)
                               VALUES (:uniqueID, :question, :subject, :creationDate, :questionType, :isOpen, :answerDisplay, :email)");
        
        $stmt->bindParam(':uniqueID', $uniqueID);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':creationDate', $creationDate);
        $stmt->bindParam(':questionType', $questionType);
        $stmt->bindParam(':isOpen', $isOpen);
        $stmt->bindParam(':answerDisplay', $answerDisplay);
        $stmt->bindParam(':email', $email);
    
        $stmt->execute();
    
        echo "Záznam bol úspešne vložený do databázy.";
    } catch(PDOException $e) {
        echo "Chyba pri vkladaní záznamu: " . $e->getMessage();
    }
}
?>
