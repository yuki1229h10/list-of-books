<?php

require_once 'lib/db.php';
require_once 'lib/escape.php';

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
  <table>
    <thead>
      <tr>
        <th>書籍名</th>
        <th>著者名</th>
        <th>読書状況</th>
        <th>評価</th>
        <th>メモ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      try {
        $db = getDb();
        $readListQuery = "SELECT * FROM books";
        $readListStmt = $db->prepare($readListQuery);
        $readListStmt->execute();

        while ($row = $readListStmt->fetch(PDO::FETCH_ASSOC)) {
      ?>
          <form action="">
            <tr>
              <td>
                <input type="text" name="title" value="<?php echo h($row['title']) ?>">
              </td>
              <td>
                <input type="text" name="author" value="<?php echo h($row['author']) ?>">
              </td>
              <td>
                <select name="status">
                  <option value="読んでいない"><?php echo ($row['status']) ?></option>
                  <option value="読んでる"><?php echo ($row['status']) ?></option>
                  <option value="読み終えた"><?php echo ($row['status']) ?></option>
                </select>
              </td>
              <td><?php echo h($row['score']) ?></td>
              <td><?php echo h($row['note']) ?></td>
            </tr>
          </form>
      <?php
        }
      } catch (PDOException $e) {
        die("エラーメッセージ:{$e->getMessage()}");
      }
      ?>
    </tbody>
  </table>
</body>

</html>
