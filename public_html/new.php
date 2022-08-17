<?php

require_once 'controllers/authController.php';
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

    /**書籍名 */
    if (empty($title)) {
        $errors['title'] = '書籍名を入力してください';
    }
    if (preg_match('/( |　)/', $title)) {
        $errors['title'] = "書籍名に空白は入力できません";
    } elseif (mb_strlen($title) > 20) {
        $errors['title'] = '書籍名は20文字以内で入力してください';
    }

    /**著者名 */
    if (empty($author)) {
        $errors['author'] = '著者名を入力してください';
    }
    if (preg_match('/( |　)/', $author)) {
        $errors['author'] = "著者名に空白は入力できません";
    } elseif (mb_strlen($author) > 20) {
        $errors['author'] = '著者名は20文字以内で入力してください';
    }

    /**読書状況 */
    if (!in_array($status, ['読んでいない', '読んでる', '読み終えた'])) {
        $errors['status'] = '読書状況をいずれか入力してください';
    }

    /**メモ */
    if (preg_match('/( |　)/', $note)) {
        $errors['note'] = "メモに空白は入力できません";
    } elseif (mb_strlen($note) > 30) {
        $errors['note'] = 'メモは30文字以内で入力してください';
    }

    /**上記までにエラーが存在しなかったら読書情報を登録 */
    if (count($errors) === 0) {

        try {
            $userEmail = $_SESSION['email'];

            $createQuery = $db->prepare("INSERT INTO books (id, email, title, author, status, score, note) VALUES (:id, :email, :title, :author, :status, :score, :note)");
            $createQuery->bindValue(':id', $id, PDO::PARAM_INT);
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
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="好き、気になる本を記録するサイト">
    <title>ホーム</title>
    <link rel="stylesheet" href="stylesheets/css/main.css">
    <script src="https://kit.fontawesome.com/a610f5c929.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- /**メールアドレスの認証がまだ行われていない場合 */ -->
    <?php if (!$_SESSION['verified']) : ?>
        <div class="auth">
            <div class="auth__form-wrapper">
                <div class="auth__inner">
                    <div class="div-text center mb-2">
                        <?php echo $_SESSION['email']; ?>
                    </div>
                    <div class="div-text">
                        メールアドレスに送信されたURLで登録を行なってください
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- /**メールアドレスの認証が行われた後 */ -->
    <?php if ($_SESSION['verified']) : ?>
        <header class="header">
            <nav class="header__nav">
                <h1 class="heading-h1 header__left">
                    Add book
                </h1>
                <div class="header__right">
                    <div class="link header__nav-icon-wrapper">
                        <span class="fa-solid fa-book header__icon"></span>
                        <a href="index.php" class="header__link"></a>
                    </div>
                    <div class="link header__nav-icon-wrapper">
                        <span class="fa-solid fa-right-from-bracket header__icon"></span>
                        <a href="login.php?logout=1" class="header__link"></a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <div class="new">
                <div class="new__form-wrapper">
                    <form action="new.php" method="POST">

                        <?php if (count($errors) > 0) : ?>
                            <ul class="auth__error-list">
                                <?php foreach ($errors as $error) : ?>
                                    <li class="li-text font-bold auth__error-item"><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <div class="mb-2 new__column">
                            <label for="title" class="label-text">書籍名</label>
                            <input type="text" name="title" id="title" value="<?php echo escape($title) ?>" class="input-text">
                        </div>
                        <div class="mb-2 new__column">
                            <label for="author" class="label-text">著者名</label>
                            <input type="text" name="author" id="author" value="<?php echo escape($author) ?>" class="input-text">
                        </div>
                        <div class="mb-2">
                            <label class="label-text">読書状況</label>
                            <div>
                                <div>
                                    <input type="radio" name="status" id="status1" value="読んでいない">
                                    <label for="status1" class="label-text">読んでいない</label>
                                </div>
                                <div>
                                    <input type="radio" name="status" id="status2" value="読んでる">
                                    <label for="status2" class="label-text">読んでる</label>
                                </div>
                                <div>
                                    <input type="radio" name="status" id="status3" value="読み終えた">
                                    <label for="status3" class="label-text">読み終えた</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="score" class="label-text">評価</label>
                            <select name="score" id="score" class="select-text">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="mb-2 new__column">
                            <label for="note" class="label-text">メモ</label>
                            <textarea type="text" name="note" id="note" class="textarea-text"><?php echo escape($note) ?></textarea>
                        </div>
                        <div class="center">
                            <button type="submit" name="create-btn" class="btn new__btn">本を登録する</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    <?php endif; ?>
</body>

</html>
