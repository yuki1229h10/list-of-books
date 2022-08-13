<?php
require_once 'controllers/authController.php';
require_once 'lib/escape.php';

$errors = array();
var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="https://kit.fontawesome.com/a610f5c929.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="auth">
        <div class="auth__form-wrapper">
            <form action="login.php" method="POST">
                <h3 class="heading-h3 auth__top">Login</h3>
                <div class="auth__inner">

                    <?php if (count($errors) > 0) : ?>
                        <ul class="auth__error-list">
                            <?php foreach ($errors as $error) : ?>
                                <li class="li-text auth__error-item"><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="auth__input-wrapper">
                        <div class="auth__icon-wrapper">
                            <span class="fa-solid fa-user auth__icon"></span>
                        </div>
                        <input type="text" name="username" value="<?php echo h($username); ?>" placeholder="Username or Email" class="input-text auth__input">
                    </div>
                    <div class="auth__input-wrapper">
                        <div class="auth__icon-wrapper">
                            <span class="fa-solid fa-key auth__icon"></span>
                        </div>
                        <input type="password" name="password_1" placeholder="Password" class="input-text auth__input">
                    </div>
                    <div class="auth__btn-wrapper">
                        <button type="submit" name="login-btn" class="btn auth__btn">Login</button>
                    </div>
                    <div class="auth__text-wrapper">
                        <p class="p-text auth__text">Not yet a member?</p>
                        <a href="signup.php" class="a-text link auth__link">Sign Up</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
