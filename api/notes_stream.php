<?php
/**
 * NursesPro Academy - Secure PDF streaming
 * GET ?id=<note id> — only serves the file if the requester is logged in AND
 * has active access (superadmin/tutor, or a student with a valid access_expiry).
 * Direct requests to /uploads/notes/*.pdf are NOT how notes are meant to be viewed —
 * this endpoint is the real backend for the `/api/notes/:id/stream` the frontend
 * has always called.
 */
require_once __DIR__ . '/../includes/auth_guard.php';

$user = current_user();
if (!$user) { http_response_code(401); exit('Not authenticated.'); }
if (!has_active_access($user)) { http_response_code(403); exit('Your access has expired. Please renew.'); }

$id = (int)($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); exit('Missing note id.'); }

$stmt = db()->prepare('SELECT * FROM notes WHERE id = ?');
$stmt->execute([$id]);
$note = $stmt->fetch();
if (!$note) { http_response_code(404); exit('Note not found.'); }

$path = __DIR__ . '/../uploads/notes/' . $note['file_path'];
if (!is_file($path)) { http_response_code(404); exit('File not found.'); }

db()->prepare('UPDATE notes SET views = views + 1 WHERE id = ?')->execute([$id]);
db()->prepare('UPDATE users SET notes_viewed = notes_viewed + 1 WHERE id = ?')->execute([$user['id']]);

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . basename($note['file_path']) . '"');
header('Cache-Control: private, no-store');
readfile($path);
