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


$errors = array();
$id = (isset($_GET['id']) ? $_GET['id'] : '');

/**編集するレコードを取得 */
try {
  $db = getDb();
  $selectListQuery = "SELECT * FROM books WHERE id = :id";
  $selectListStmt = $db->prepare($selectListQuery);
  $selectListStmt->bindParam(':id', $id);

  /**元々表示されている編集されていない項目 */
  $selectListStmt->execute();
  $row = $selectListStmt->fetch(PDO::FETCH_ASSOC);
  $title = $row['title'] ?? '';
  $author = $row['author'] ?? '';
  $status = $row['status'] ?? '';
  $score = $row['score'] ?? '';
  $note = $row['note'] ?? '';
} catch (PDOException $e) {
  die("Error: {$e->getMessage()}");
}


if (isset($_POST['update-btn'])) {

  /**編集した項目を変数に落としこむ */
  $id = $_POST['id'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $status = $_POST['status'];
  $score = $_POST['score'];
  $note = $_POST['note'];

  /**書籍名 */
  if (empty($title)) {
    $errors['title'] = '書籍名を入力してください';
  }
  if (preg_match('/( |　)/', $title)) {
    $errors['title'] = "書籍名に空白は入力できません";
  } elseif (mb_strlen($title) > 20) {
    $errors['title'] = '書籍名は20文字以内で入力してください';
  }

  /**著者名 */
  if (empty($author)) {
    $errors['author'] = '著者名を入力してください';
  }
  if (preg_match('/( |　)/', $author)) {
    $errors['author'] = "著者名に空白は入力できません";
  } elseif (mb_strlen($author) > 20) {
    $errors['author'] = '著者名は20文字以内で入力してください';
  }

  /**メモ */
  if (preg_match('/( |　)/', $note)) {
    $errors['note'] = "メモに空白は入力できません";
  } elseif (mb_strlen($note) > 30) {
    $errors['note'] = 'メモは30文字以内で入力してください';
  }

  /**上記までにエラーが存在しなかったら読書情報を更新 */
  if (count($errors) === 0) {

    try {
      $db = getDb();
      $updateListQuery = "UPDATE books SET title = :title, author = :author, status = :status, score = :score, note = :note WHERE id = :id";
      $updateListStmt = $db->prepare($updateListQuery);
      $updateListStmt->bindParam(':id', $id);
      $updateListStmt->bindParam(':title', $title);
      $updateListStmt->bindParam(':author', $author);
      $updateListStmt->bindParam(':status', $status);
      $updateListStmt->bindParam(':score', $score);
      $updateListStmt->bindParam(':note', $note);

      $updateListStmt->execute();
      header('location: index.php');
    } catch (PDOException $e) {
      die("Error: {$e->getMessage()}");
    }
  }
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="好き、気になる本を記録するサイト">
  <title>編集画面</title>
  <link rel="stylesheet" href="stylesheets/css/main.css">
  <script src="https://kit.fontawesome.com/a610f5c929.js" crossorigin="anonymous"></script>
</head>

<body>
  <header class="header">
    <nav class="header__nav">
      <h1 class="heading-h1 header__left">
        Edit book
      </h1>
      <div class="header__right">
        <div class="link header__nav-icon-wrapper">
          <span class="fa-solid fa-angle-left header__icon"></span>
          <a href="index.php" class="header__link"></a>
        </div>
        <div class="link header__nav-icon-wrapper">
          <span class="fa-solid fa-right-from-bracket header__icon"></span>
          <a href="login.php?logout=1" class="header__link"></a>
        </div>
      </div>
    </nav>
  </header>
  <main>
    <div class="new">
      <div class="new__form-wrapper">
        <form action="edit.php" method="POST">

          <?php if (count($errors) > 0) : ?>
            <ul class="auth__error-list">
              <?php foreach ($errors as $error) : ?>
                <li class="li-text font-bold auth__error-item"><?php echo $error; ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="mb-2 new__column">
            <input type="hidden" name="id" value="<?php echo escape($id); ?>">
            <label for="title" class="label-text">書籍名</label>
            <input type="text" name="title" id="title" value="<?php echo escape($title) ?>" class="input-text">
          </div>
          <div class="mb-2 new__column">
            <label for="author" class="label-text">著者名</label>
            <input type="text" name="author" id="author" value="<?php echo escape($author) ?>" class="input-text">
          </div>
          <div class="mb-2">
            <label class="label-text">読書状況</label>
            <div>
              <div>
                <input type="radio" name="status" id="status1" value="読んでいない" <?php if ($status === "読んでいない") {
                                                                                echo 'checked="checked"';
                                                                              } ?>>
                <label for="status1" class="label-text">読んでいない</label>
              </div>
              <div>
                <input type="radio" name="status" id="status2" value="読んでる" <?php if ($status === "読んでる") {
                                                                              echo 'checked="checked"';
                                                                            } ?>>
                <label for="status2" class="label-text">読んでる</label>
              </div>
              <div>
                <input type="radio" name="status" id="status3" value="読み終えた" <?php if ($status === "読み終えた") {
                                                                                echo 'checked="checked"';
                                                                              } ?>>
                <label for="status3" class="label-text">読み終えた</label>
              </div>
            </div>
          </div>
          <div class="mb-2">
            <label for="score" class="label-text">評価</label>
            <select name="score" id="score" class="select-text">
              <option value="1" <?php if ($score == "1") echo 'selected="selected"'; ?>>1</option>
              <option value="2" <?php if ($score == "2") echo 'selected="selected"'; ?>>2</option>
              <option value="3" <?php if ($score == "3") echo 'selected="selected"'; ?>>3</option>
              <option value="4" <?php if ($score == "4") echo 'selected="selected"'; ?>>4</option>
              <option value="5" <?php if ($score == "5") echo 'selected="selected"'; ?>>5</option>
            </select>
          </div>
          <div class="mb-2 new__column">
            <label for="note" class="label-text">メモ</label>
            <textarea type="text" name="note" id="note" class="textarea-text"><?php echo escape($note) ?></textarea>
          </div>
          <div class="center">
            <button type="submit" name="update-btn" class="btn new__btn">本を更新する</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>

</html>
