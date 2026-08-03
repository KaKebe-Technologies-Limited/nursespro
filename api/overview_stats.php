<?php
/**
 * NursesPro Academy - Overview stats API
 * Aggregate counts only (no raw student PII) so both superadmin and tutor
 * can see the dashboard overview without exposing the full student roster,
 * which is restricted to superadmin via api/students.php.
 *
 * `revenue` is superadmin-only — tutors must not see money moved by students
 * (matches the same restriction on api/payments.php).
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$user = require_role_api(['superadmin', 'tutor']);

$totalStudents  = (int) db()->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$activeStudents = (int) db()->query("SELECT COUNT(*) FROM users WHERE role='student' AND access_expiry IS NOT NULL AND access_expiry > NOW()")->fetchColumn();
$notesCount     = (int) db()->query("SELECT COUNT(*) FROM notes")->fetchColumn();

$response = [
  'success' => true,
  'totalStudents' => $totalStudents,
  'activeStudents' => $activeStudents,
  'notesCount' => $notesCount,
];

if ($user['role'] === 'superadmin') {
  $response['revenue'] = (float) db()->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid'")->fetchColumn();
}

respond($response);
