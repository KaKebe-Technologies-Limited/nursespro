<?php
/**
 * NursesPro Academy - Staff (tutor/admin) accounts API (superadmin only)
 * GET
 * POST {action:'add'|'remove', ...}
 */
require_once __DIR__ . '/../includes/auth_guard.php';

require_role_api(['superadmin']);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $stmt = db()->query("SELECT id, name, email, role, created_at FROM users WHERE role IN ('tutor','superadmin') ORDER BY name");
  respond(['success' => true, 'users' => $stmt->fetchAll()]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'add') {
  $name = trim($data['name'] ?? '');
  $email = strtolower(trim($data['email'] ?? ''));
  $role = in_array($data['role'] ?? '', ['tutor', 'superadmin'], true) ? $data['role'] : 'tutor';
  $password = $data['password'] ?? '';
  if (!$name || !$email || strlen($password) < 4) {
    respond(['success' => false, 'message' => 'Please fill in all fields.']);
  }
  $stmt = db()->prepare('SELECT id FROM users WHERE email=?');
  $stmt->execute([$email]);
  if ($stmt->fetch()) respond(['success' => false, 'message' => 'An account with this email already exists.']);

  $ins = db()->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)');
  $ins->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), $role]);
  respond(['success' => true, 'message' => 'Account created.']);
}

if ($action === 'remove') {
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing user id.']);
  db()->prepare("DELETE FROM users WHERE id=? AND role IN ('tutor','superadmin')")->execute([$id]);
  respond(['success' => true, 'message' => 'User removed.']);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
