<?php
/**
 * NursesPro Academy - Payments API
 * GET                          -> own payments (student), or ALL payments — superadmin only.
 *                                 Tutors are intentionally excluded: they get their own
 *                                 (empty) payment list, same as any non-paying role, never
 *                                 other students' financial data.
 * POST {action:'initiate', phone, method}  -> simulated Mobile Money payment, persists to DB
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$user = require_login_api();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  if ($user['role'] === 'superadmin') {
    $stmt = db()->query('SELECT p.*, u.name AS student_name FROM payments p JOIN users u ON u.id = p.user_id ORDER BY p.paid_at DESC');
  } else {
    $stmt = db()->prepare('SELECT * FROM payments WHERE user_id = ? ORDER BY paid_at DESC');
    $stmt->execute([$user['id']]);
  }
  respond(['success' => true, 'payments' => $stmt->fetchAll()]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'initiate') {
  $phone = trim($data['phone'] ?? '');
  $methodName = trim($data['method'] ?? 'MTN');
  if (!preg_match('/^(07|08|03)\d{8}$/', preg_replace('/\s+/', '', $phone))) {
    respond(['success' => false, 'message' => 'Please enter a valid phone number.']);
  }

  $amount = 18500;
  $reference = 'REF' . time() . rand(100, 999);
  $expiry = date('Y-m-d H:i:s', strtotime('+6 months'));

  $ins = db()->prepare('INSERT INTO payments (user_id, amount, method, phone, reference, status, expiry_granted) VALUES (?,?,?,?,?,\'paid\',?)');
  $ins->execute([$user['id'], $amount, $methodName, $phone, $reference, $expiry]);
  db()->prepare('UPDATE users SET access_expiry=? WHERE id=?')->execute([$expiry, $user['id']]);

  respond(['success' => true, 'reference' => $reference, 'expiry' => $expiry]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
