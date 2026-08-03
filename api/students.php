<?php
/**
 * NursesPro Academy - Students management API (admin/tutor)
 * GET  ?search=&course=
 * POST {action:'grant-access'|'remove'|'view', id}
 */
require_once __DIR__ . '/../includes/auth_guard.php';

require_role_api(['superadmin']);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $sql = "SELECT id, name, email, course, year, semester, access_expiry, reg_number, created_at FROM users WHERE role='student'";
  $params = [];
  if (!empty($_GET['search'])) {
    $sql .= ' AND (name LIKE ? OR email LIKE ? OR reg_number LIKE ?)';
    $like = '%' . $_GET['search'] . '%';
    array_push($params, $like, $like, $like);
  }
  if (!empty($_GET['course'])) { $sql .= ' AND course = ?'; $params[] = $_GET['course']; }
  $sql .= ' ORDER BY name';
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  respond(['success' => true, 'students' => $stmt->fetchAll()]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

$data = json_input();
$action = $data['action'] ?? '';
$id = (int)($data['id'] ?? 0);
if (!$id) respond(['success' => false, 'message' => 'Missing student id.']);

if ($action === 'grant-access') {
  $expiry = date('Y-m-d H:i:s', strtotime('+6 months'));
  db()->prepare('UPDATE users SET access_expiry=? WHERE id=? AND role="student"')->execute([$expiry, $id]);
  respond(['success' => true, 'message' => 'Access granted for 6 months.']);
}

if ($action === 'remove') {
  db()->prepare('DELETE FROM users WHERE id=? AND role="student"')->execute([$id]);
  respond(['success' => true, 'message' => 'Student removed.']);
}

if ($action === 'view') {
  $stmt = db()->prepare('SELECT name, email, course, year, semester FROM users WHERE id=?');
  $stmt->execute([$id]);
  $s = $stmt->fetch();
  if (!$s) respond(['success' => false, 'message' => 'Student not found.']);
  respond(['success' => true, 'student' => $s]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
