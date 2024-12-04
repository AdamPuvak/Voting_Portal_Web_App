<?php
session_start();

require_once '../config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require '../languages/' . $_SESSION['lang'] . '.php';


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$userEmail = isset($_GET['email']) ? $_GET['email'] : null;

if (!$userEmail) {
    header("location: ../manage_users.php?status=error&message=UserEmailRequired");
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("location: ../manage_users.php?status=error&message=UserNotFound");
        exit;
    }
} catch (PDOException $e) {
    header("location: ../manage_users.php?status=error&message=" . urlencode($e->getMessage()));
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
    <h2><?php echo $lang['edit_user']; ?></h2>
    <form action="update_user.php" method="post">
        <input type="hidden" name="original_email" value="<?php echo htmlspecialchars($user['email']); ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required class="form-control"
                   value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>
        <div class="form-group">
            <label for="set_admin">Admin:</label>
            <input type="checkbox" id="set_admin" name="set_admin" value="1" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
        </div>
        <div class="form-group">
            <label for="password"><?php echo $lang['set_password']; ?></label>
            <input type="password" id="password" name="password" class="form-control"
                   value="">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $lang['edit_user']; ?></button>
    </form>


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