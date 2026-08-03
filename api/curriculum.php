<?php
/**
 * NursesPro Academy - Curriculum API
 * GET  ?course=&year=&semester=  -> list modules (public within the app; login required)
 * POST {action:'add'|'update'|'delete', ...}  -> superadmin only
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  require_login_api();
  $sql = 'SELECT m.id, c.name AS course, m.year, m.semester, m.code, m.title, m.sort_order
          FROM curriculum_modules m JOIN curriculum_courses c ON c.id = m.course_id';
  $where = [];
  $params = [];
  if (!empty($_GET['course']))   { $where[] = 'c.name = ?';   $params[] = $_GET['course']; }
  if (!empty($_GET['year']))     { $where[] = 'm.year = ?';   $params[] = $_GET['year']; }
  if (!empty($_GET['semester'])) { $where[] = 'm.semester = ?'; $params[] = $_GET['semester']; }
  if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
  $sql .= ' ORDER BY c.name, m.year, m.semester, m.sort_order';

  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  respond(['success' => true, 'modules' => $stmt->fetchAll(), 'courses' => db()->query('SELECT name FROM curriculum_courses ORDER BY name')->fetchAll(PDO::FETCH_COLUMN)]);
}

require_role_api(['superadmin']);
$data = json_input();
$action = $data['action'] ?? '';

if ($action === 'add' || $action === 'update') {
  $course = trim($data['course'] ?? '');
  $year = trim($data['year'] ?? '');
  $semester = trim($data['semester'] ?? '');
  $code = trim($data['code'] ?? '');
  $title = trim($data['title'] ?? '');
  if (!$course || !$year || !$semester || !$code || !$title) {
    respond(['success' => false, 'message' => 'Please fill in all fields.']);
  }

  $stmt = db()->prepare('SELECT id FROM curriculum_courses WHERE name = ?');
  $stmt->execute([$course]);
  $courseRow = $stmt->fetch();
  if (!$courseRow) {
    db()->prepare('INSERT INTO curriculum_courses (name) VALUES (?)')->execute([$course]);
    $courseId = db()->lastInsertId();
  } else {
    $courseId = $courseRow['id'];
  }

  if ($action === 'add') {
    $maxOrder = db()->prepare('SELECT COALESCE(MAX(sort_order),0) FROM curriculum_modules WHERE course_id=? AND year=? AND semester=?');
    $maxOrder->execute([$courseId, $year, $semester]);
    $sortOrder = (int)$maxOrder->fetchColumn() + 1;
    try {
      $ins = db()->prepare('INSERT INTO curriculum_modules (course_id, year, semester, code, title, sort_order) VALUES (?,?,?,?,?,?)');
      $ins->execute([$courseId, $year, $semester, $code, $title, $sortOrder]);
    } catch (PDOException $e) {
      respond(['success' => false, 'message' => 'A module with this code already exists for this course.']);
    }
  } else {
    $id = (int)($data['id'] ?? 0);
    if (!$id) respond(['success' => false, 'message' => 'Missing module id.']);
    try {
      $upd = db()->prepare('UPDATE curriculum_modules SET course_id=?, year=?, semester=?, code=?, title=? WHERE id=?');
      $upd->execute([$courseId, $year, $semester, $code, $title, $id]);
    } catch (PDOException $e) {
      respond(['success' => false, 'message' => 'A module with this code already exists for this course.']);
    }
  }
  respond(['success' => true]);
}

if ($action === 'delete') {
  $id = (int)($data['id'] ?? 0);
  if (!$id) respond(['success' => false, 'message' => 'Missing module id.']);
  db()->prepare('DELETE FROM curriculum_modules WHERE id=?')->execute([$id]);
  respond(['success' => true]);
}

respond(['success' => false, 'message' => 'Unknown action.'], 400);
