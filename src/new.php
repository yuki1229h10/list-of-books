<?php

require_once 'controllers/authController.php';
require_once 'lib/db.php';
require_once 'lib/escape.php';

$errors = [];

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    verifyUser($token);
}

if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

function validate($book)
{
    $errors = [];

    if (!strlen($book['title'])) {
        $errors['title'] = '書籍名を入力してください';
    } elseif (strlen($book['title'] > 255)) {
        $errors['title'] = '書籍名は255文字以内で入力してください';
    }

    if (!strlen($book['author'])) {
        $errors['author'] = '著者名を入力してください';
    } elseif (strlen($book['author']) > 255) {
        $errors['author'] = '著者名は255文字以内で入力してください';
    }

    if (!in_array($book['status'], ['読んでいない', '読んでる', '読み終えた'])) {
        $errors['status'] = '読書状況をどれか入力してください';
    }

    if (!in_array($book['score'], ['1', '2', '3', '4', '5'])) {
        $errors['score'] = '評価をどれか入力してください';
    }

    if (!strlen($book['note'])) {
        $errors['note'] = 'メモを入力してください';
    } elseif (strlen($book['note']) > 255) {
        $errors['note'] = 'メモは255文字以内で入力してください';
    }

    return $errors;
}

function createReview($db, $book)
{
    $user_id = $db->lastInsertId();

    $createQuery = $db->prepare("INSERT INTO books (user_id,title, author, status, score, note) VALUES (:user_id,:title, :author, :status, :score, :note)");

    $createQuery->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $createQuery->bindValue(':title', $book['title'], PDO::PARAM_STR);
    $createQuery->bindValue(':author', $book['author'], PDO::PARAM_STR);
    $createQuery->bindValue(':status', $book['status'], PDO::PARAM_STR);
    $createQuery->bindValue(':score', $book['score'], PDO::PARAM_INT);
    $createQuery->bindValue(':note', $book['note'], PDO::PARAM_STR);

    $createQuery->execute();
}

if (isset($_POST['create-btn'])) {

    $status = '';
    if (array_key_exists('status', $_POST)) {
        $status = $_POST['status'];
    }

    $book = [
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'status' => $status,
        'score' => $_POST['score'],
        'note' => $_POST['note']
    ];

    $errors = validate($book);

    if (!count($errors)) {
        $db = getDb();
        createReview($db, $book);
        header("Location: index.php");
    }
}

include 'views/new.php';
