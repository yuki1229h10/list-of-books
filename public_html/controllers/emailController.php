<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'authController.php';
require_once 'lib/db.php';
require __DIR__ . '../../../vendor/autoload.php';

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';


function sendVerificationEmail($email, $token)
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = $_ENV['HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['EMAIL'];
    $mail->Password = $_ENV['PASSWORD'];
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom($_ENV['EMAIL'], $_ENV['USER']);
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = mb_encode_mimeheader('list-of-books');
    $mail->Body = '<!DOCTYPE html>
        <html lang="ja">

        <head>
            <meta charset="UTF-8">
            <title>メールアドレスの認証</title>
        </head>

        <body>
            <div>
                <p>ありがとうございます</p>
                <p>リンクをクリックして認証を行なってください</p>
                <a href="http://localhost:8888/list-of-books/src/new.php?token=' . $token . '">認証を行う</a>
            </div>
        </body>

        </html>';
    $mail->AltBody = '非HTML受信者用本文';
    $mail->send();
}
