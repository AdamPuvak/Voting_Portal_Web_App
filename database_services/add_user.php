<?php
session_start();

require_once '../config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk'; // Predvolený jazyk
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require '../languages/' . $_SESSION['lang'] . '.php';


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['managing_users']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/change_password.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/main_page.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <a href="?lang=sk"><img src="../images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 30px"></a>
            ./.
            <a href="?lang=en"><img src="../images/Flag_of_the_United_Kingdom.png" alt="EN"
                                    style="height: 20px; width: 30px"></a>
            <li class="nav-item">
                <a class="nav-link" href="../main_page.php"><?php echo $lang['questions']; ?><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../create_question.php"><?php echo $lang['create_question']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><?php echo $lang['pricing']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#"><?php echo $lang['disabled']; ?></a>
            </li>
        </ul>

        <ul class="navbar-nav">

            <?php
            if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
                echo '<li class="nav-item">
            <a class="nav-link" href="../manage_users.php">' . $lang['manage_users'] . '</a>
          </li>';
            }
            ?>
            <li class="nav-item">
                <a class="nav-link" href="../change_password.php"><?php echo $lang['change_password']; ?></a>
            </li>
            <li class="nav-item">
                <a class="btn btn-danger" href="../logout.php"><?php echo $lang['logout']; ?></a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">

    <h2><?php echo $lang['add_user']; ?></h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="form-group">
            <label for="email"><?php echo $lang['email']; ?></label>
            <input type="email" name="email" id="email" required class="form-control">
        </div>
        <div class="form-group">
            <label for="password"><?php echo $lang['password']; ?></label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <label for="confirm_password"><?php echo $lang['confirm_password']; ?></label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <div class="form-group">
            <label for="is_admin"><?php echo $lang['admin']; ?></label>
            <input type="checkbox" name="is_admin" id="is_admin" value="1">
        </div>
        <button type="submit" class="btn btn-success"><?php echo $lang['add_user']; ?></button>
    </form>
</div>


<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Získanie hodnôt z formulára
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmed_password = $_POST["confirm_password"];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($password == $confirmed_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check_email_query = "SELECT * FROM users WHERE email=:email";

        $stmt = $conn->prepare($check_email_query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $num_rows = $stmt->rowCount();

        // Ak bol nájdený používateľ s daným emailom
        if ($num_rows > 0) {
            header("location: add_user.php?status=user_exists");
        } else {
            $insert_user_query = "INSERT INTO users (email, password, is_admin) VALUES (:email, :password,:is_admin)";

            $stmt = $conn->prepare($insert_user_query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':is_admin', $is_admin);

            if ($stmt->execute()) {
                header("location: add_user.php?status=success");
            } else {
                header("location: add_user.php?status=error");
            }
        }
    } else {
        header("location: add_user.php?status=password_error");
    }

}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var lang = {
        error: '<?php echo addslashes($lang['error']); ?>',
        success: '<?php echo addslashes($lang['success']); ?>',
        successfullyCreated: '<?php echo addslashes($lang['user_successfully_created']); ?>',
        passwordMismatch: '<?php echo addslashes($lang['password_mismatch']); ?>',
        userAlreadyExists: '<?php echo addslashes($lang['user_already_exists']); ?>',
        errorCreatingUser: '<?php echo addslashes($lang['error_creating_user']); ?>'
    };

    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: lang.success,
                text: lang.successfullyCreated
            });
        } else if( status === 'password_error'){
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.passwordMismatch
            });
        } else if( status === 'user_exists') {
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.userAlreadyExists
            });
        } else if( status === 'error'){
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.errorCreatingUser
            });
        }
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