<?php
/**
 * NursesPro Academy - My Notes API (personal student notes)
 * GET
 * POST {action:'create'|'update'|'delete', ...}
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$user = require_login_api();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $stmt = db()->prepare('SELECT * FROM student_notes WHERE user_id = ? ORDER BY updated_at DESC');
  $stmt->execute([$user['id']]);
  respond(['success' => true, 'notes' => $stmt->fetchAll()]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'create') {
  $title = trim($data['title'] ?? '');
  if (!$title) respond(['success' => false, 'message' => 'Please enter a title for your note.']);
  $category = trim($data['category'] ?? '') ?: 'General';
  $content = $data['content'] ?? '';

  $ins = db()->prepare('INSERT INTO student_notes (user_id, title, category, content) VALUES (?,?,?,?)');
  $ins->execute([$user['id'], $title, $category, $content]);
  respond(['success' => true, 'id' => db()->lastInsertId()]);
}

if ($action === 'update') {
  $id = (int)($data['id'] ?? 0);
  $title = trim($data['title'] ?? '');
  if (!$id || !$title) respond(['success' => false, 'message' => 'Please enter a title for your note.']);
  $category = trim($data['category'] ?? '') ?: 'General';
  $content = $data['content'] ?? '';

  $upd = db()->prepare('UPDATE student_notes SET title=?, category=?, content=? WHERE id=? AND user_id=?');
  $upd->execute([$title, $category, $content, $id, $user['id']]);
  respond(['success' => true]);
}

if ($action === 'delete') {
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing note id.']);
  db()->prepare('DELETE FROM student_notes WHERE id=? AND user_id=?')->execute([$id, $user['id']]);
  respond(['success' => true]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
