<?php
ini_set("display_errors", 'On');
error_reporting(E_ALL);
require_once 'emailController.php';
require_once 'lib/db.php';
session_start();

$db = getDb();
$errors = array();
$username = '';
$email = '';

/**ユーザーを認証する */
function verifyUser($token)
{
    global $db;
    $verifyQuery = ("SELECT * FROM users WHERE token = :token LIMIT 1");
    $verifyStmt = $db->prepare($verifyQuery);
    $verifyStmt->bindValue(':token', $token, PDO::PARAM_STR);
    $verifyStmt->execute();

    /**ユーザーが存在していたら */
    if ($verifyStmt->rowCount() > 0) {
        $user = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        $updateVerifyQuery = ("UPDATE users SET verified = 1 WHERE token = :token");
        $updateVerifyStmt = $db->prepare($updateVerifyQuery);
        $updateVerifyStmt->bindValue(':token', $token, PDO::PARAM_STR);

        if ($updateVerifyStmt->execute()) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['verified'] = 1;
            header('location: new.php');
            exit();
        } else {
            echo 'ユーザーが見つかりません';
        }
    }
}

/**
 * signup-btn
 */

/**サインアップのバリデート */
if (isset($_POST['signup-btn'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_1 = $_POST['password_1'];
    $password_2 = $_POST['password_2'];

    /**名前 */
    if (empty($username)) {
        $errors['username'] = "名前を入力してください";
    }
    if (preg_match('/( |　)/', $username)) {
        $errors['username'] = "名前に空白は入力できません";
    } elseif (mb_strlen($username) > 20) {
        $errors['username'] = "名前は20文字以内で入力してください";
    }

    /**メールアドレス */
    if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD", $email)) {
        $errors['email'] = "メールアドレスの形式が間違っています";
    }
    if (empty($email)) {
        $errors['email'] = "メールアドレスを入力してください";
    }

    /**パスワード */
    if (!preg_match("/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,20}+\z/", $password_1)) {
        $errors['password'] = "パスワードは半角英小文字大文字数字をそれぞれ1種類以上含む8文字以上20文字以下で入力してください";
    }
    if (preg_match('/( |　)/', $password_1)) {
        $errors['password'] = "パスワードに空白は入力できません";
    }
    if ($password_1 !== $password_2) {
        $errors['password'] = "パスワードが一致していません";
    }

    try {
        /**メールアドレスの存在チェック */
        $checkEmailQuery = ("SELECT email FROM users WHERE email = :email");
        $checkEmailStmt = $db->prepare($checkEmailQuery);
        $checkEmailStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $checkEmailStmt->execute();

        if ($checkEmailStmt->rowCount() > 0) {
            $errors['email'] = "メールアドレスは既に使われています";
        }

        /**上記までにエラーが存在しなかったらユーザー情報を登録 */
        if (count($errors) === 0) {
            $password_1 = password_hash($password_1, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            $verified = false;

            /**ユーザー情報をINSERT */
            $insertUsersQuery = "INSERT INTO users (username, email, verified, token, password) VALUES (:username, :email, :verified, :token, :password)";
            $insertUsersStmt = $db->prepare($insertUsersQuery);
            $insertUsersStmt->bindValue(':username', $username, PDO::PARAM_STR);
            $insertUsersStmt->bindValue(':email', $email, PDO::PARAM_STR);
            $insertUsersStmt->bindValue(':verified', intval($verified), PDO::PARAM_INT);
            $insertUsersStmt->bindValue(':token', $token, PDO::PARAM_STR);
            $insertUsersStmt->bindValue(':password', $password_1, PDO::PARAM_STR);

            if ($insertUsersStmt->execute()) {
                /**セッションにデータを格納 */
                $user_id = $db->lastInsertId();
                $_SESSION['id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['verified'] = $verified;
                $_SESSION['token'] = $token;
                /**メールアドレスから認証を行わせる */
                sendVerificationEmail($email, $token);
            } else {
                $errors['db_error'] = "Error: 登録に失敗しました";
            }
            header('location: new.php');
            exit();
        }
    } catch (PDOException $e) {
        die("Error: {$e->getMessage()}");
    }
}

/**
 * login-btn
 */
if (isset($_POST['login-btn'])) {
    $username = $_POST['username'];
    $password = $_POST['password_1'];

    /**名前かメールアドレス */
    if (empty($username)) {
        $errors['username'] = "名前 or メールアドレスを入力してください";
    } elseif (preg_match('/( |　)/', $username)) {
        $errors['username'] = "空白は入力できません";
    }

    /**パスワード */
    if (empty($password)) {
        $errors['password'] = "パスワードを入力してください";
    } elseif (preg_match('/( |　)/', $password)) {
        $errors['password'] = "空白は入力できません";
    }

    try {
        if (count($errors) === 0) {
            $userQuery = $db->prepare("SELECT * FROM users WHERE email = :email OR username = :username LIMIT 1");
            $userQuery->bindValue(':username', $username, PDO::PARAM_STR);
            $userQuery->bindValue(':email', $username, PDO::PARAM_STR);
            $user = $userQuery->execute();
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (!empty($user) && password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['verified'] = $user['verified'];

                header('location: new.php');
                exit();
            } else {
                $errors['login_fail'] = "入力情報が正しくありません";
            }
        }
    } catch (PDOException $e) {
        die("Error: {$e->getMessage()}");
    }
}

/**
 * logout
 */
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['verified']);
    header('location: login.php');
    exit();
}
