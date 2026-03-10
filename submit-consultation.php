<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$location = trim($_POST['location'] ?? '');
$topic = trim($_POST['topic'] ?? '');
$messageText = trim($_POST['message'] ?? '');

if ($name === '' || $email === '') {
    die('Missing required fields.');
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int) $_ENV['SMTP_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM'], 'Polina Kravtsova Legal Advisory');
    $mail->addAddress($_ENV['ADMIN_EMAIL']);
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'New free consultation request';
    $mail->Body =
        "New free consultation request\n\n" .
        "Name: $name\n" .
        "Email: $email\n" .
        "Phone: $phone\n" .
        "Location: $location\n" .
        "Topic: $topic\n" .
        "Message: $messageText\n";

    $mail->send();

    echo 'Request received. Email sent successfully.';
} catch (Exception $e) {
    echo 'Form submitted, but email failed: ' . $mail->ErrorInfo;
}