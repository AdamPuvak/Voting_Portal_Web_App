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

if (isset($_GET['id'])) {
    $questionId = $_GET['id'];
} else {
    header("location: index.php");
    exit;
}

require_once 'config.php';
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$questionId]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $question = $_POST['question'];
    $subject = $_POST['subject'];
    $userEmail = $_POST['userEmailValue'];

    echo $question;
    echo $subject;
    echo $userEmail;

    $stmt = $conn->prepare("UPDATE questions SET user_email = ?, question = ?, subject = ? WHERE id = ?");
    if (!$stmt->execute([$userEmail, $question, $subject, $questionId])) {
        echo $lang['error_executing_sql_statement'] . $stmt->errorInfo();
    }

    if ($stmt->rowCount() > 0) {
        header("location: index.php");
        exit;
    } else {
        header("location: index.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['edit_question']; ?></title>
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

            <li class="nav-item">
                <a class="nav-link" href="main_page.php"><?php echo $lang['questions']; ?><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="create_question.php"><?php echo $lang['create_question']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><?php echo $lang['pricing']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#"><?php echo $lang['disabled']; ?></a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <div class="nav-link"><?php echo $email; ?></div> <!-- Zobrazenie emailu -->
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
                <a class="btn btn-danger" href="logout.php"><?php echo $lang['logout']; ?></a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">

    <label for="userEmail"><?php echo $lang['edit_in_name']; ?></label>

    <select id="userEmail" name="userEmail" required>
        <?php

        require_once 'config.php';

        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $users = $conn->query("SELECT email FROM users")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $user) {
                echo "<option value='{$user['email']}'>{$user['email']}</option>";
            }
        } else {
            echo "<option value='{$email}'>{$email}</option>";
        }

        ?>
    </select> <br>


    <div id="editQuestionContainer" class="open-question-container add-question-container">
        <form id="editQuestionForm" method="post">
            <input type="hidden" id="userEmailValue" name="userEmailValue" value="<?php echo $question['user_email']; ?>" >
            <label for="question" class='bold'><?php echo $lang['question']; ?></label>
            <input type="text" id="question" name="question" value="<?php echo $question['question']; ?>" required>

            <label for="subject" class='bold'><?php echo $lang['subject_abbreviation']; ?></label>
            <input type="text" id="subject" name="subject" value="<?php echo $question['subject']; ?>" required>

            <input type="submit" value="<?php echo $lang['save_changes']; ?>">
        </form>
    </div>

    <div class='edit-question-container' id='message-div'>
        <h6 id='message'></h6>
    </div>

</div>

<script>
    document.getElementById("userEmail").addEventListener("change", function() {
        var userEmail = this.value;
        document.getElementById("userEmailValue").value = userEmail;
        console.log(document.getElementById("userEmailValue").value);
    });
</script>

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
