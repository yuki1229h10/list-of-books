<?php

require_once 'lib/db.php';

$id = $_GET['id'];

try {
  $db = getDb();
  $deleteListQuery = "DELETE FROM books WHERE id = :id";
  $deleteListStmt = $db->prepare($deleteListQuery);
  // $deleteListStmt->bindValue(':id', $id, PDO::PARAM_INT);
  $deleteListStmt->bindParam(':id', $id);
  $deleteListStmt->execute();

  header('location: index.php');
} catch (PDOException $e) {
  // $e->getMessage();
  die("Error: {$e->getMessage()}");
}