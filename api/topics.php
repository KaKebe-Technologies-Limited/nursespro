<?php
/**
 * NursesPro Academy - Topics & Subtopics API
 * GET  ?module=CODE   -> topics (with nested subtopics) for a module — public
 *                        (curriculum structure, not sensitive; powers both the
 *                        logged-in admin cascade and the public notes catalog)
 * POST {action:'add-topic'|'update-topic'|'delete-topic'
 *              |'add-subtopic'|'update-subtopic'|'delete-subtopic', ...}  -> admin/tutor
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $moduleCode = trim($_GET['module'] ?? '');
  if (!$moduleCode) respond(['success' => false, 'message' => 'Missing module.'], 400);

  $stmt = db()->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
  $stmt->execute([$moduleCode]);
  $module = $stmt->fetch();
  if (!$module) respond(['success' => true, 'topics' => []]);

  $stmt = db()->prepare('SELECT id, title, sort_order FROM topics WHERE module_id = ? ORDER BY sort_order, id');
  $stmt->execute([$module['id']]);
  $topics = $stmt->fetchAll();

  $subStmt = db()->prepare('SELECT id, code, title, sort_order FROM subtopics WHERE topic_id = ? ORDER BY sort_order, id');
  foreach ($topics as &$t) {
    $subStmt->execute([$t['id']]);
    $t['subtopics'] = $subStmt->fetchAll();
  }

  respond(['success' => true, 'topics' => $topics]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

require_role_api(['superadmin', 'tutor']);
$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'add-topic') {
  $moduleCode = trim($data['module'] ?? '');
  $title = trim($data['title'] ?? '');
  if (!$moduleCode || !$title) respond(['success' => false, 'message' => 'Please provide a module and title.']);

  $stmt = db()->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
  $stmt->execute([$moduleCode]);
  $module = $stmt->fetch();
  if (!$module) respond(['success' => false, 'message' => 'Unknown module.']);

  $maxOrder = db()->prepare('SELECT COALESCE(MAX(sort_order),0) FROM topics WHERE module_id = ?');
  $maxOrder->execute([$module['id']]);
  $order = (int)$maxOrder->fetchColumn() + 1;

  $ins = db()->prepare('INSERT INTO topics (module_id, title, sort_order) VALUES (?,?,?)');
  $ins->execute([$module['id'], $title, $order]);
  respond(['success' => true, 'id' => db()->lastInsertId()]);
}

if ($action === 'update-topic') {
  $id = (int)($data['id'] ?? 0);
  $title = trim($data['title'] ?? '');
  if (!$id || !$title) respond(['success' => false, 'message' => 'Please provide a title.']);
  db()->prepare('UPDATE topics SET title=? WHERE id=?')->execute([$title, $id]);
  respond(['success' => true]);
}

if ($action === 'delete-topic') {
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing topic id.']);
  db()->prepare('DELETE FROM topics WHERE id=?')->execute([$id]);
  respond(['success' => true]);
}

if ($action === 'add-subtopic') {
  $topicId = (int)($data['topic_id'] ?? 0);
  $title = trim($data['title'] ?? '');
  $code = trim($data['code'] ?? '') ?: null;
  if (!$topicId || !$title) respond(['success' => false, 'message' => 'Please provide a topic and title.']);

  $maxOrder = db()->prepare('SELECT COALESCE(MAX(sort_order),0) FROM subtopics WHERE topic_id = ?');
  $maxOrder->execute([$topicId]);
  $order = (int)$maxOrder->fetchColumn() + 1;

  $ins = db()->prepare('INSERT INTO subtopics (topic_id, code, title, sort_order) VALUES (?,?,?,?)');
  $ins->execute([$topicId, $code, $title, $order]);
  respond(['success' => true, 'id' => db()->lastInsertId()]);
}

if ($action === 'update-subtopic') {
  $id = (int)($data['id'] ?? 0);
  $title = trim($data['title'] ?? '');
  $code = trim($data['code'] ?? '') ?: null;
  if (!$id || !$title) respond(['success' => false, 'message' => 'Please provide a title.']);
  db()->prepare('UPDATE subtopics SET title=?, code=? WHERE id=?')->execute([$title, $code, $id]);
  respond(['success' => true]);
}

if ($action === 'delete-subtopic') {
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing subtopic id.']);
  db()->prepare('DELETE FROM subtopics WHERE id=?')->execute([$id]);
  respond(['success' => true]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
