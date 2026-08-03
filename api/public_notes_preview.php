<?php
/**
 * NursesPro Academy - Public PDF preview streaming
 * GET ?id=<note id> — no login required. Serves the same protected PDF the
 * logged-in viewer uses (never a raw uploads/ URL), but the client-side
 * viewer caps navigation at ~50% of pages for anonymous visitors and prompts
 * account creation to continue — see js/pdf-viewer.js `open(..., {preview:true})`.
 *
 * Note: true server-side page-count enforcement would require a PDF-manipulation
 * library (e.g. FPDI) to physically truncate arbitrary admin-uploaded PDFs, which
 * isn't available in this environment. The page cap here is a client UI
 * restriction, consistent with the rest of this app's PDF protections
 * (right-click/print/shortcut blocking) — a deterrent, not cryptographic DRM.
 */
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { http_response_code(400); exit('Missing note id.'); }

$stmt = db()->prepare('SELECT * FROM notes WHERE id = ?');
$stmt->execute([$id]);
$note = $stmt->fetch();
if (!$note) { http_response_code(404); exit('Note not found.'); }

$path = __DIR__ . '/../uploads/notes/' . $note['file_path'];
if (!is_file($path)) { http_response_code(404); exit('File not found.'); }

db()->prepare('UPDATE notes SET views = views + 1 WHERE id = ?')->execute([$id]);

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . basename($note['file_path']) . '"');
header('Cache-Control: private, no-store');
readfile($path);
