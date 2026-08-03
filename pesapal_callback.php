<?php
/**
 * NursesPro Academy - Pesapal payment callback landing page
 * Pesapal redirects the student's browser here after they finish (or abandon)
 * checkout, with ?OrderTrackingId=...&OrderMerchantReference=...
 *
 * This page double-checks the transaction status directly (rather than relying
 * solely on the server-to-server IPN), which also keeps the demo usable during
 * local development where Pesapal's servers can't reach an IPN URL on localhost.
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/pesapal.php';

$user = require_login_page();

$orderTrackingId = $_GET['OrderTrackingId'] ?? '';
$status = 'error';
$message = 'We could not confirm your payment. If you completed checkout, this will update shortly — check your Payment History.';

if ($orderTrackingId && pesapal_is_configured()) {
  try {
    $token = pesapal_get_token();
    $result = pesapal_get_transaction_status($token, $orderTrackingId);
    $statusDesc = strtolower($result['payment_status_description'] ?? '');

    $stmt = db()->prepare('SELECT * FROM payments WHERE reference = ?');
    $stmt->execute([$orderTrackingId]);
    $payment = $stmt->fetch();

    if ($statusDesc === 'completed') {
      if ($payment && $payment['status'] !== 'paid') {
        db()->prepare("UPDATE payments SET status='paid' WHERE id=?")->execute([$payment['id']]);
        db()->prepare('UPDATE users SET access_expiry=? WHERE id=?')->execute([$payment['expiry_granted'], $payment['user_id']]);
      }
      $status = 'success';
      $message = 'Payment confirmed! Your access is now active.';
    } elseif (in_array($statusDesc, ['failed', 'invalid'], true)) {
      $status = 'failed';
      $message = 'Payment was not completed (' . ($result['payment_status_description'] ?? 'failed') . '). You can try again from your dashboard.';
    } else {
      $status = 'pending';
      $message = 'Your payment is still processing. This page will update automatically once Pesapal confirms it.';
    }
  } catch (PesapalException $e) {
    $message = $e->getMessage();
  }
}

$icon = ['success' => '✅', 'pending' => '⏳', 'failed' => '❌', 'error' => '⚠️'][$status];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Status – NursesPro Academy</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= asset_v('css/style.css') ?>">
<style>
  body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--light-blue); }
  .status-card { background:#fff; border-radius:var(--radius-md); box-shadow:var(--shadow-md); padding:48px 40px; max-width:440px; text-align:center; }
  .status-icon { font-size:3.5rem; margin-bottom:16px; }
</style>
</head>
<body>
  <div class="status-card">
    <div class="status-icon"><?= $icon ?></div>
    <h2 style="color:var(--primary-blue);margin-bottom:12px;">
      <?= $status === 'success' ? 'Payment Successful' : ($status === 'pending' ? 'Payment Processing' : 'Payment Not Confirmed') ?>
    </h2>
    <p style="color:#5a6a7a;margin-bottom:28px;"><?= htmlspecialchars($message) ?></p>
    <a href="dashboard.php" class="btn btn-primary btn-full"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
  </div>
</body>
</html>
