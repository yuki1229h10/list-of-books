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

try {
  $db = getDb();
  $readListQuery = "SELECT * FROM books";
  $readListStmt = $db->prepare($readListQuery);
  $readListStmt->execute();
  $readItemCount = $readListStmt->rowCount();

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
        <form action="" method="POST">
          <?php

          while ($row = $readListStmt->fetch(PDO::FETCH_ASSOC)) {
          ?>
            <tr>
              <td>
                <?php echo h($row['title']) ?>
              </td>
              <td>
                <?php echo h($row['author']) ?>
              </td>
              <td>
                <?php echo h($row['status']) ?>
              </td>
              <td>
                <?php echo h($row['score']) ?>
              </td>
              <td>
                <?php echo h($row['note']) ?>
              </td>
              <td>
                <?php echo $row['created_at'] ?>
              </td>
              <td>
                <?php if ($row['updated_at']) echo $row['updated_at'] ?>
              </td>
              <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>">編集</a>
              </td>
              <td>
                <a href="delete_done?id=<?php echo $row['id'] ?>">削除</a>
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
