<?php
/**
 * NursesPro Academy - Notes API
 * GET  ?q=&module=CODE&topic=ID&subtopic=ID&year=&semester=&uploader=
 *      -> search/list notes (any logged-in user)
 * POST multipart {action:'upload', title, module, topic?, subtopic?, description, file}      -> admin/tutor
 * POST multipart {action:'update', id, title, module, topic?, subtopic?, description, file?}  -> admin/tutor
 * POST json      {action:'delete', id}                                                        -> admin/tutor
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$method = $_SERVER['REQUEST_METHOD'];
$uploadDir = __DIR__ . '/../uploads/notes/';

const NOTES_SELECT = 'SELECT n.id, n.title, n.description, n.views, n.created_at,
       m.code AS module, m.title AS module_title, m.year, m.semester,
       c.name AS course,
       t.id AS topic_id, t.title AS topic_title,
       s.id AS subtopic_id, s.code AS subtopic_code, s.title AS subtopic_title,
       u.name AS uploader_name
FROM notes n
JOIN curriculum_modules m ON m.id = n.module_id
JOIN curriculum_courses c ON c.id = m.course_id
LEFT JOIN topics t ON t.id = n.topic_id
LEFT JOIN subtopics s ON s.id = n.subtopic_id
LEFT JOIN users u ON u.id = n.uploaded_by';

if ($method === 'GET') {
  require_login_api();

  $sql = NOTES_SELECT;
  $where = [];
  $params = [];

  if (!empty($_GET['q'])) {
    $where[] = '(n.title LIKE ? OR n.description LIKE ? OR t.title LIKE ? OR s.title LIKE ? OR m.title LIKE ? OR u.name LIKE ?)';
    $like = '%' . $_GET['q'] . '%';
    array_push($params, $like, $like, $like, $like, $like, $like);
  }
  if (!empty($_GET['module']))   { $where[] = 'm.code = ?';    $params[] = $_GET['module']; }
  if (!empty($_GET['topic']))    { $where[] = 'n.topic_id = ?';    $params[] = (int)$_GET['topic']; }
  if (!empty($_GET['subtopic'])) { $where[] = 'n.subtopic_id = ?'; $params[] = (int)$_GET['subtopic']; }
  if (!empty($_GET['year']))     { $where[] = 'm.year = ?';     $params[] = $_GET['year']; }
  if (!empty($_GET['semester'])) { $where[] = 'm.semester = ?'; $params[] = $_GET['semester']; }
  if (!empty($_GET['uploader'])) { $where[] = 'u.name LIKE ?';  $params[] = '%' . $_GET['uploader'] . '%'; }

  if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
  $sql .= ' ORDER BY n.created_at DESC';

  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  respond(['success' => true, 'notes' => $stmt->fetchAll()]);
}

if ($method !== 'POST') respond(['success' => false, 'message' => 'Invalid request.'], 405);

$isMultipart = str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data');

function resolveTopicSubtopic(int $moduleId, ?string $topicId, ?string $subtopicId): array {
  $topic = null;
  $subtopic = null;
  if (!empty($topicId)) {
    $stmt = db()->prepare('SELECT id FROM topics WHERE id = ? AND module_id = ?');
    $stmt->execute([(int)$topicId, $moduleId]);
    $topic = $stmt->fetchColumn() ?: null;
  }
  if (!empty($subtopicId) && $topic) {
    $stmt = db()->prepare('SELECT id FROM subtopics WHERE id = ? AND topic_id = ?');
    $stmt->execute([(int)$subtopicId, $topic]);
    $subtopic = $stmt->fetchColumn() ?: null;
  }
  return [$topic, $subtopic];
}

if ($isMultipart && ($_POST['action'] ?? '') === 'upload') {
  $user = require_role_api(['superadmin', 'tutor']);

  $title = trim($_POST['title'] ?? '');
  $moduleCode = trim($_POST['module'] ?? '');
  $description = trim($_POST['description'] ?? '');
  if (!$title || !$moduleCode) respond(['success' => false, 'message' => 'Please fill in the title and select a module.']);

  $stmt = db()->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
  $stmt->execute([$moduleCode]);
  $module = $stmt->fetch();
  if (!$module) respond(['success' => false, 'message' => 'Unknown module.']);

  [$topicId, $subtopicId] = resolveTopicSubtopic($module['id'], $_POST['topic'] ?? null, $_POST['subtopic'] ?? null);

  if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    respond(['success' => false, 'message' => 'Please attach a PDF file.']);
  }
  $file = $_FILES['file'];
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime = finfo_file($finfo, $file['tmp_name']);
  finfo_close($finfo);
  if ($mime !== 'application/pdf') respond(['success' => false, 'message' => 'Only PDF files are allowed.']);
  if ($file['size'] > 50 * 1024 * 1024) respond(['success' => false, 'message' => 'File size must be under 50MB.']);

  if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
  $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
  $fileName = $safeName . '_' . time() . '.pdf';
  if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
    respond(['success' => false, 'message' => 'Failed to save the uploaded file.']);
  }

  $ins = db()->prepare('INSERT INTO notes (module_id, topic_id, subtopic_id, title, description, file_path, uploaded_by) VALUES (?,?,?,?,?,?,?)');
  $ins->execute([$module['id'], $topicId, $subtopicId, $title, $description, $fileName, $user['id']]);
  respond(['success' => true, 'message' => 'Note published successfully! Students will see it under ' . $moduleCode . '.']);
}

if ($isMultipart && ($_POST['action'] ?? '') === 'update') {
  require_role_api(['superadmin', 'tutor']);

  $id = (int)($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $moduleCode = trim($_POST['module'] ?? '');
  $description = trim($_POST['description'] ?? '');
  if (!$id || !$title || !$moduleCode) respond(['success' => false, 'message' => 'Please fill in the title and select a module.']);

  $stmt = db()->prepare('SELECT * FROM notes WHERE id = ?');
  $stmt->execute([$id]);
  $existing = $stmt->fetch();
  if (!$existing) respond(['success' => false, 'message' => 'Note not found.']);

  $stmt = db()->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
  $stmt->execute([$moduleCode]);
  $module = $stmt->fetch();
  if (!$module) respond(['success' => false, 'message' => 'Unknown module.']);

  [$topicId, $subtopicId] = resolveTopicSubtopic($module['id'], $_POST['topic'] ?? null, $_POST['subtopic'] ?? null);

  $fileName = $existing['file_path'];
  if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['file'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mime !== 'application/pdf') respond(['success' => false, 'message' => 'Only PDF files are allowed.']);
    if ($file['size'] > 50 * 1024 * 1024) respond(['success' => false, 'message' => 'File size must be under 50MB.']);

    $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $newFileName = $safeName . '_' . time() . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
      respond(['success' => false, 'message' => 'Failed to save the uploaded file.']);
    }
    if ($existing['file_path'] && is_file($uploadDir . $existing['file_path'])) {
      unlink($uploadDir . $existing['file_path']);
    }
    $fileName = $newFileName;
  }

  $upd = db()->prepare('UPDATE notes SET title=?, description=?, module_id=?, topic_id=?, subtopic_id=?, file_path=? WHERE id=?');
  $upd->execute([$title, $description, $module['id'], $topicId, $subtopicId, $fileName, $id]);
  respond(['success' => true, 'message' => 'Note updated.']);
}

$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'delete') {
  require_role_api(['superadmin', 'tutor']);
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing note id.']);

  $stmt = db()->prepare('SELECT file_path FROM notes WHERE id=?');
  $stmt->execute([$id]);
  $note = $stmt->fetch();
  db()->prepare('DELETE FROM notes WHERE id=?')->execute([$id]);
  if ($note && $note['file_path'] && is_file($uploadDir . $note['file_path'])) {
    unlink($uploadDir . $note['file_path']);
  }
  respond(['success' => true, 'message' => 'Note deleted.']);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
