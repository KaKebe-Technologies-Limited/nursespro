<?php
/**
 * NursesPro Academy - Pesapal IPN (Instant Payment Notification)
 * GET ?OrderTrackingId=...&OrderNotificationType=...&OrderMerchantReference=...
 *
 * Called server-to-server by Pesapal, NOT by the browser — there is no user
 * session here, so this does not use auth_guard's login checks. This is the
 * authoritative source of truth for payment confirmation in production;
 * pesapal_callback.php additionally double-checks status for the user-facing
 * redirect (and is what actually updates the demo during local development,
 * since Pesapal's servers can't reach a localhost IPN URL).
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/pesapal.php';

$orderTrackingId = $_GET['OrderTrackingId'] ?? '';

if ($orderTrackingId && pesapal_is_configured()) {
  try {
    $token = pesapal_get_token();
    $result = pesapal_get_transaction_status($token, $orderTrackingId);

    if (strtolower($result['payment_status_description'] ?? '') === 'completed') {
      $stmt = db()->prepare('SELECT * FROM payments WHERE reference = ?');
      $stmt->execute([$orderTrackingId]);
      $payment = $stmt->fetch();
      if ($payment && $payment['status'] !== 'paid') {
        db()->prepare("UPDATE payments SET status='paid' WHERE id=?")->execute([$payment['id']]);
        db()->prepare('UPDATE users SET access_expiry=? WHERE id=?')->execute([$payment['expiry_granted'], $payment['user_id']]);
      }
    }
  } catch (PesapalException $e) {
    // Swallow — Pesapal will retry the IPN; nothing useful to return to it on our error.
  }
}

// Pesapal expects this exact acknowledgement shape back.
header('Content-Type: application/json');
echo json_encode([
  'orderNotificationType' => $_GET['OrderNotificationType'] ?? '',
  'orderTrackingId' => $orderTrackingId,
  'orderMerchantReference' => $_GET['OrderMerchantReference'] ?? '',
  'status' => 200,
]);
