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
    <title><?php echo $lang['main_page']; ?></title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
<header>
    <a href="?lang=sk"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 40px"></a> /
    <a href="?lang=en"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN" style="height: 20px; width: 40px"></a>
</header>


<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <h2><?php echo $lang['login']; ?></h2>
    <label for="email"><?php echo $lang['username']; ?></label>
    <input type="text" id="email" name="email" required>
    <label for="password"><?php echo $lang['password']; ?></label>
    <input type="password" id="password" name="password" required>

    <div class="checkbox">
        <label>
            <input type="checkbox" name="admin_login" id="admin_login" value="1"> <?php echo $lang['login_as_admin']; ?>
        </label>
    </div>

    <input type="submit" value="<?php echo $lang['signIn']; ?>">
    <a href="registration.php" class="registration-link"><?php echo $lang['register']; ?></a>
</form>

<br><br>

<form id="codeForm" style="text-align: center">
    <label for="codeInput"><?php echo $lang['enter_code_question']; ?></label>
    <input type="text" id="codeInput" name="code" required>
    <button type="submit" class="btn btn-success"><?php echo $lang['show_question']; ?></button>
</form>

<?php

require_once 'config.php';

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: main_page.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $admin_login = isset($_POST['admin_login']) ? 1 : 0;

    $user_query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($user_query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {

            if ($admin_login && $user['is_admin']) {
                $_SESSION["is_admin"] = 1;

                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $email;
                header("location: main_page.php");
                exit;
            } else if ($admin_login && !$user['is_admin']) {
                header("location: index.php?status=not_admin");
            } else {
                $_SESSION["is_admin"] = 0;

                $_SESSION["loggedin"] = true;
                $_SESSION["email"] = $email;
                header("location: main_page.php");
                exit;
            }

        } else {
            header("location: index.php?status=password_error");
        }
    } else {
        header("location: index.php?status=user_not_exists");
    }
}

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var lang = {
        error: '<?php echo addslashes($lang['error']); ?>',
        passwordMismatch: '<?php echo addslashes($lang['password_mismatch']); ?>',
        userNotFound: '<?php echo addslashes($lang['user_not_found']); ?>',
        notAdmin: '<?php echo addslashes($lang['user_not_admin']); ?>'
    };


    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status === 'password_error') {
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.passwordMismatch
            });
        } else if (status === 'user_not_exists') {
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.userNotFound
            });
        } else if (status === 'not_admin') {
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.notAdmin
            });
        }
    });
</script>

<script src="scripts/show_question.js"></script>

</body>

</html>