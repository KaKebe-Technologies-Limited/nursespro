<?php
/**
 * NursesPro Academy - Migration: topics & subtopics
 * Adds the Course/Module -> Topic -> Subtopic hierarchy on top of the existing
 * curriculum_modules table, and links notes to a topic/subtopic.
 *
 * Safe to re-run: every step checks whether it's already applied first, and
 * nothing here touches existing rows in users/notes/payments/etc.
 *
 * Run: php sql/migrate_topics.php
 */

require_once __DIR__ . '/../config/db.php';
$pdo = db();

function columnExists(PDO $pdo, string $table, string $column): bool {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
  $stmt->execute([$table, $column]);
  return (int)$stmt->fetchColumn() > 0;
}

function tableExists(PDO $pdo, string $table): bool {
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
  $stmt->execute([$table]);
  return (int)$stmt->fetchColumn() > 0;
}

echo "Migrating: topics & subtopics...\n";

if (!tableExists($pdo, 'topics')) {
  $pdo->exec("CREATE TABLE topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES curriculum_modules(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  echo "  created table: topics\n";
} else {
  echo "  table topics already exists, skipping\n";
}

if (!tableExists($pdo, 'subtopics')) {
  $pdo->exec("CREATE TABLE subtopics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    code VARCHAR(20) NULL,
    title VARCHAR(500) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  echo "  created table: subtopics\n";
} else {
  echo "  table subtopics already exists, skipping\n";
}

if (!columnExists($pdo, 'notes', 'topic_id')) {
  $pdo->exec("ALTER TABLE notes ADD COLUMN topic_id INT NULL AFTER module_id");
  $pdo->exec("ALTER TABLE notes ADD CONSTRAINT fk_notes_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE SET NULL");
  echo "  added column: notes.topic_id\n";
} else {
  echo "  notes.topic_id already exists, skipping\n";
}

if (!columnExists($pdo, 'notes', 'subtopic_id')) {
  $pdo->exec("ALTER TABLE notes ADD COLUMN subtopic_id INT NULL AFTER topic_id");
  $pdo->exec("ALTER TABLE notes ADD CONSTRAINT fk_notes_subtopic FOREIGN KEY (subtopic_id) REFERENCES subtopics(id) ON DELETE SET NULL");
  echo "  added column: notes.subtopic_id\n";
} else {
  echo "  notes.subtopic_id already exists, skipping\n";
}

echo "Done.\n";
