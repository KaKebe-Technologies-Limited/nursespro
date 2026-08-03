<?php
/**
 * NursesPro Academy - Session/auth helpers
 * Shared by both page guards (dashboard.php, admin-dashboard.php) and API endpoints.
 */

require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function current_user(): ?array {
  if (empty($_SESSION['user_id'])) return null;
  $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
  return $user ?: null;
}

function has_active_access(array $user): bool {
  if (in_array($user['role'], ['superadmin', 'tutor'], true)) return true;
  if (empty($user['access_expiry'])) return false;
  return strtotime($user['access_expiry']) > time();
}

// ── Page guards (redirect) ──────────────────────────────────────────────────
function require_login_page(): array {
  $user = current_user();
  if (!$user) {
    header('Location: index.php');
    exit;
  }
  return $user;
}

function require_role_page(array $roles): array {
  $user = require_login_page();
  if (!in_array($user['role'], $roles, true)) {
    header('Location: index.php');
    exit;
  }
  return $user;
}

// ── API guards (JSON) ────────────────────────────────────────────────────────
function json_input(): array {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function respond($data, int $code = 200): void {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

function require_login_api(): array {
  $user = current_user();
  if (!$user) respond(['success' => false, 'message' => 'Not authenticated.'], 401);
  return $user;
}

function require_role_api(array $roles): array {
  $user = require_login_api();
  if (!in_array($user['role'], $roles, true)) {
    respond(['success' => false, 'message' => 'Access denied.'], 403);
  }
  return $user;
}

function public_user(array $user): array {
  unset($user['password_hash']);
  return $user;
}

// ── Cache-busting for local static assets ────────────────────────────────────
// Appends ?v=<mtime> to css/js links so browsers pick up changes immediately
// instead of serving a stale cached copy after every edit.
function asset_v(string $path): string {
  $full = __DIR__ . '/../' . $path;
  $v = is_file($full) ? filemtime($full) : time();
  return $path . '?v=' . $v;
}
