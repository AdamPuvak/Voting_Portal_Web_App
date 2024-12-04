<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require 'languages/' . $_SESSION['lang'] . '.php';

$question_id = isset($_GET['parameter']) ? trim($_GET['parameter'], '/') : '';
$question_text = '';
$question_type = '';

if (!empty($question_id)) {
    try {
        // Ziskanie textu otázky a typ otázky
        $stmt = $conn->prepare("SELECT question, question_type FROM questions WHERE id = :id");
        $stmt->bindParam(':id', $question_id, PDO::PARAM_STR);
        $stmt->execute();
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($question) {
            $question_text = $question['question'];
            $question_type = $question['question_type'];
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['answer']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/answer_page.css">
</head>
<body>
<header>
    <a href="?lang=sk&question_id=<?php echo $question_id; ?>"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
    <a href="?lang=en&question_id=<?php echo $question_id; ?>"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
</header>
<div class="container">
    <h1><?php echo $lang['answer']; ?></h1>
    <?php if (!empty($question_text)): ?>
        <h2><?php echo $lang['question']; ?>: <?php echo htmlspecialchars($question_text); ?></h2>
        <?php if ($question_type == 'open'): ?>
            <form action="result_page.php" method="post">
                <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question_id); ?>">
                <label for="answer"><?php echo $lang['answer']; ?>:</label>
                <input type="text" name="answer" id="answer" required>
                <input type="submit" value="<?php echo $lang['submit']; ?>">
            </form>
        <?php elseif ($question_type == 'defined-answers'): ?>
            <form action="result_page.php" method="post">
                <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question_id); ?>">
                <label for="answer"><?php echo $lang['answer']; ?>:</label>
                <select name="answer" id="answer" required>
                    <option value=""><?php echo $lang['select_option']; ?></option>
                    <?php
                    try {
                        $stmt = $conn->prepare("SELECT DISTINCT answer FROM defined_answers WHERE answer_to_question = :question_id");
                        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
                        $stmt->execute();
                        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($answers as $answer) {
                            echo "<option value='" . htmlspecialchars($answer['answer']) . "'>" . htmlspecialchars($answer['answer']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
                    }
                    ?>
                </select>
                <input type="submit" value="<?php echo $lang['submit']; ?>">
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p><?php echo $lang['no_questions']; ?></p>
    <?php endif; ?>
    <a href="index.php"><?php echo $lang['main_page']; ?></a>
</div>
</body>
</html>