<?php

require 'vendor/autoload.php';

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

  if ($session->payment_status === 'paid') {
    if (!is_dir('bookings')) {
      mkdir('bookings', 0777, true);
    }

    $filename = 'bookings/booking-' . time() . '-' . preg_replace('/[^a-zA-Z0-9_-]/', '', $session->id) . '.json';
    file_put_contents($filename, json_encode($bookingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
</body>
</html>