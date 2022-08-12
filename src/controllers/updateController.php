<?php

require_once 'lib/db.php';


try {
  $db = getDb();
  $updateListQuery = "";
  $updateListStmt = $db->prepare($updateListQuery);
  $updateListStmt->bindParam(':title', $title);
  $updateListStmt->bindParam(':author', $author);
  $updateListStmt->bindParam(':status', $status);
  $updateListStmt->bindParam(':score', $score);
  $updateListStmt->bindParam(':note', $note);

  for ($i = 1; $i <= $_POST['cnt']; $i++) {
    $title = $_POST['title' . $i];
    $author = $_POST['author' . $i];
    $status = $_POST['status' . $i];
    $score = $_POST['score' . $i];
    $note = $_POST['note' . $i];
    $updateListStmt->execute();
  }


  header('Location: index.php');
  // header('Location: http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php');
} catch (PDOException $e) {
  die("エラーメッセージ：{$e->getMessage()}");
}
