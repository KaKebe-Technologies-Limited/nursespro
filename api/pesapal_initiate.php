<?php
/**
 * NursesPro Academy - Start a Pesapal payment
 * POST {phone?}  -> logged-in student/tutor/admin (in practice: students)
 *
 * If Pesapal isn't configured yet (no consumer key/secret in config/pesapal.php),
 * falls back to an instant, clearly-labeled demo grant so the rest of the app
 * (and the paywall UX) stays testable before real credentials are added.
 */
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/pesapal.php';

$user = require_login_api();
$data = json_input();
$phone = trim($data['phone'] ?? $user['phone'] ?? '');

if (!pesapal_is_configured()) {
  $reference = 'DEMO' . time() . rand(100, 999);
  $expiry = date('Y-m-d H:i:s', strtotime('+' . PESAPAL_ACCESS_MONTHS . ' months'));

  db()->prepare('INSERT INTO payments (user_id, amount, method, phone, reference, status, expiry_granted) VALUES (?,?,?,?,?,\'paid\',?)')
    ->execute([$user['id'], PESAPAL_AMOUNT, 'Demo (Pesapal not configured)', $phone, $reference, $expiry]);
  db()->prepare('UPDATE users SET access_expiry=? WHERE id=?')->execute([$expiry, $user['id']]);

  respond([
    'success' => true,
    'mode' => 'demo',
    'message' => 'Pesapal is not configured yet, so demo access was granted instead. Add your Consumer Key/Secret in config/pesapal.php to accept real payments.',
    'expiry' => $expiry,
  ]);
}

try {
  $token = pesapal_get_token();
  $ipnId = pesapal_get_ipn_id($token, app_base_url() . '/api/pesapal_ipn.php');

  $nameParts = array_pad(explode(' ', trim($user['name']), 2), 2, '');
  $merchantRef = 'NP' . $user['id'] . '-' . time();
  $expiry = date('Y-m-d H:i:s', strtotime('+' . PESAPAL_ACCESS_MONTHS . ' months'));

  $order = pesapal_submit_order($token, [
    'id' => $merchantRef,
    'currency' => PESAPAL_CURRENCY,
    'amount' => PESAPAL_AMOUNT,
    'description' => 'NursesPro Academy - ' . PESAPAL_ACCESS_MONTHS . ' Month Access',
    'callback_url' => app_base_url() . '/pesapal_callback.php',
    'notification_id' => $ipnId,
    'billing_address' => [
      'email_address' => $user['email'],
      'phone_number' => $phone,
      'first_name' => $nameParts[0],
      'last_name' => $nameParts[1],
    ],
  ]);

  db()->prepare('INSERT INTO payments (user_id, amount, method, phone, reference, status, expiry_granted) VALUES (?,?,\'Pesapal\',?,?,\'pending\',?)')
    ->execute([$user['id'], PESAPAL_AMOUNT, $phone, $order['order_tracking_id'], $expiry]);

  respond(['success' => true, 'mode' => 'pesapal', 'redirect_url' => $order['redirect_url']]);
} catch (PesapalException $e) {
  respond(['success' => false, 'message' => $e->getMessage()], 502);
}
