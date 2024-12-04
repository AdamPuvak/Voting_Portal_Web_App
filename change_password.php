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

$is_post_request = $_SERVER["REQUEST_METHOD"] == "POST";

$email = $_SESSION["email"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['change_password']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/change_password.css">
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

            <?php
            if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
                echo '<li class="nav-item">
            <a class="nav-link" href="manage_users.php">' . $lang['manage_users'] . '</a>
          </li>';
            }
            ?>
            <li class="nav-item active">
                <a class="nav-link" href="#"><?php echo $lang['change_password']; ?></a>
            </li>
            <li class="nav-item">
                <a class="btn btn-danger" href="logout.php"><?php echo $lang['logout']; ?></a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id='change-password-form'>
        <h2><?php echo $lang['change_your_password']; ?></h2>
        <label for="oldPassword"><?php echo $lang['old_password']; ?></label>
        <input type="password" id="oldPassword" name="oldPassword" required>
        <label for="newPassword"><?php echo $lang['new_password']; ?></label>
        <input type="password" id="newPassword" name="newPassword" required>
        <label for="confirmNewPassword"><?php echo $lang['confirm_new_password']; ?></label>
        <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
        <input type="submit" value="<?php echo $lang['change_password']; ?>">
    </form>

    <?php

    if ($is_post_request) {
        $password = $_POST["oldPassword"];
        $newPassword = $_POST["newPassword"];
        $confirmNewPassword = $_POST["confirmNewPassword"];

        $password_query = "SELECT password FROM users WHERE email=:email";
        $stmt = $conn->prepare($password_query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $hashed_password = $result['password'];

            if (password_verify($password, $hashed_password)) {
                if ($password !== $newPassword) {
                    if ($newPassword === $confirmNewPassword) {
                        $hashed_new_password = password_hash($newPassword, PASSWORD_DEFAULT);

                        $update_query = "UPDATE users SET password=:password WHERE email=:email";
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->bindParam(':password', $hashed_new_password);
                        $update_stmt->bindParam(':email', $email);

                        if ($update_stmt->execute()) {
                            echo $lang['password_updated_successfully'];
                        } else {
                            echo $lang['error_updating_password'];
                        }
                    } else {
                        echo $lang['new_password_and_confirm_new_password_do_not_match'];
                    }
                } else {
                    echo $lang['new_password_is_the_same_as_old_password'];
                }
            } else {
                echo $lang['old_password_is_incorrect'];
            }
        } else {
            echo $lang['error_fetching_password_from_database'];
        }
    }
    ?>


</div>

<script src='scripts/change_password.js'></script>
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