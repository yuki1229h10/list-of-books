<?php
ini_set("display_errors", 'On');
error_reporting(E_ALL);
require_once 'controllers/emailController.php';
require_once 'lib/db.php';
session_start();

$db = getDb();
$errors = array();
$username = '';
$email = '';

/**
 * signup-btn
 */
if (isset($_POST['signup-btn'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_1 = $_POST['password_1'];
    $password_2 = $_POST['password_2'];

    if (empty($username)) {
        $errors['username'] = "Username required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email address invalid";
    }
    if (empty($email)) {
        $errors['email'] = "Email required";
    }
    if (empty($password_1)) {
        $errors['password'] = "Password required";
    }
    if ($password_1 !== $password_2) {
        $errors['password'] = "The two password do not match";
    }

    try {
        $mailQuery = $db->prepare("SELECT email FROM users WHERE email = :email");
        $mailQuery->bindValue(':email', $email, PDO::PARAM_STR);
        $mailQuery->execute();

        if ($mailQuery->rowCount() > 0) {
            $errors['email'] = "Email address is already used";
        }

        if (count($errors) === 0) {
            $password_1 = password_hash($password_1, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            $verified = false;

            $statement = $db->prepare("INSERT INTO users (username, email, verified, token, password) VALUES (:username, :email, :verified, :token, :password)");
            $statement->bindValue(':username', $username, PDO::PARAM_STR);
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->bindValue(':verified', intval($verified), PDO::PARAM_INT);
            $statement->bindValue(':token', $token, PDO::PARAM_STR);
            $statement->bindValue(':password', $password_1, PDO::PARAM_STR);

            if ($statement->execute()) {
                $user_id = $db->lastInsertId();
                $_SESSION['id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['verified'] = $verified;

                sendVerificationEmail($email, $token);

                $_SESSION['message'] = "You are now logged in!";
                $_SESSION['alert-class'] = "alert-success";
            } else {
                $errors['db_error'] = "Database error: failed to register";
            }
            header('location: index.php');
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

    if (empty($username)) {
        $errors['username'] = "Username required";
    }
    if (empty($password)) {
        $errors['password'] = "Password required";
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

                $_SESSION['message'] = "You are now logged in!";
                $_SESSION['alert-class'] = "alert-success";
                header('location: index.php');
                exit();
            } else {
                $errors['login_fail'] = "Wrong credentials";
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

function verifyUser($token)
{
    global $db;
    $verifyQuery = $db->prepare("SELECT * FROM users WHERE token = :token LIMIT 1");
    $verifyQuery->bindValue(':token', $token, PDO::PARAM_STR);
    $verifyQuery->execute();

    if ($verifyQuery->rowCount() > 0) {
        $user = $verifyQuery->fetch(PDO::FETCH_ASSOC);
        $updateQuery = $db->prepare("UPDATE users SET verified = 1 WHERE token= :token");
        $updateQuery->bindValue(':token', $token, PDO::PARAM_STR);

        if ($updateQuery->execute()) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['verified'] = 1;

            $_SESSION['message'] = "Your email address was successfully verified!";
            $_SESSION['alert-class'] = "alert-success";
            header('location: index.php');
            exit();
        } else {
            echo 'User not found';
        }
    }
}
