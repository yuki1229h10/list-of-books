<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="css/main.css">
  <script src="https://kit.fontawesome.com/a610f5c929.js" crossorigin="anonymous"></script>
</head>

<body>
  <div class="auth">
    <div class="auth__form-wrapper">
      <form action="signup.php" method="POST" novalidate>
        <h3 class="heading-h3 auth__top">Register</h3>
        <div class="auth__inner">

          <?php if (count($errors) > 0) : ?>
            <ul class="alert alert-danger auth__error-list">
              <?php foreach ($errors as $error) : ?>
                <li class="li-text auth__error-item"><?php echo $error; ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="auth__input-wrapper">
            <div class="auth__icon-wrapper">
              <span class="fa-solid fa-user auth__icon"></span>
            </div>
            <input type="text" name="username" value="<?php echo h($username); ?>" placeholder="Username" class="input-text auth__input">
          </div>
          <div class="auth__input-wrapper">
            <div class="auth__icon-wrapper">
              <span class="fa-solid fa-envelope auth__icon"></span>
            </div>
            <input type="email" name="email" value="<?php echo h($email); ?>" placeholder="Email" class="input-text auth__input">
          </div>
          <div class="auth__input-wrapper">
            <div class="auth__icon-wrapper">
              <span class="fa-solid fa-key auth__icon"></span>
            </div>
            <input type="password" name="password_1" placeholder="Password" class="input-text auth__input">
          </div>
          <div class="auth__input-wrapper">
            <div class="auth__icon-wrapper">
              <span class="fa-solid fa-key auth__icon"></span>
            </div>
            <input type="password" name="password_2" placeholder="Confirm password" class="input-text auth__input">
          </div>
          <div class="auth__btn-wrapper">
            <button type="submit" name="signup-btn" class="btn auth__btn">Sign Up</button>
          </div>
          <div class="auth__text-wrapper">
            <p class="p-text auth__text">Already a member?</p>
            <a href="login.php" class="a-text link auth__link">Sign In</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
