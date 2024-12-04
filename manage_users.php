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
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $lang['error'] . $e->getMessage();
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
    <link rel="stylesheet" href="css/change_password.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/main_page.css">
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
                echo '<li class="nav-item active">
            <a class="nav-link" href="#">' . $lang['manage_users'] . '</a>
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



<table class="table">
    <thead>
    <tr>
        <th><?php echo $lang['email']; ?></th>
        <th><?php echo $lang['admin']; ?></th>
        <th><?php echo $lang['actions']; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
            <td>
                <a href="database_services/edit_user.php?email=<?php echo $user['email']; ?>" class="btn btn-primary"><?php echo $lang['edit']; ?></a>
                <a href="database_services/delete_user.php?email=<?php echo $user['email']; ?>" class="btn btn-danger"><?php echo $lang['delete']; ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<br><br>

<div class="centered-items">
    <button type="submit" class="btn btn-success"
            onclick="location.href='database_services/add_user.php';"><?php echo $lang['add_user']; ?></button>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var lang = {
        error: '<?php echo addslashes($lang['error']); ?>',
        success: '<?php echo addslashes($lang['success']); ?>',
        successfullyDeleted: '<?php echo addslashes($lang['user_successfully_deleted']); ?>',
        successfullyCreated: '<?php echo addslashes($lang['user_successfully_created']); ?>',
        successfullyEdited: '<?php echo addslashes($lang['user_successfully_edited']); ?>',
    };


    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status === 'success_deleted') {
            Swal.fire({
                icon: 'success',
                title: lang.success,
                text: lang.successfullyDeleted
            });
        } else if (status === 'success_added') {
            Swal.fire({
                icon: 'success',
                title: lang.success,
                text: lang.successfullyCreated
            });
        } else if (status === 'success_edited') {
            Swal.fire({
                icon: 'success',
                title: lang.success,
                text: lang.successfullyEdited
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: lang.error,
                text: lang.error + decodeURIComponent(message)
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