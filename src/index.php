<?php
ini_set("display_errors", 'On');
error_reporting(E_ALL);

require_once 'controllers/authController.php';
require_once 'lib/db.php';
require_once 'lib/escape.php';

// if (isset($_GET['token'])) {
//   $token = $_GET['token'];
//   verifyUser($token);
// }

if (!isset($_SESSION['id'])) {
  header('location: login.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>読書一覧</title>
</head>

<body>
  <a href="new.php">戻る</a>
  <table>
    <thead>
      <tr>
        <th>書籍名</th>
        <th>著者名</th>
        <th>読書状況</th>
        <th>評価</th>
        <th>メモ</th>
        <th>投稿日時</th>
        <th>更新日時</th>
      </tr>
    </thead>
    <tbody>
      <form action="">
        <input type="submit" name="update" value="更新">
        <?php
        try {
          $db = getDb();
          $readListQuery = "SELECT * FROM books";
          $readListStmt = $db->prepare($readListQuery);
          $readListStmt->execute();

          while ($row = $readListStmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <tr>
              <td>
                <input type="text" name="title" value="<?php echo h($row['title']) ?>">
              </td>
              <td>
                <input type="text" name="author" value="<?php echo h($row['author']) ?>">
              </td>
              <td>
                <select name="status">
                  <option value="読んでいない" <?php if ($row['status'] == "読んでいない") echo 'selected="selected"'; ?>>読んでいない</option>
                  <option value="読んでる" <?php if ($row['status'] == "読んでる") echo 'selected="selected"'; ?>>読んでる</option>
                  <option value="読み終えた" <?php if ($row['status'] == "読み終えた") echo 'selected="selected"'; ?>>読み終えた</option>
                </select>
              </td>
              <td>
                <select name="score">
                  <option value="1" <?php if ($row['score'] == "1") echo 'selected="selected"'; ?>>1</option>
                  <option value="2" <?php if ($row['score'] == "2") echo 'selected="selected"'; ?>>2</option>
                  <option value="3" <?php if ($row['score'] == "3") echo 'selected="selected"'; ?>>3</option>
                  <option value="4" <?php if ($row['score'] == "4") echo 'selected="selected"'; ?>>4</option>
                  <option value="5" <?php if ($row['score'] == "5") echo 'selected="selected"'; ?>>5</option>
                </select>
              </td>
              <td>
                <textarea name="note" id="note" cols="30" rows="5"><?php echo h($row['note']) ?></textarea>
              </td>
              <td>
                <?php echo $row['created_at'] ?>
              </td>
              <td>
                <?php echo $row['updated_at'] ?>
              </td>
            </tr>
        <?php
          }
        } catch (PDOException $e) {
          die("エラーメッセージ:{$e->getMessage()}");
        }
        ?>
      </form>
    </tbody>
  </table>
  <script src="script.js"></script>
</body>

</html>
