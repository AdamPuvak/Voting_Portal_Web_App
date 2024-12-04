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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['registration']; ?></title>
    <link rel="stylesheet" href="css/registration.css">
</head>

<body>
<header>
    <a href="?lang=sk"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
    <a href="?lang=en"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
</header>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id='registration_form'>
        <h2><?php echo $lang['registration']; ?></h2>
        <label for="email"><?php echo $lang['email']; ?></label>
        <input type="text" id="email" name="email" required>
        <label for="password"><?php echo $lang['password']; ?></label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password"><?php echo $lang['confirm_password']; ?></label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <input type="submit" value="<?php echo $lang['register']; ?>">
        <a href="index.php" class="login-link"><?php echo $lang['back_to_login']; ?></a>
    </form>

    <?php
    
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: main_page.php");
        exit;
    }

    require_once 'config.php';

    // Kontrola, či bola odoslaná POST požiadavka
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $is_post_request = true;
    } else {
        $is_post_request = false;
    }

    if ($is_post_request) {
        // Získanie hodnôt z formulára
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmed_password = $_POST["confirm_password"];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if($password == $confirmed_password){
            $check_email_query = "SELECT * FROM users WHERE email=:email";

            $stmt = $conn->prepare($check_email_query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $num_rows = $stmt->rowCount();

            if ($num_rows > 0) {
                header("location: registration.php?status=user_exists");
            } else {
                $insert_user_query = "INSERT INTO users (email, password) VALUES (:email, :password)";

                $stmt = $conn->prepare($insert_user_query);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);

                if ($stmt->execute()) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["email"] = $email;
                    header("location: main_page.php");
                } else {
                    header("location: registration.php?status=error");
                }
            }
        }
        else{
            header("location: registration.php?status=password_error");
        }
    }
    ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    var lang = {
        error: '<?php echo addslashes($lang['error']); ?>',
        success: '<?php echo addslashes($lang['success']); ?>',
        successfullyDeleted: '<?php echo addslashes($lang['user_successfully_deleted']); ?>',
        successfullyCreated: '<?php echo addslashes($lang['user_successfully_created']); ?>',
        successfullyEdited: '<?php echo addslashes($lang['user_successfully_edited']); ?>',
        passwordMismatch: '<?php echo addslashes($lang['password_mismatch']); ?>',
        userNotFound: '<?php echo addslashes($lang['user_not_found']); ?>',
        notAdmin: '<?php echo addslashes($lang['user_not_admin']); ?>',
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

    <script src='scripts/registration.js'></script>
</body>

</html>