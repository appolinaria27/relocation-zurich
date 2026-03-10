<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

$sessionId = $_GET['session_id'] ?? '';

if (!$sessionId) {
  die('Missing Stripe session ID.');
}

try {
  $session = \Stripe\Checkout\Session::retrieve($sessionId);

  $bookingData = [
    'stripe_session_id' => $session->id,
    'payment_status' => $session->payment_status,
    'package' => $session->metadata->package ?? '',
    'package_name' => $session->metadata->package_name ?? '',
    'price_chf' => $session->metadata->price_chf ?? '',
    'name' => $session->metadata->name ?? '',
    'email' => $session->metadata->email ?? '',
    'phone' => $session->metadata->phone ?? '',
    'location' => $session->metadata->location ?? '',
    'preferred' => $session->metadata->preferred ?? '',
    'message' => $session->metadata->message ?? '',
    'created_at' => date('Y-m-d H:i:s'),
  ];

  $emailSent = false;
  $emailError = '';

  if ($session->payment_status === 'paid') {
    if (!is_dir('bookings')) {
      mkdir('bookings', 0777, true);
    }

    $safeSessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $session->id);
    $filename = 'bookings/booking-' . time() . '-' . $safeSessionId . '.json';

    if (!file_exists($filename)) {
      file_put_contents(
        $filename,
        json_encode($bookingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
      );
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
      $mail->CharSet = 'UTF-8';

      $mail->setFrom($_ENV['MAIL_FROM'], 'Polina Kravtsova Legal Advisory');
      $mail->addAddress($_ENV['ADMIN_EMAIL']);

      if (!empty($bookingData['email'])) {
        $mail->addReplyTo($bookingData['email'], $bookingData['name'] ?: 'Client');
      }

      $mail->Subject = 'New Paid Booking Received';

      $mail->Body =
        "A new paid consultation booking has been received.\n\n" .
        "Package: {$bookingData['package_name']}\n" .
        "Price: CHF {$bookingData['price_chf']}\n" .
        "Name: {$bookingData['name']}\n" .
        "Email: {$bookingData['email']}\n" .
        "Phone / WhatsApp: {$bookingData['phone']}\n" .
        "Current location: {$bookingData['location']}\n" .
        "Preferred consultation format: {$bookingData['preferred']}\n\n" .
        "Message:\n{$bookingData['message']}\n\n" .
        "Stripe session ID: {$bookingData['stripe_session_id']}\n" .
        "Payment status: {$bookingData['payment_status']}\n" .
        "Submitted at: {$bookingData['created_at']}\n";

      $mail->send();
      $emailSent = true;

    } catch (Exception $e) {
      $emailError = $mail->ErrorInfo;
    }
  }

} catch (Exception $e) {
  die('Error retrieving Stripe session: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Payment successful</title>
</head>
<body>
  <h1>Payment successful</h1>
  <p>Your consultation request has been received.</p>
  <p>Thank you for your booking.</p>

  <?php if (!$emailSent && !empty($emailError)): ?>
    <p style="color:#a00;">Booking saved, but notification email failed: <?= htmlspecialchars($emailError) ?></p>
  <?php endif; ?>
</body>
</html>