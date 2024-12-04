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

try {

    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        $stmt = $conn->prepare("SELECT * FROM questions");
    } else {
        $stmt = $conn->prepare("SELECT subject, creation_date FROM questions WHERE user_email=:email");
        $stmt->bindParam(':email', $email);
    }
    $stmt->execute();

    $subjects = array();
    $creation_dates = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subjects[] = $row['subject'];
        $creation_dates[] = $row['creation_date'];
    }

    $unique_subjects = array_unique($subjects);
    $unique_creation_dates = array_unique($creation_dates);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => $lang["error_database_access"] . $e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['main_page']; ?></title>
    <link href="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main_page.css">
    <link rel="stylesheet" href="css/navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <a class="nav-link" href="#"><?php echo $lang['questions']; ?><span class="sr-only">(current)</span></a>
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
    <h3><?php echo $lang["defined_questions"]; ?></h3>

    <div class="select-boxes-div">
        <select name="select1">
            <option value=""><?php echo $lang['select_subject']; ?></option>
            <?php
            foreach ($unique_subjects as $subject) {
                echo "<option value='$subject'>$subject</option>";
            }
            ?>
        </select>

        <select name="select2">
            <option value=""><?php echo $lang['select_date']; ?></option>
            <?php
            foreach ($unique_creation_dates as $date) {
                echo "<option value='$date'>$date</option>";
            }

            ?>
        </select>

        <form action="export_questions.php" method="post">
            <button type="submit" name="export" id="export_button"><?php echo "Export"; ?></button>
        </form>

    </div>


    <table id='questionsTable'>
        <thead>
        <tr>
            <th><?php echo $lang['question']; ?></th>
            <th><?php echo $lang['question_code']; ?></th>
            <th><?php echo $lang['subject']; ?></th>
            <th><?php echo $lang['active']; ?></th>
            <th><?php echo $lang['question_type']; ?></th>
            <th><?php echo $lang['creation_date']; ?></th>
            <th><?php echo $lang['actions']; ?></th>
            <th><?php echo $lang['user']; ?></th>
            <th><?php echo $lang['close_voting']; ?></th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<div id="qrCodeModal" class="modal">
    <div class="modal-content">
        <p id='questionCode' class='modalText'></p>
        <p id='questionAdress' class='modalText'></p>
        <div id="questionQrCode"></div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</script>
<script src="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/qrcode.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.js'></script>
<script src='scripts/main_page.js'></script>
</body>

</html>