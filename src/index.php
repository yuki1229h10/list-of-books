<?php
require_once 'controllers/authController.php';
require_once 'lib/escape.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    verifyUser($token);
}

if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>

<body>
    <div>
        <div>

            <?php if (isset($_SESSION['message'])) : ?>
                <div class="alert <?php echo $_SESSION['alert-class']; ?>">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['alert-class']);
                    ?>
                </div>
            <?php endif; ?>

            <h3>Welcome, <?php echo h($_SESSION['username']); ?></h3>

            <a href="index.php?logout=1">logout</a>

            <?php if (!$_SESSION['verified']) : ?>
                <div>
                    You need to verify your account.
                    Sign in to your email account and click on the
                    verification link we just emailed you at
                    <strong><?php echo $_SESSION['email']; ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['verified']) : ?>
                <button>I am verified!</button>
            <?php endif; ?>

        </div>

    </div>
</body>

</html>
