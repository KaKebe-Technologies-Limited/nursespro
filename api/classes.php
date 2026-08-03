<?php
/**
 * NursesPro Academy - Classes API
 * GET  ?module=CODE   -> list classes (telegram_link only included if requester has active access)
 * POST {action:'schedule'|'update'|'delete', ...}  -> admin/tutor
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $user = require_login_api();
  $sql = 'SELECT c.id, c.title, c.tutor_name, c.class_date, c.class_time, c.status, c.telegram_link, m.code AS module
          FROM classes c JOIN curriculum_modules m ON m.id = c.module_id';
  $params = [];
  if (!empty($_GET['module'])) { $sql .= ' WHERE m.code = ?'; $params[] = $_GET['module']; }
  $sql .= ' ORDER BY c.class_date DESC';
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  $classes = $stmt->fetchAll();

  $hasAccess = has_active_access($user);
  foreach ($classes as &$c) {
    if (!$hasAccess) $c['telegram_link'] = null;
  }
  respond(['success' => true, 'classes' => $classes, 'hasAccess' => $hasAccess]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

require_role_api(['superadmin', 'tutor']);
$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'schedule') {
  $title = trim($data['title'] ?? '');
  $moduleCode = trim($data['module'] ?? '');
  $tutor = trim($data['tutor'] ?? '');
  $date = trim($data['date'] ?? '');
  $time = trim($data['time'] ?? '');
  $status = trim($data['status'] ?? 'upcoming');
  $telegram = trim($data['telegram'] ?? '') ?: null;

  if (!$title || !$moduleCode || !$tutor || !$date || !$time) {
    respond(['success' => false, 'message' => 'Please fill in all required fields.']);
  }
  $stmt = db()->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
  $stmt->execute([$moduleCode]);
  $module = $stmt->fetch();
  if (!$module) respond(['success' => false, 'message' => 'Unknown module.']);

  $ins = db()->prepare('INSERT INTO classes (module_id, title, tutor_name, class_date, class_time, status, telegram_link) VALUES (?,?,?,?,?,?,?)');
  $ins->execute([$module['id'], $title, $tutor, $date, $time, $status, $telegram]);
  respond(['success' => true, 'message' => 'Class scheduled! Students will see it under ' . $moduleCode . '.']);
}

if ($action === 'update') {
  $id = (int)($data['id'] ?? 0);
  $title = trim($data['title'] ?? '');
  $moduleCode = trim($data['module'] ?? '');
  $tutor = trim($data['tutor'] ?? '');
  $date = trim($data['date'] ?? '');
  $time = trim($data['time'] ?? '');
  $status = trim($data['status'] ?? 'upcoming');
  $telegram = trim($data['telegram'] ?? '') ?: null;

  if (!$id || !$title || !$moduleCode || !$tutor || !$date || !$time) {
    respond(['success' => false, 'message' => 'Please fill in all required fields.']);
  }
  $stmt = db()->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
  $stmt->execute([$moduleCode]);
  $module = $stmt->fetch();
  if (!$module) respond(['success' => false, 'message' => 'Unknown module.']);

  $upd = db()->prepare('UPDATE classes SET module_id=?, title=?, tutor_name=?, class_date=?, class_time=?, status=?, telegram_link=? WHERE id=?');
  $upd->execute([$module['id'], $title, $tutor, $date, $time, $status, $telegram, $id]);
  respond(['success' => true, 'message' => 'Class updated.']);
}

if ($action === 'delete') {
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing class id.']);
  db()->prepare('DELETE FROM classes WHERE id=?')->execute([$id]);
  respond(['success' => true, 'message' => 'Class deleted.']);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
