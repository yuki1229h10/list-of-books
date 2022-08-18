<?php

require_once 'controllers/authController.php';
require_once 'lib/escape.php';

if (!isset($_SESSION['id']) && !isset($_SESSION['verified'])) {
  header('location: login.php');
  exit();
}

if (empty($_SESSION['verified'])) {
  header('location: login.php');
  exit();
}

try {
  $userEmail = $_SESSION['email'];

  $readListQuery = "SELECT * FROM books WHERE email = :email ORDER BY updated_at DESC";
  $readListStmt = $db->prepare($readListQuery);
  $readListStmt->bindValue(':email', $userEmail, PDO::PARAM_STR);
  $readListStmt->execute();
  $readRecordCount = $readListStmt->rowCount();
?>

  <!DOCTYPE html>
  <html lang="ja">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="好き、気になる本を記録するサイト">
    <title>本の一覧</title>
    <link rel="stylesheet" href="stylesheets/css/main.css">
    <script src="https://kit.fontawesome.com/a610f5c929.js" crossorigin="anonymous"></script>
  </head>

  <body>
    <header class="header">
      <nav class="header__nav">
        <h1 class="heading-h1 header__left">
          Library
        </h1>
        <div class="header__right">
          <div class="link header__nav-icon-wrapper">
            <span class="fa-solid fa-plus header__icon"></span>
            <a href="new.php" class="header__link"></a>
          </div>
          <div class="link header__nav-icon-wrapper">
            <span class="fa-solid fa-right-from-bracket header__icon"></span>
            <a href="login.php?logout=1" class="header__link"></a>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <div class="index">
        <div class="div-text index__top">
          本の総数：<?php echo $readRecordCount ?>
        </div>
        <table class="index__table">
          <?php if ($readRecordCount > 0) : ?>
            <thead class="index__thead">
              <tr class="tr-text index__tr">
                <th class="index__th">書籍名</th>
                <th class="index__th">著者名</th>
                <th class="index__th">読書状況</th>
                <th class="index__th">評価</th>
                <th class="index__th">メモ</th>
                <th class="index__th">投稿日時</th>
                <th class="index__th">更新日時</th>
                <th class="index__th">アクション</th>
              </tr>
            </thead>
          <?php endif; ?>
          <tbody class="index__tbody">
            <?php
            while ($row = $readListStmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
              <tr class="tr-text index__tr">
                <td class="index__td">
                  <?php echo escape($row['title']) ?>
                </td>
                <td class="index__td">
                  <?php echo escape($row['author']) ?>
                </td>
                <td class="index__td">
                  <?php echo escape($row['status']) ?>
                </td>
                <td class="index__td">
                  <?php echo escape($row['score']) ?>
                </td>
                <td class="index__td">
                  <?php echo escape($row['note']) ?>
                </td>
                <td class="index__td">
                  <?php echo $row['created_at'] ?>
                </td>
                <td class="index__td">
                  <?php echo $row['updated_at'] ?>
                </td>
                <td class="index__td">
                  <div class="index__flex">
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="link index__btn">編集</a>
                    <a href="controllers/deleteController.php?id=<?php echo $row['id'] ?>" onclick="return deleteFunc()" class="link index__btn error">削除</a>
                  </div>
                </td>
              </tr>
          <?php
            }
          } catch (PDOException $e) {
            die("Error: {$e->getMessage()}");
          }
          ?>
          </tbody>
        </table>
      </div>
    </main>
    <script src="controllers/script.js"></script>
  </body>

  </html>
