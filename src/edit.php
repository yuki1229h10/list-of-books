<?php
require_once 'lib/db.php';
require_once 'lib/escape.php';

$errors = array();
$id = $_GET['id'];

/**編集するレコードを取得 */
try {
  $db = getDb();
  $selectListQuery = "SELECT * FROM books WHERE id = :id";
  $selectListStmt = $db->prepare($selectListQuery);
  $selectListStmt->bindParam(':id', $id);

  /**元々表示されている編集されていない項目 */
  if ($selectListStmt->execute()) {
    $row = $selectListStmt->fetch(PDO::FETCH_ASSOC);
    $title = $row['title'];
    $author = $row['author'];
    $status = $row['status'];
    $score = $row['score'];
    $note = $row['note'];
  }
} catch (PDOException $e) {
  die("Error: {$e->getMessage()}");
}


if (isset($_POST['update-btn'])) {

  /**編集した項目を変数に落としこむ */
  $id = $_POST['id'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $status  = $_POST['status'];
  $score = $_POST['score'];
  $note = $_POST['note'];

  /**書籍名 */
  if (!strlen($title)) {
    $errors['title'] = '書籍名を入力してください';
  }
  if (preg_match('/( |　)/', $title)) {
    $errors['title'] = "書籍名に空白は入力できません";
  } elseif (strlen($title > 255)) {
    $errors['title'] = '書籍名は255文字以内で入力してください';
  }

  /**著者名 */
  if (!strlen($author)) {
    $errors['author'] = '著者名を入力してください';
  }
  if (preg_match('/( |　)/', $author)) {
    $errors['author'] = "著者名に空白は入力できません";
  } elseif (strlen($author) > 255) {
    $errors['author'] = '著者名は255文字以内で入力してください';
  }

  /**メモ */
  if (strlen($note) > 255) {
    $errors['note'] = 'メモは255文字以内で入力してください';
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
  <title>編集画面</title>
</head>

<body>
  <h1>編集画面</h1>
  <a href="index.php">戻る</a>
  <form action="edit.php" method="POST">
    <?php if (count($errors) > 0) : ?>
      <ul class="auth__error-list">
        <?php foreach ($errors as $error) : ?>
          <li class="li-text auth__error-item"><?php echo $error; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <div>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <label for="title">書籍名</label>
      <input type="text" name="title" id="title" value="<?php echo h($title) ?>">
    </div>
    <div>
      <label for="author">著者名</label>
      <input type="text" name="author" id="author" value="<?php echo h($author) ?>">
    </div>
    <div>
      <label>読書状況</label>
      <div>
        <div>
          <input type="radio" name="status" id="status1" value="読んでいない" <?php if ($status === "読んでいない") {
                                                                          echo 'checked="checked"';
                                                                        } ?>>
          <label for="status1">読んでいない</label>
        </div>
        <div>
          <input type="radio" name="status" id="status2" value="読んでる" <?php if ($status === "読んでる") {
                                                                        echo 'checked="checked"';
                                                                      } ?>>
          <label for="status2">読んでる</label>
        </div>
        <div>
          <input type="radio" name="status" id="status3" value="読み終えた" <?php if ($status === "読み終えた") {
                                                                          echo 'checked="checked"';
                                                                        } ?>>
          <label for="status3">読み終えた</label>
        </div>
      </div>
    </div>
    <div>
      <label for="score">評価</label>
      <select name="score" id="score">
        <option value="1" <?php if ($row['score'] == "1") echo 'selected="selected"'; ?>>1</option>
        <option value="2" <?php if ($row['score'] == "2") echo 'selected="selected"'; ?>>2</option>
        <option value="3" <?php if ($row['score'] == "3") echo 'selected="selected"'; ?>>3</option>
        <option value="4" <?php if ($row['score'] == "4") echo 'selected="selected"'; ?>>4</option>
        <option value="5" <?php if ($row['score'] == "5") echo 'selected="selected"'; ?>>5</option>
      </select>
    </div>
    <div>
      <label for="note">メモ</label>
      <textarea type="text" name="note" id="note" cols="30" rows="10" placeholder="メモ"><?php echo h($note) ?></textarea>
    </div>
    <div>
      <button type="submit" name="update-btn">更新する</button>
    </div>
  </form>
</body>

</html>
