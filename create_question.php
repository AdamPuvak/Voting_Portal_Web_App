<?php
session_start();

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['create_question']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/create_question.css">
    <link rel="stylesheet" href="css/navbar.css">

</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <a href="?lang=sk"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 30px"></a>
            ./.
            <a href="?lang=en"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN"
                                    style="height: 20px; width: 30px"></a>

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
                <a class="btn btn-danger" href="logout.php"><?php echo $lang["logout"]; ?></a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <!-- Selectbox -->
    <select class="custom-select" id="questionType" name="questionType">
        <option value="1" ><?php echo $lang['question_with_correct_answer']; ?></option>
        <option value="2"><?php echo $lang['open_ended_question']; ?></option>
    </select>

    <label for="userEmail"><?php echo $lang['create_in_name']; ?></label>
    <select id="userEmail" name="userEmail" required>
        <?php


        require_once 'config.php';

        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $users = $conn->query("SELECT email FROM users")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $user) {
                echo "<option value='{$user['email']}'>{$user['email']}</option>";
            }
        } else {
            echo "<option value='{$email}'>{$email}</option>";  // Len sám seba, ak nie je admin
        }

        ?>
    </select> <br>

    <div id="answerQuestionContainer" class="choose-answer-question-container add-question-container">
        <form id="closedQuestionForm">
            <label for="question1" class='bold'><?php echo $lang['question']; ?></label>
            <input type="text" id="question1" name="question1" required>

            <label for="subject1" class='bold'><?php echo $lang['subject_abbreviation']; ?></label>
            <input type="text" id="subject1" name="subject1" required>

            <input type="hidden" id="creationDate1" name="creationDate1" value="">

            <input type="hidden" id="questionType1" name="questionType1" value="defined-answers">

            <input type="hidden" id="isOpen1" name="isOpen1" value="true">

            <input type="hidden" id="numOfAnswers1" name="numOfAnswers1" value="2">

            <div class='bold'><?php echo $lang['answers']; ?></div>
            <div id="answerOptions">
                <div class="answer-option">
                    <label for="answer1"><?php echo $lang['option']; ?> 1</label>
                    <input type="text" id="answer1" name="answer1" required>
                    <input type="checkbox" id="correctAnswer1" name="correctAnswer1">
                    <label for="correctAnswer1"><?php echo $lang['correct_option']; ?></label>
                </div>
                <div class="answer-option">
                    <label for="answer2"><?php echo $lang['option']; ?> 2</label>
                    <input type="text" id="answer2" name="answer2" required>
                    <input type="checkbox" id="correctAnswer2" name="correctAnswer2">
                    <label for="correctAnswer2"><?php echo $lang['correct_option']; ?></label>
                </div>
            </div>

            <button type="button" id="addOptionButton"
                    style="background-color: transparent; border: none; color: gray; cursor: pointer;"><?php echo $lang['add_another_option']; ?>
            </button>

            <input type="submit" value="<?php echo $lang['create_question']; ?>">
        </form>

    </div>


    <div id="openQuestionContainer" class="open-question-container add-question-container">
        <form id="openQuestionForm">

            <label for="question" class='bold'><?php echo $lang['question']; ?></label>
            <input type="text" id="question" name="question" required>

            <label for="subject" class='bold'><?php echo $lang['subject_abbreviation']; ?></label>
            <input type="text" id="subject" name="subject" required>

            <input type="hidden" id="creationDate" name="creationDate" value="">

            <input type="hidden" id="questionType" name="questionType" value="open">

            <input type="hidden" id="isOpen" name="isOpen" value="true">

            <div class="show-type-div">
                <div class='bold'><?php echo $lang['display_answers']; ?></div>

                <div class="item">
                    <input type="radio" id="list" name="answerDisplay" value="list" required checked>
                    <label for="list"><?php echo $lang['list_items']; ?></label>
                </div>

                <div class="item">
                    <input type="radio" id="wordCloud" name="answerDisplay" value="wordCloud">
                    <label for="wordCloud"><?php echo $lang['word_cloud']; ?></label>
                </div>
            </div>

            <input type="submit" value="<?php echo $lang['create_question']; ?>">
        </form>
    </div>

    <div class='add-question-container' id='message-div'>
        <h6 id='message'></h6>
    </div>

</div>

<script src='scripts/create_question.js'></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>

</html>