<?php
/**
 * NursesPro Academy - Database seeder
 * Run once: php sql/seed.php
 * Safe to re-run — clears and reseeds all tables.
 */

require_once __DIR__ . '/../config/db.php';
$pdo = db();

echo "Seeding NursesPro Academy database...\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['student_notes','payments','classes','notes','curriculum_modules','curriculum_courses','users'] as $t) {
  $pdo->exec("TRUNCATE TABLE $t");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// ── Curriculum ────────────────────────────────────────────────────────────────
$courses = ['Diploma in Nursing (Extension)', 'Diploma in Nursing (Direct)', 'Diploma in Midwifery (Extension)', 'Diploma in Midwifery (Direct)'];
$courseIds = [];
$insCourse = $pdo->prepare('INSERT INTO curriculum_courses (name) VALUES (?)');
foreach ($courses as $c) { $insCourse->execute([$c]); $courseIds[$c] = $pdo->lastInsertId(); }

$dneModules = [
  ['Year 1', 'Semester 1', 'DNE 111', 'Foundations of Nursing III', 1],
  ['Year 1', 'Semester 1', 'DNE 112', 'Medical Nursing III', 2],
  ['Year 1', 'Semester 1', 'DNE 113', 'Surgical Nursing III & Paediatric Nursing', 3],
  ['Year 1', 'Semester 1', 'DNE 114', 'Mental Health Nursing II & Pharmacology II', 4],
  ['Year 1', 'Semester 1', 'DNE 115', 'Practicals', 5],
  ['Year 1', 'Semester 2', 'DNE 121', 'Primary Health Care (PHC) & Community Based Health Care (CBHC)', 1],
  ['Year 1', 'Semester 2', 'DNE 122', 'Applied Research and Teaching Methodology', 2],
  ['Year 1', 'Semester 2', 'DNE 123', 'Palliative Care Nursing', 3],
  ['Year 1', 'Semester 2', 'DNE 124', 'Disaster Management & Occupational Health and Safety', 4],
  ['Year 1', 'Semester 2', 'DNE 125', 'Practicals', 5],
  ['Year 2', 'Semester 1', 'DNE 211', 'Paediatric Nursing III', 1],
  ['Year 2', 'Semester 1', 'DNE 212', 'Gynaecology Nursing (II) & Reproductive Health Nursing (II)', 2],
  ['Year 2', 'Semester 1', 'DNE 213', 'Health Service Management & Entrepreneurship', 3],
  ['Year 2', 'Semester 1', 'DNE 214', 'Practical', 4],
];
$moduleIds = [];
$insMod = $pdo->prepare('INSERT INTO curriculum_modules (course_id, year, semester, code, title, sort_order) VALUES (?,?,?,?,?,?)');
foreach ($dneModules as [$year, $sem, $code, $title, $order]) {
  $insMod->execute([$courseIds['Diploma in Nursing (Extension)'], $year, $sem, $code, $title, $order]);
  $moduleIds[$code] = $pdo->lastInsertId();
}
echo "  curriculum: " . count($courses) . " courses, " . count($dneModules) . " modules\n";

// ── Users ─────────────────────────────────────────────────────────────────────
function months(int $n): string { return date('Y-m-d H:i:s', strtotime("+$n months")); }
function daysAgo(int $n): string { return date('Y-m-d H:i:s', strtotime("-$n days")); }

$users = [
  ['Admin User', 'admin@nursespro.ac.ug', 'Admin1234', '0392972444', 'superadmin', 'Diploma in Nursing (Direct)', 'Year 2', 'Semester 1', 'Mulago School of Nursing', 'ADM001', months(6), 42, 18],
  ['Sarah Nakato', 'sarah@demo.com', 'Student1234', '0701234567', 'student', 'Diploma in Midwifery (Direct)', 'Year 1', 'Semester 2', 'Mulago School of Nursing', 'MID22001', months(3), 14, 7],
  ['John Okello', 'john@demo.com', 'Tutor1234', '0712345678', 'tutor', 'Diploma in Nursing (Extension)', 'Year 3', 'Semester 1', 'Kampala International', 'NUR21009', months(6), 28, 12],
  ['Grace Apio', 'grace@demo.com', 'Grace1234', '0789012345', 'student', 'Diploma in Nursing (Direct)', 'Year 2', 'Semester 2', 'Makerere University', 'NUR23007', daysAgo(200), 5, 2],
  ['Peter Musoke', 'peter@demo.com', 'Peter1234', '0700333444', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 1', 'Mulago School of Nursing', 'DNE24001', months(2), 9, 4],
  ['Immaculate Nabirye', 'immaculate@demo.com', 'Immy1234', '0701444555', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 1', 'Mulago School of Nursing', 'DNE24002', months(3), 11, 6],
  ['Brian Ssekandi', 'brian@demo.com', 'Brian1234', '0702555666', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 2', 'Kampala International', 'DNE24003', months(1), 6, 3],
  ['Diana Achieng', 'diana@demo.com', 'Diana1234', '0703666777', 'student', 'Diploma in Nursing (Extension)', 'Year 2', 'Semester 1', 'Mulago School of Nursing', 'DNE23004', months(2), 17, 9],
  ['Moses Kato', 'moses@demo.com', 'Moses1234', '0704777888', 'student', 'Diploma in Nursing (Extension)', 'Year 2', 'Semester 1', 'Makerere University', 'DNE23005', daysAgo(45), 3, 1],
  ['Ritah Nansubuga', 'ritah@demo.com', 'Ritah1234', '0705888999', 'student', 'Diploma in Nursing (Extension)', 'Year 1', 'Semester 2', 'Mulago School of Nursing', 'DNE24006', months(3), 8, 5],
  ['Emmanuel Byaruhanga', 'emmanuel@demo.com', 'Emma1234', '0706999000', 'student', 'Diploma in Nursing (Direct)', 'Year 1', 'Semester 1', 'Kampala International', 'NUR24010', months(1), 4, 2],
];

$userIds = [];
$insUser = $pdo->prepare('INSERT INTO users (name,email,password_hash,phone,role,course,year,semester,institution,reg_number,access_expiry,notes_viewed,classes_attended) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
foreach ($users as [$name,$email,$pw,$phone,$role,$course,$year,$sem,$inst,$reg,$expiry,$nv,$ca]) {
  $insUser->execute([$name,$email,password_hash($pw, PASSWORD_BCRYPT),$phone,$role,$course,$year,$sem,$inst,$reg,$expiry,$nv,$ca]);
  $userIds[$email] = $pdo->lastInsertId();
}
echo "  users: " . count($users) . " (passwords bcrypt-hashed)\n";

// ── Notes (real PDFs, generated into uploads/notes/) ────────────────────────────
$notes = [
  ['dne111_foundations_of_nursing.pdf', 'Foundations of Nursing Practice III', 'DNE 111', 'Core nursing principles, the nursing process (ADPIE), and professional ethical practice.', 342],
  ['dne112_medical_nursing.pdf', 'Medical Nursing III – Chronic Disease Management', 'DNE 112', 'Nursing management of hypertension, diabetes, cardiovascular and respiratory conditions.', 289],
  ['dne113_surgical_paediatric_nursing.pdf', 'Surgical Nursing III & Paediatric Nursing', 'DNE 113', 'Pre/post-operative care and the principles of paediatric nursing.', 176],
  ['dne114_pharmacology_drug_calculations.pdf', 'Pharmacology II – Drug Calculations & Dosage', 'DNE 114', 'Drug dosage formulas, IV drip rate calculations, and medication safety.', 210],
  ['dne121_phc_cbhc.pdf', 'Primary Health Care & Community-Based Health Care', 'DNE 121', 'PHC principles and community-based health care delivery in Uganda.', 154],
  ['dne122_applied_research_teaching_methodology.pdf', 'Applied Research Methods for Nursing', 'DNE 122', 'Research process, evidence-based practice, and basic teaching methodology.', 198],
  ['dne123_palliative_care_nursing.pdf', 'Palliative Care Nursing – End of Life Care', 'DNE 123', 'Symptom management and holistic care for terminally ill patients.', 132],
  ['dne211_paediatric_nursing_iii.pdf', 'Paediatric Nursing III – Advanced Child Care', 'DNE 211', 'IMCI assessment, nutrition/growth monitoring, and common childhood illnesses.', 167],
  ['dne212_gynaecology_reproductive_health.pdf', 'Gynaecology & Reproductive Health Nursing', 'DNE 212', 'Gynaecological conditions and reproductive health nursing care.', 141],
  ['dne213_health_service_management.pdf', 'Health Service Management & Entrepreneurship', 'DNE 213', 'Health service leadership, management principles, and entrepreneurship.', 98],
];
$insNote = $pdo->prepare('INSERT INTO notes (module_id, title, description, file_path, views, uploaded_by) VALUES (?,?,?,?,?,?)');
foreach ($notes as [$file, $title, $moduleCode, $desc, $views]) {
  $insNote->execute([$moduleIds[$moduleCode], $title, $desc, $file, $views, $userIds['admin@nursespro.ac.ug']]);
}
echo "  notes: " . count($notes) . "\n";

// ── Classes ────────────────────────────────────────────────────────────────────
$classes = [
  ['Medical Nursing III – Cardiac Case Review', 'DNE 112', 'Dr. Sarah Namukasa', '2026-08-05', '6:00 PM – 8:00 PM', 'upcoming', 'https://t.me/nursespro_dne112'],
  ['Surgical & Paediatric Nursing Masterclass', 'DNE 113', 'Ms. Grace Atim', '2026-08-03', '7:00 PM – 9:00 PM', 'live', 'https://t.me/nursespro_dne113'],
  ['Pharmacology II – Drug Calculations Workshop', 'DNE 114', 'Mr. Joseph Otim', '2026-07-28', '5:00 PM – 7:00 PM', 'completed', null],
  ['PHC & Community-Based Health Care Seminar', 'DNE 121', 'Dr. Peter Waiswa', '2026-08-08', '6:30 PM – 8:30 PM', 'upcoming', 'https://t.me/nursespro_dne121'],
];
$insClass = $pdo->prepare('INSERT INTO classes (module_id, title, tutor_name, class_date, class_time, status, telegram_link) VALUES (?,?,?,?,?,?,?)');
foreach ($classes as [$title, $moduleCode, $tutor, $date, $time, $status, $tg]) {
  $insClass->execute([$moduleIds[$moduleCode], $title, $tutor, $date, $time, $status, $tg]);
}
echo "  classes: " . count($classes) . "\n";

// ── Payments (for every student with an access_expiry) ──────────────────────────
$insPay = $pdo->prepare('INSERT INTO payments (user_id, amount, method, phone, reference, status, paid_at, expiry_granted) VALUES (?,?,?,?,?,?,?,?)');
$paymentCount = 0;
foreach ($users as [$name,$email,$pw,$phone,$role,$course,$year,$sem,$inst,$reg,$expiry,$nv,$ca]) {
  if ($role === 'superadmin' || $role === 'tutor') continue;
  $method = ['MTN', 'Airtel'][array_rand(['MTN', 'Airtel'])];
  $paidAt = date('Y-m-d H:i:s', strtotime($expiry . ' -6 months'));
  $insPay->execute([$userIds[$email], 18500, $method, $phone, 'REF' . strtoupper(substr(md5($email . '1'), 0, 10)), 'paid', $paidAt, $expiry]);
  $paymentCount++;
}
// Give Sarah a second, older payment to show renewal history
$insPay->execute([$userIds['sarah@demo.com'], 18500, 'MTN', '0701234567', 'REF' . strtoupper(substr(md5('sarah-renewal'), 0, 10)), 'paid', daysAgo(140), daysAgo(50)]);
$paymentCount++;
echo "  payments: $paymentCount\n";

echo "Done.\n";
