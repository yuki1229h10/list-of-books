<?php
// ini_set("display_errors", 'On');
// error_reporting(E_ALL);
require_once 'lib/db.php';

$id = $_POST['id'];
$title = $_POST['title'];
$author = $_POST['author'];
$status = $_POST['status'];
$score = $_POST['score'];
$note = $_POST['note'];

try {
  $db = getDb();
  $updateListQuery = "UPDATE books SET title = :title, author = :author, status = :status, score = :score, note = :note WHERE id = :id";
  $updateListStmt = $db->prepare($updateListQuery);
  $updateListStmt->bindParam(':title', $title);
  $updateListStmt->bindParam(':author', $author);
  $updateListStmt->bindParam(':status', $status);
  $updateListStmt->bindParam(':score', $score);
  $updateListStmt->bindParam(':note', $note);
  $updateListStmt->bindParam(':id', $id);

  $updateListStmt->execute();
  header('location: index.php');
} catch (PDOException $e) {
  $e->getMessage();
  die("Error: {$e->getMessage()}");
}
