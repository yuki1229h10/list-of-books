<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '../../../vendor/autoload.php';
require_once 'controllers/authController.php';
require_once 'lib/db.php';

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';


function sendVerificationEmail($email, $token)
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
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

    $mail->setFrom($_ENV['EMAIL'], $_ENV['Yuki']);
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = mb_encode_mimeheader('Verification to todo-app');
    $mail->Body = '<!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Verify email</title>
        </head>

        <body>
            <div>
                <p>Thank you for signing up on our website. Please click on the link below
                    to verify your email.</p>
                <a href="http://localhost:8888/todo_app/public/index.php?token=' . $token . '">Verify your email address</a>
            </div>
        </body>

        </html>';
    $mail->AltBody = '非HTML受信者用本文';
    $mail->send();
}
