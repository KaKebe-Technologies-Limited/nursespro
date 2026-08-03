<?php
/**
 * NursesPro Academy - Profile API
 * POST {action: 'update-academic'|'change-password', ...}
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$user = require_login_api();
$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'update-academic') {
  $course = trim($data['course'] ?? '');
  $year = trim($data['year'] ?? '');
  $semester = trim($data['semester'] ?? '');
  if (!$course || !$year || !$semester) respond(['success' => false, 'message' => 'Please fill in all fields.']);

  $stmt = db()->prepare('UPDATE users SET course=?, year=?, semester=? WHERE id=?');
  $stmt->execute([$course, $year, $semester, $user['id']]);

  $stmt = db()->prepare('SELECT * FROM users WHERE id=?');
  $stmt->execute([$user['id']]);
  respond(['success' => true, 'user' => public_user($stmt->fetch())]);
}

if ($action === 'change-password') {
  $current = $data['currentPassword'] ?? '';
  $new = $data['newPassword'] ?? '';
  if (!password_verify($current, $user['password_hash'])) {
    respond(['success' => false, 'message' => 'Current password is incorrect.']);
  }
  if (strlen($new) < 8) {
    respond(['success' => false, 'message' => 'New password must be at least 8 characters.']);
  }
  $stmt = db()->prepare('UPDATE users SET password_hash=? WHERE id=?');
  $stmt->execute([password_hash($new, PASSWORD_BCRYPT), $user['id']]);
  respond(['success' => true]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
