<?php

require_once 'controllers/authController.php';
require_once 'lib/escape.php';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="好き、気になる本を記録するサイト">
    <title>サインアップ</title>
    <link rel="stylesheet" href="stylesheets/css/main.css">
    <script src="https://kit.fontawesome.com/a610f5c929.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="auth">
        <div class="auth__form-wrapper">
            <form action="signup.php" method="POST" novalidate>
                <h3 class="heading-h3 auth__top">サインアップ</h3>
                <div class="auth__inner">

                    <?php if (count($errors) > 0) : ?>
                        <ul class="auth__error-list">
                            <?php foreach ($errors as $error) : ?>
                                <li class="li-text font-bold auth__error-item"><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="auth__input-wrapper">
                        <div class="auth__icon-wrapper">
                            <span class="fa-solid fa-user auth__icon"></span>
                        </div>
                        <input type="text" name="username" value="<?php echo escape($username); ?>" placeholder="名前" class="input-text auth__input">
                    </div>
                    <div class="auth__input-wrapper">
                        <div class="auth__icon-wrapper">
                            <span class="fa-solid fa-envelope auth__icon"></span>
                        </div>
                        <input type="email" name="email" value="<?php echo escape($email); ?>" placeholder="メールアドレス" class="input-text auth__input">
                    </div>
                    <div class="auth__input-wrapper">
                        <div class="auth__icon-wrapper">
                            <span class="fa-solid fa-key auth__icon"></span>
                        </div>
                        <input type="password" name="password_1" placeholder="パスワード" class="input-text auth__input">
                    </div>
                    <div class="auth__input-wrapper">
                        <div class="auth__icon-wrapper">
                            <span class="fa-solid fa-key auth__icon"></span>
                        </div>
                        <input type="password" name="password_2" placeholder="パスワード（確認）" class="input-text auth__input">
                    </div>
                    <div class="auth__btn-wrapper">
                        <button type="submit" name="signup-btn" class="btn auth__btn">登録する</button>
                    </div>
                    <div class="auth__text-wrapper">
                        <p class="p-text auth__text">既に登録している方は</p>
                        <a href="login.php" class="a-text link auth__link">ログイン</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
