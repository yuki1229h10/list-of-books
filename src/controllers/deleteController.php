<?php

require_once '../lib/db.php';

$id = $_GET['id'];

try {
  $db = getDb();
  $deleteListQuery = "DELETE FROM books WHERE id = :id";
  $deleteListStmt = $db->prepare($deleteListQuery);
  $deleteListStmt->bindParam(':id', $id);
  $deleteListStmt->execute();
  header('location:' . "../index.php");
} catch (PDOException $e) {
  die("Error: {$e->getMessage()}");
}
