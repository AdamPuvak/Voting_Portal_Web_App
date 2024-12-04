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

$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : (isset($_GET['question_id']) ? $_GET['question_id'] : '');
$question_text = '';
$question_type = '';
$answers_display = '';

if (!empty($question_id)) {
    try {
        $stmt = $conn->prepare("SELECT question, question_type, answers_display FROM questions WHERE id = :id");
        $stmt->bindParam(':id', $question_id, PDO::PARAM_STR);
        $stmt->execute();
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($question) {
            $question_text = $question['question'];
            $question_type = $question['question_type'];
            $answers_display = $question['answers_display'];
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($question_id)) {
    $answer = $_POST['answer'];
    $is_right = 'false';

    if ($question_type == 'defined-answers') {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM defined_answers WHERE answer_to_question = :question_id AND answer = :answer AND is_right = 'true'");
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
            $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
            $stmt->execute();
            $is_right = $stmt->fetchColumn() > 0 ? 'true' : 'false';
        } catch (PDOException $e) {
            http_response_code(500);
            echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
        }
    }

    try {
        if ($question_type == 'open') {
            $stmt = $conn->prepare("SELECT count FROM open_answers WHERE answer_to_question = :question_id AND answer_text = :answer");
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
            $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $stmt = $conn->prepare("UPDATE open_answers SET count = count + 1 WHERE answer_to_question = :question_id AND answer_text = :answer");
            } else {
                $stmt = $conn->prepare("INSERT INTO open_answers (answer_to_question, answer_text, count) VALUES (:question_id, :answer, 1)");
            }
        } elseif ($question_type == 'defined-answers') {
            $stmt = $conn->prepare("SELECT count FROM defined_answers WHERE answer_to_question = :question_id AND answer = :answer");
            $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
            $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $stmt = $conn->prepare("UPDATE defined_answers SET count = count + 1 WHERE answer_to_question = :question_id AND answer = :answer");
            } else {
                $stmt = $conn->prepare("INSERT INTO defined_answers (answer_to_question, answer, is_right, count) VALUES (:question_id, :answer, :is_right, 1)");
                $stmt->bindParam(':is_right', $is_right, PDO::PARAM_STR);
            }
        }
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
        $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        http_response_code(500);
        echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
    }
}

function getFontSize($count, $maxCount) {
    $minFontSize = 12;
    $maxFontSize = 48;
    if ($maxCount == 0) {
        return $minFontSize;
    }
    $size = ($count / $maxCount) * ($maxFontSize - $minFontSize) + $minFontSize;
    return round($size);
}
?>

    <!DOCTYPE html>
    <html lang="sk">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $lang['results']; ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
              integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="css/result_page.css">
    </head>
<body>
    <header>
        <a href="?lang=sk&question_id=<?php echo $question_id; ?>"><img src="../WEBTE2_Zaverecne_zadanie/images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
        <a href="?lang=en&question_id=<?php echo $question_id; ?>"><img src="../WEBTE2_Zaverecne_zadanie/images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
    </header>
<div class="container">
    <h1><?php echo $lang['results']; ?></h1>

<?php if (!empty($question_text)): ?>
    <h2><?php echo $lang['question']; ?>: <?php echo htmlspecialchars($question_text); ?></h2>
<?php endif; ?>
<?php
if (!empty($question_id)) {
    try {
        if ($question_type == 'open') {
            $stmt = $conn->prepare("SELECT answer_text as answer, count FROM open_answers WHERE answer_to_question = :question_id");

        } elseif ($question_type == 'defined-answers') {
            $stmt = $conn->prepare("SELECT answer, is_right, count FROM defined_answers WHERE answer_to_question = :question_id");
        }
        $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            $maxCount = max(array_column($results,'count'));

            if ($answers_display == 'wordCloud') {
                echo "<div id='wordCloudView' class='word-cloud'>";
                foreach ($results as $result) {
                    $fontSize = getFontSize($result['count'], $maxCount);
                    $correctStyle = isset($result['is_right']) && $result['is_right'] === 'true' ? 'color: green;' : '';
                    echo "<span style='font-size: {$fontSize}px; {$correctStyle}'>" . htmlspecialchars($result['answer']) . "</span> ";
                    }
                    echo "</div>";
                } else {
                    echo "<div id='listView' class='results'>";
                    echo "<h2>" . $lang['results_for'] . ": $question_id</h2>";
                    echo "<ul class='results-list'>";
                    foreach ($results as $result) {
                        $correctClass = isset($result['is_right']) && $result['is_right'] === 'true' ? 'correct' : '';
                        echo "<li class='{$correctClass}'><span>" . htmlspecialchars($result['answer']) . "</span> " . htmlspecialchars($result['count']) . "</li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                }
            } else {
                echo "<p>" . $lang['no_answers'] . ": $question_id</p>";
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>" . $lang['no_answers'] . "</p>";
    }
    ?>
    <a href="index.php"><?php echo $lang['main_page']; ?></a>
</div>
</body>
</html>