<?php

require_once 'controllers/authController.php';
require_once 'lib/db.php';
require_once 'lib/escape.php';

if (!isset($_SESSION['id'])) {
  header('location: login.php');
  exit();
}

try {
  $db = getDb();
  $readListQuery = "SELECT * FROM books ORDER BY updated_at DESC";
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
    <?php echo $readItemCount ?>
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
              <?php echo h($row['created_at']) ?>
            </td>
            <td>
              <?php echo h($row['updated_at']) ?>
            </td>
            <td>
              <a href="edit.php?id=<?php echo $row['id']; ?>">編集</a>
            </td>
            <td>
              <a href="delete.php?id=<?php echo $row['id'] ?>" id="delete-btn" onclick="return deleteFunc()">削除</a>
            </td>
          </tr>
      <?php
        }
      } catch (PDOException $e) {
        die("Error :{$e->getMessage()}");
      }
      ?>
      </tbody>
    </table>
    <script src="script.js"></script>
  </body>

  </html>
