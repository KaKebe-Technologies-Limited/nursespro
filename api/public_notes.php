<?php
/**
 * NursesPro Academy - Public Notes Catalog API
 * GET ?q=&module=CODE&topic=ID&subtopic=ID&year=&semester=&uploader=
 * No login required — powers the public notes.php browsing page. Never
 * exposes file_path; PDFs are only ever reachable via api/public_notes_preview.php
 * or (for logged-in, paid access) api/notes_stream.php.
 */
require_once __DIR__ . '/../config/db.php';

$sql = 'SELECT n.id, n.title, n.description, n.views, n.created_at,
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

header('Content-Type: application/json');
echo json_encode(['success' => true, 'notes' => $stmt->fetchAll()]);
