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

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$email = $_SESSION["email"];

function getFontSize($count, $maxCount) {
    $minFontSize = 12;
    $maxFontSize = 48;
    if ($maxCount == 0) {
        return $minFontSize;
    }
    $size = ($count / $maxCount) * ($maxFontSize - $minFontSize) + $minFontSize;
    return round($size);
}

function getArchivedResults($conn, $question_id) {
    $stmt = $conn->prepare("
        SELECT question_id, answer, count, note, created_at 
        FROM answers_archive 
        WHERE question_id = :question_id
        ORDER BY created_at DESC
    ");
    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function displayResults($conn, $question) {
    global $lang;

    $question_id = $question['id'];
    $question_text = htmlspecialchars($question['question']);
    $question_type = $question['question_type'];
    $answers_display = $question['answers_display'];

    echo "<h2>{$question_text}</h2>";

    if ($question_type == 'open') {
        $stmt = $conn->prepare("SELECT answer_text as answer, count FROM open_answers WHERE answer_to_question = :question_id");
    } elseif ($question_type == 'defined-answers') {
        $stmt = $conn->prepare("SELECT answer, is_right, count FROM defined_answers WHERE answer_to_question = :question_id");
    }
    $stmt->bindParam(':question_id', $question_id, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        $maxCount = max(array_column($results, 'count'));

        if ($answers_display == 'wordCloud') {
            echo "<br><h3>" . $lang['current_results'] . "</h3>";
            echo "<div id='wordCloudView' class='word-cloud'>";
            foreach ($results as $result) {
                $fontSize = getFontSize($result['count'], $maxCount);
                $correctStyle = isset($result['is_right']) && $result['is_right'] === 'true' ? 'color: green;' : '';
                echo "<span style='font-size: {$fontSize}px; {$correctStyle}'>" . htmlspecialchars($result['answer']) . "</span> ";
            }
            echo "</div><br><br>";
        } else {
            echo "<div id='listView' class='results'>";
            echo "<h3>" . $lang['current_results'] . "</h3>";
            echo "<ul class='results-list'>";
            foreach ($results as $result) {
                $correctClass = isset($result['is_right']) && $result['is_right'] === 'true' ? 'correct' : '';
                echo "<li class='{$correctClass}'><span>" . htmlspecialchars($result['answer']) . "</span> " . htmlspecialchars($result['count']) . "</li>";
            }
            echo "</ul>";
            echo "</div><br><br>";
        }
    } else {
        echo "<p>" . $lang['no_answers'] . ": $question_id</p>";
    }

    $archivedResults = getArchivedResults($conn, $question_id);
    if ($archivedResults) {
        echo "<h3>" . $lang['historical_results'] . "</h3>";

        $groupedResults = [];
        foreach ($archivedResults as $archived) {
            $dateKey = date('Y-m-d H:i:s', strtotime($archived['created_at']));
            if (!isset($groupedResults[$dateKey])) {
                $groupedResults[$dateKey] = [];
            }
            $groupedResults[$dateKey][] = $archived;
        }

        foreach ($groupedResults as $date => $results) {
            $totalCount = array_sum(array_column($results, 'count'));
            echo "<div class='historical-record'>";
            echo "<h4>{$date}</h4>";
            echo "<ul class='historical-list'>";
            foreach ($results as $result) {
                $percentage = ($totalCount > 0) ? round(($result['count'] / $totalCount) * 100, 2) : 0;
                $note = !empty($result['note']) ? htmlspecialchars($result['note']) : $lang['no_note'];
                echo "<li>" . htmlspecialchars($result['answer']) . " - {$percentage}% ({$result['count']})<br><small> {$note}</small></li>";
            }
            echo "</ul>";
            echo "</div><br><br><br>";
        }
    } else {
        echo "<p>" . $lang['no_historical_data'] . "</p>";
    }
}

try {
    $stmt = $conn->prepare("SELECT id, question, question_type, answers_display FROM questions");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>" . $lang['database_error'] . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang['results']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/show_results.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <a href="?lang=sk"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 30px"></a> ./.
            <a href="?lang=en"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 30px"></a>
            <li class="nav-item active">
                <a class="nav-link" href="main_page.php"><?php echo $lang['questions']; ?><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_question.php"><?php echo $lang['create_question']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="show_results.php"><?php echo $lang['show_results']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manual.php"><?php echo $lang['manual']; ?></a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <div class="nav-link"><?php echo $email; ?></div>
            </li>
            <li class="nav-item">
                <div class="nav-link">
                    <?php
                    if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
                        echo "(Admin)";
                    }
                    ?>
                </div>
            </li>


    </div>
</
<?php
if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
    echo '<li class="nav-item">
            <a class="nav-link" href="manage_users.php">' . $lang['manage_users'] . '</a>
          </li>';
}
?>

<li class="nav-item">
    <a class="nav-link" href="change_password.php"><?php echo $lang['change_password']; ?></a>
</li>
<li class="nav-item">
    <a class="btn btn-danger" href="logout.php"><?php echo $lang['logout']; ?></a>
</li>
</ul>
</div>
</nav>

<div class="container">
    <h1><?php echo $lang['results']; ?></h1>

    <?php
    if (!empty($questions)) {
        foreach ($questions as $question) {
            displayResults($conn, $question);
        }
    } else {
        echo "<p>" . $lang['no_questions'] . "</p>";
    }
    ?>
</div>
</body>
</html>