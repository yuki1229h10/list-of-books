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

        <h1>読書記録</h1>
        <form action="new.php" method="POST">
          <?php if (count($errors) > 0) : ?>
            <ul class="auth__error-list">
              <?php foreach ($errors as $error) : ?>
                <li class="li-text auth__error-item"><?php echo $error; ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <div>
            <label for="title">書籍名</label>
            <input type="text" name="title" id="title" value="<?php echo h($book['title']) ?>">
          </div>
          <div>
            <label for="author">著者名</label>
            <input type="text" name="author" id="author" value="<?php echo h($book['author']) ?>">
          </div>
          <div>
            <label>読書状況</label>
            <div>
              <div>
                <input type="radio" name="status" id="status1" value="読んでいない">
                <label for="status1">読んでいない</label>
              </div>
              <div>
                <input type="radio" name="status" id="status2" value="読んでる">
                <label for="status2">読んでる</label>
              </div>
              <div>
                <input type="radio" name="status" id="status3" value="読み終えた">
                <label for="status3">読み終えた</label>
              </div>
            </div>
          </div>
          <div>
            <label for="score">評価</label>
            <select name="score" id="score">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
            </select>
          </div>
          <div>
            <label for="note">メモ</label>
            <textarea type="text" name="note" id="note" cols="30" rows="10" placeholder="メモ"><?php echo h($book['note']) ?></textarea>
          </div>
          <div>
            <button type="submit" name="create-btn">登録する</button>
          </div>
        </form>
      <?php endif; ?>

    </div>

  </div>
</body>

</html>
