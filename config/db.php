<?php
/**
 * NursesPro Academy - Database connection
 *
 * Admin panel login (not DB credentials — for admin-dashboard.php):
 *   Superadmin: admin@nursespro.ac.ug / Admin1234
 *   Tutor:      john@demo.com / Tutor1234
 * (Same accounts shown in the login modal's "Demo Accounts" list — not secret.)
 */

const DB_HOST = 'localhost';
const DB_NAME = 'nursespro';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $pdo = new PDO(
      'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
      DB_USER,
      DB_PASS,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]
    );
  }
  return $pdo;
}
