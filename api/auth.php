<?php
/**
 * NursesPro Academy - Auth API
 * GET  ?action=me
 * POST {action: 'login'|'register'|'logout', ...}
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $method === 'GET' ? ($_GET['action'] ?? '') : (json_input()['action'] ?? '');

if ($method === 'GET' && $action === 'me') {
  $user = current_user();
  respond(['success' => true, 'user' => $user ? public_user($user) : null]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

$data = json_input();

if ($action === 'login') {
  $email = trim($data['email'] ?? '');
  $password = $data['password'] ?? '';
  $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
  $stmt->execute([strtolower($email)]);
  $user = $stmt->fetch();
  if (!$user || !password_verify($password, $user['password_hash'])) {
    respond(['success' => false, 'message' => 'Invalid email or password.']);
  }
  $_SESSION['user_id'] = $user['id'];
  respond(['success' => true, 'user' => public_user($user)]);
}

if ($action === 'register') {
  $email = strtolower(trim($data['email'] ?? ''));
  $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
  $stmt->execute([$email]);
  if ($stmt->fetch()) {
    respond(['success' => false, 'message' => 'An account with this email already exists.']);
  }

  $required = ['name', 'email', 'phone', 'regNumber', 'course', 'year', 'semester', 'institution', 'password'];
  foreach ($required as $f) {
    if (empty($data[$f])) respond(['success' => false, 'message' => 'Please fill in all required fields.']);
  }

  $ins = db()->prepare('INSERT INTO users (name,email,password_hash,phone,role,course,year,semester,institution,reg_number,access_expiry) VALUES (?,?,?,?,\'student\',?,?,?,?,?,NULL)');
  $ins->execute([
    strip_tags(trim($data['name'])), $email, password_hash($data['password'], PASSWORD_BCRYPT),
    trim($data['phone']), trim($data['course']), trim($data['year']), trim($data['semester']),
    strip_tags(trim($data['institution'])), strip_tags(trim($data['regNumber']))
  ]);
  $id = db()->lastInsertId();
  $_SESSION['user_id'] = $id;

  $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
  $stmt->execute([$id]);
  respond(['success' => true, 'user' => public_user($stmt->fetch())]);
}

if ($action === 'logout') {
  $_SESSION = [];
  session_destroy();
  respond(['success' => true]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
