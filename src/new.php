<?php

require_once 'controllers/authController.php';
require_once 'lib/db.php';
require_once 'lib/escape.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    verifyUser($token);
}

if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

$errors = array();

$title = '';
$author = '';
$note = '';

var_dump($_SESSION);

/**読書作成のバリデート */
if (isset($_POST['create-btn'])) {

    $status = '';
    if (array_key_exists('status', $_POST)) {
        $status = $_POST['status'];
    }

    $title = $_POST['title'];
    $author = $_POST['author'];
    $status;
    $score = $_POST['score'];
    $note = $_POST['note'];

    /**タイトル */
    if (!strlen($title)) {
        $errors['title'] = '書籍名を入力してください';
    } elseif (strlen($title > 255)) {
        $errors['title'] = '書籍名は255文字以内で入力してください';
    }

    /**著者名 */
    if (!strlen($author)) {
        $errors['author'] = '著者名を入力してください';
    } elseif (strlen($author) > 255) {
        $errors['author'] = '著者名は255文字以内で入力してください';
    }

    /**読書状況 */
    if (!in_array($status, ['読んでいない', '読んでる', '読み終えた'])) {
        $errors['status'] = '読書状況をいずれか入力してください';
    }

    /**メモ */
    if (!strlen($note)) {
        $errors['note'] = 'メモを入力してください';
    } elseif (strlen($note) > 255) {
        $errors['note'] = 'メモは255文字以内で入力してください';
    }

    /**上記までにエラーが存在しなかったら読書情報を登録 */
    if (count($errors) === 0) {

        try {
            $userEmail = $_SESSION['email'];
            $createQuery = $db->prepare("INSERT INTO books (email, title, author, status, score, note) VALUES (:email, :title, :author, :status, :score, :note)");
            $createQuery->bindValue(':email', $userEmail, PDO::PARAM_STR);
            $createQuery->bindValue(':title', $title, PDO::PARAM_STR);
            $createQuery->bindValue(':author', $author, PDO::PARAM_STR);
            $createQuery->bindValue(':status', $status, PDO::PARAM_STR);
            $createQuery->bindValue(':score', $score, PDO::PARAM_INT);
            $createQuery->bindValue(':note', $note, PDO::PARAM_STR);
            $createQuery->execute();
        } catch (PDOException $e) {
            die("Error: {$e->getMessage()}");
        }
        header('location: index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>

<body>
    <div>
        <div>

            <?php if (isset($_SESSION['message'])) : ?>
                <div class="alert <?php echo $_SESSION['alert-class']; ?>">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['alert-class']);
                    ?>
                </div>
            <?php endif; ?>

            <h3>Welcome, <?php echo h($_SESSION['username']); ?></h3>

            <a href="login.php?logout=1">logout</a>
            <a href="index.php">読書一覧</a>

            <?php if (!$_SESSION['verified']) : ?>
                <div>
                    You need to verify your account.
                    Sign in to your email account and click on the
                    verification link we just emailed you at
                    <strong><?php echo $_SESSION['email']; ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['verified']) : ?>

                <h1>読書記録</h1>
                <form action="new.php" method="POST">
                    <?php if (count($errors) > 0) : ?>
                        <ul class="auth__error-list">
                            <?php foreach ($errors as $error) : ?>
                                <li class="li-text auth__error-item"><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div>
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
                                <input type="radio" name="status" id="status1" value="読んでいない">
                                <label for="status1">読んでいない</label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status2" value="読んでる">
                                <label for="status2">読んでる</label>
                            </div>
                            <div>
                                <input type="radio" name="status" id="status3" value="読み終えた">
                                <label for="status3">読み終えた</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="score">評価</label>
                        <select name="score" id="score">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div>
                        <label for="note">メモ</label>
                        <textarea type="text" name="note" id="note" cols="30" rows="10" placeholder="メモ"><?php echo h($note) ?></textarea>
                    </div>
                    <div>
                        <button type="submit" name="create-btn">登録する</button>
                    </div>
                </form>
            <?php endif; ?>

        </div>

    </div>
</body>

</html>
