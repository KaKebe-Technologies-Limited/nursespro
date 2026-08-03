<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/config/pesapal.php';
$user = require_role_page(['superadmin', 'tutor']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard – NursesPro Academy</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= asset_v('css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/dashboard.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/admin.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/responsive.css') ?>">
</head>
<body>
<div class="dashboard-wrapper">

  <!-- ── Admin Sidebar ──────────────────────────────────────── -->
  <aside class="sidebar admin-sidebar" id="sidebar" role="navigation" aria-label="Admin navigation">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <i class="fas fa-graduation-cap" style="color:var(--accent-gold);font-size:1.3rem;" aria-hidden="true"></i>
        NursesPro <span class="logo-badge">ADMIN</span>
      </div>
      <div class="sidebar-user">
        <div class="sidebar-avatar" data-user-initials aria-hidden="true">AD</div>
        <div class="sidebar-user-info">
          <h4 data-user-name>Admin</h4>
          <p data-user-role>Super Admin</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Overview</div>
      <a href="#" class="sidebar-link active" data-admin-page="overview">
        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard
      </a>
      <div class="nav-section-label">Management</div>
      <a href="#" class="sidebar-link superadmin-only" data-admin-page="students">
        <i class="fas fa-users" aria-hidden="true"></i> Students
      </a>
      <a href="#" class="sidebar-link" data-admin-page="notes">
        <i class="fas fa-file-pdf" aria-hidden="true"></i> Study Notes
      </a>
      <a href="#" class="sidebar-link" data-admin-page="classes">
        <i class="fab fa-telegram-plane" aria-hidden="true"></i> Live Classes
      </a>
      <a href="#" class="sidebar-link superadmin-only" data-admin-page="transactions">
        <i class="fas fa-money-bill-wave" aria-hidden="true"></i> Transactions
      </a>
      <a href="#" class="sidebar-link superadmin-only" data-admin-page="curriculum">
        <i class="fas fa-layer-group" aria-hidden="true"></i> Curriculum
      </a>
      <div class="nav-section-label">Administration</div>
      <a href="#" class="sidebar-link superadmin-only" data-admin-page="users">
        <i class="fas fa-user-shield" aria-hidden="true"></i> User Management
      </a>
      <a href="#" class="sidebar-link superadmin-only" data-admin-page="reports">
        <i class="fas fa-chart-bar" aria-hidden="true"></i> Reports
      </a>
      <a href="#" class="sidebar-link superadmin-only" data-admin-page="settings">
        <i class="fas fa-cog" aria-hidden="true"></i> Settings
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="dashboard.php" class="sidebar-link" style="color:rgba(255,255,255,0.6);font-size:0.85rem;">
        <i class="fas fa-user-graduate" aria-hidden="true"></i> Student View
      </a>
      <button class="sidebar-logout" id="logoutBtn">
        <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Sign Out
      </button>
    </div>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ── Main Content ────────────────────────────────────────── -->
  <div class="main-content">
    <header class="topbar" role="banner">
      <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
          <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <h1 class="page-title" id="pageTitle">Dashboard Overview</h1>
      </div>
      <div class="topbar-right">
        <button class="notif-btn" aria-label="Notifications"><i class="fas fa-bell" aria-hidden="true"></i></button>
        <div class="topbar-user">
          <div class="topbar-avatar" data-user-initials aria-hidden="true">AD</div>
          <span class="topbar-user-name" data-user-name>Admin</span>
          <span class="admin-badge" style="margin-left:4px;">Admin</span>
        </div>
      </div>
    </header>

    <main class="page-content" role="main">

      <!-- ══ ADMIN PAGE: OVERVIEW ══ -->
      <section class="admin-page-section" id="adminPage-overview">
        <div class="admin-stats-grid">
          <div class="admin-stat-card blue">
            <div class="admin-stat-label"><i class="fas fa-users" aria-hidden="true"></i> Total Students</div>
            <div class="admin-stat-value" id="adminStatStudents">—</div>
            <div class="admin-stat-change up"><i class="fas fa-arrow-up" aria-hidden="true"></i> Active enrollments</div>
          </div>
          <div class="admin-stat-card green">
            <div class="admin-stat-label"><i class="fas fa-check-circle" aria-hidden="true"></i> Active Subscriptions</div>
            <div class="admin-stat-value" id="adminStatActive">—</div>
            <div class="admin-stat-change up"><i class="fas fa-arrow-up" aria-hidden="true"></i> Paid & active</div>
          </div>
          <div class="admin-stat-card gold superadmin-only">
            <div class="admin-stat-label"><i class="fas fa-money-bill" aria-hidden="true"></i> Total Revenue</div>
            <div class="admin-stat-value" id="adminStatRevenue" style="font-size:1.3rem;">—</div>
            <div class="admin-stat-change up"><i class="fas fa-arrow-up" aria-hidden="true"></i> All time</div>
          </div>
          <div class="admin-stat-card orange">
            <div class="admin-stat-label"><i class="fas fa-file-pdf" aria-hidden="true"></i> Published Notes</div>
            <div class="admin-stat-value" id="adminStatNotes">—</div>
            <div class="admin-stat-change up"><i class="fas fa-arrow-up" aria-hidden="true"></i> Available to students</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;" class="content-grid superadmin-only">
          <div class="card">
            <div class="card-header">
              <h3><i class="fas fa-chart-line" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Revenue Overview</h3>
              <a href="#" data-admin-page="reports">View Report</a>
            </div>
            <div class="card-body">
              <div class="chart-placeholder"><i class="fas fa-chart-bar" aria-hidden="true"></i> Revenue chart – Connect analytics backend</div>
            </div>
          </div>
          <div class="card">
            <div class="card-header">
              <h3><i class="fas fa-clock" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Recent Payments</h3>
              <a href="#" data-admin-page="transactions">View All</a>
            </div>
            <div class="card-body" id="recentPaymentsList">
              <div class="activity-list" id="overviewPaymentsList"></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3><i class="fas fa-bolt" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Quick Actions</h3>
          </div>
          <div class="card-body" style="display:flex;gap:16px;flex-wrap:wrap;">
            <button class="btn btn-primary" onclick="openModal('uploadNoteModal')">
              <i class="fas fa-upload" aria-hidden="true"></i> Upload New Note
            </button>
            <button class="btn btn-gold superadmin-only" onclick="showAdminPage('students')">
              <i class="fas fa-users" aria-hidden="true"></i> Manage Students
            </button>
            <button class="btn btn-outline superadmin-only" onclick="exportReport('payments')">
              <i class="fas fa-download" aria-hidden="true"></i> Export Payments CSV
            </button>
            <button class="btn btn-outline superadmin-only" onclick="openModal('addUserModal')">
              <i class="fas fa-user-plus" aria-hidden="true"></i> Add Tutor
            </button>
          </div>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: STUDENTS ══ -->
      <section class="admin-page-section" id="adminPage-students" style="display:none;">
        <div class="table-toolbar">
          <div class="search-box">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" id="studentSearch" placeholder="Search by name, email, or reg number…" aria-label="Search students">
          </div>
          <div class="toolbar-filters">
            <select class="filter-select" id="courseFilter" aria-label="Filter by course">
              <option value="">All Courses</option>
              <option>Diploma in Nursing (Extension)</option>
              <option>Diploma in Nursing (Direct)</option>
              <option>Diploma in Midwifery (Extension)</option>
              <option>Diploma in Midwifery (Direct)</option>
            </select>
            <button class="btn btn-primary btn-sm" onclick="exportReport('students')">
              <i class="fas fa-download" aria-hidden="true"></i> Export
            </button>
          </div>
        </div>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Student list">
            <thead>
              <tr>
                <th>Student</th><th>Email</th><th>Course</th><th>Year</th>
                <th>Status</th><th>Access Expires</th><th>Actions</th>
              </tr>
            </thead>
            <tbody id="studentsTableBody">
              <tr><td colspan="7" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
            </tbody>
          </table>
        </div>
        <div class="pagination">
          <span id="studentsCount">Showing all students</span>
          <div class="pagination-controls">
            <button class="page-btn" disabled aria-label="Previous page"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
            <button class="page-btn active">1</button>
            <button class="page-btn" disabled aria-label="Next page"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
          </div>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: NOTES ══ -->
      <section class="admin-page-section" id="adminPage-notes" style="display:none;">
        <div class="table-toolbar">
          <div class="search-box">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" placeholder="Search notes…" aria-label="Search notes">
          </div>
          <button class="btn btn-primary btn-sm" onclick="openModal('uploadNoteModal')">
            <i class="fas fa-plus" aria-hidden="true"></i> Add New Note
          </button>
        </div>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Notes list">
            <thead><tr><th>Title</th><th>Year / Semester</th><th>Module</th><th>Views</th><th>Date Added</th><th>Actions</th></tr></thead>
            <tbody id="notesTableBody">
              <tr><td colspan="6" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: LIVE CLASSES ══ -->
      <section class="admin-page-section" id="adminPage-classes" style="display:none;">
        <div class="table-toolbar">
          <h2 style="font-size:1.1rem;color:var(--primary-blue);font-weight:600;">Manage Live Class Sessions & Telegram Links</h2>
          <button class="btn btn-primary btn-sm" onclick="openModal('addClassModal')">
            <i class="fas fa-plus" aria-hidden="true"></i> Schedule Class
          </button>
        </div>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Live classes">
            <thead><tr><th>Title</th><th>Module</th><th>Tutor</th><th>Date</th><th>Time</th><th>Status</th><th>Telegram Link</th><th>Actions</th></tr></thead>
            <tbody id="classesTableBody">
              <tr><td colspan="8" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: TRANSACTIONS ══ -->
      <section class="admin-page-section" id="adminPage-transactions" style="display:none;">
        <div class="table-toolbar">
          <div class="search-box">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" placeholder="Search by reference or name…" aria-label="Search transactions">
          </div>
          <div class="toolbar-filters">
            <button class="btn btn-primary btn-sm" onclick="exportReport('payments')">
              <i class="fas fa-download" aria-hidden="true"></i> Export CSV
            </button>
          </div>
        </div>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Transactions">
            <thead><tr><th>Reference</th><th>Student</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
            <tbody id="transactionsTableBody">
              <tr><td colspan="6" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: CURRICULUM ══ -->
      <section class="admin-page-section" id="adminPage-curriculum" style="display:none;">
        <div class="table-toolbar">
          <h2 style="font-size:1.1rem;color:var(--primary-blue);font-weight:600;">Curriculum Modules</h2>
          <button class="btn btn-primary btn-sm" id="addModuleBtn">
            <i class="fas fa-plus" aria-hidden="true"></i> Add Module
          </button>
        </div>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Curriculum modules">
            <thead><tr><th>Course</th><th>Year</th><th>Semester</th><th>Code</th><th>Title</th><th>Actions</th></tr></thead>
            <tbody id="curriculumTableBody">
              <tr><td colspan="6" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: USER MANAGEMENT ══ -->
      <section class="admin-page-section" id="adminPage-users" style="display:none;">
        <div class="table-toolbar">
          <h3 style="color:var(--primary-blue);font-size:1rem;">Admins & Tutors</h3>
          <button class="btn btn-primary btn-sm" onclick="openModal('addUserModal')">
            <i class="fas fa-user-plus" aria-hidden="true"></i> Add Tutor
          </button>
        </div>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Users table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody id="usersTableBody">
              <tr><td colspan="5" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: REPORTS ══ -->
      <section class="admin-page-section" id="adminPage-reports" style="display:none;">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;">
          <div class="card">
            <div class="card-header"><h3>Student Report</h3></div>
            <div class="card-body">
              <p style="font-size:0.9rem;color:#5a6a7a;margin-bottom:16px;">Export full student list with enrollment details, access status, and payment history.</p>
              <button class="btn btn-primary btn-sm" onclick="exportReport('students')"><i class="fas fa-download" aria-hidden="true"></i> Export Students CSV</button>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Payment Report</h3></div>
            <div class="card-body">
              <p style="font-size:0.9rem;color:#5a6a7a;margin-bottom:16px;">Export all payment transactions with reference numbers, amounts, and dates.</p>
              <button class="btn btn-primary btn-sm" onclick="exportReport('payments')"><i class="fas fa-download" aria-hidden="true"></i> Export Payments CSV</button>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Content Analytics</h3></div>
            <div class="card-body">
              <p style="font-size:0.9rem;color:#5a6a7a;margin-bottom:16px;">See which notes are most viewed and student engagement statistics.</p>
              <button class="btn btn-outline btn-sm" onclick="Toast.info('Analytics dashboard – connect backend.')"><i class="fas fa-chart-bar" aria-hidden="true"></i> View Analytics</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ══ ADMIN PAGE: SETTINGS ══ -->
      <section class="admin-page-section" id="adminPage-settings" style="display:none;">
        <div class="settings-grid">
          <div class="settings-card">
            <h3><i class="fas fa-money-bill-wave" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Pricing Settings</h3>
            <div class="form-group">
              <label class="form-label">Subscription Price (UGX)</label>
              <input type="number" class="form-input" value="18500" min="0">
            </div>
            <div class="form-group">
              <label class="form-label">Access Duration (days)</label>
              <input type="number" class="form-input" value="90" min="1">
            </div>
            <button class="btn btn-primary btn-sm" onclick="Toast.success('Settings saved (demo).')">
              <i class="fas fa-save" aria-hidden="true"></i> Save Changes
            </button>
          </div>
          <div class="settings-card">
            <h3><i class="fas fa-envelope" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Notification Settings</h3>
            <div class="form-group">
              <label class="form-label">Expiry Warning (days before)</label>
              <input type="number" class="form-input" value="7" min="1">
            </div>
            <div class="form-group">
              <label class="form-label">Support Email</label>
              <input type="email" class="form-input" value="support@nursespro.ac.ug">
            </div>
            <button class="btn btn-primary btn-sm" onclick="Toast.success('Settings saved (demo).')">
              <i class="fas fa-save" aria-hidden="true"></i> Save Changes
            </button>
          </div>
          <div class="settings-card">
            <h3><i class="fas fa-credit-card" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Pesapal Settings</h3>
            <p class="form-hint" style="margin-bottom:14px;">Live Consumer Key/Secret are configured directly in <code>config/pesapal.php</code> on the server (not editable here, so they're never exposed to the browser). Environment: <strong><?= strtoupper(PESAPAL_ENV) ?></strong><?= pesapal_is_configured() ? '' : ' — not yet configured, demo payments are active' ?>.</p>
            <div class="form-group">
              <label class="form-label">Subscription Amount (UGX)</label>
              <input type="number" class="form-input" value="<?= (int)PESAPAL_AMOUNT ?>" disabled>
            </div>
            <button class="btn btn-primary btn-sm" onclick="Toast.info('Update PESAPAL_AMOUNT / PESAPAL_ENV in config/pesapal.php to change this.')">
              <i class="fas fa-save" aria-hidden="true"></i> Save Changes
            </button>
          </div>
          <div class="settings-card">
            <h3><i class="fas fa-shield-alt" style="color:var(--accent-gold);margin-right:8px;" aria-hidden="true"></i>Security Settings</h3>
            <div class="form-group">
              <label class="form-label" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" checked> Require email verification on signup
              </label>
            </div>
            <div class="form-group">
              <label class="form-label" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" checked> Apply watermark on PDF notes
              </label>
            </div>
            <div class="form-group">
              <label class="form-label" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" checked> Enable rate limiting on login (5 attempts)
              </label>
            </div>
            <button class="btn btn-primary btn-sm" onclick="Toast.success('Security settings saved (demo).')">
              <i class="fas fa-save" aria-hidden="true"></i> Save Changes
            </button>
          </div>
        </div>
      </section>

    </main>
  </div>
</div>

<!-- ── Upload Note Modal ──────────────────────────────────────── -->
<div class="modal-overlay" id="uploadNoteModal" role="dialog" aria-modal="true" aria-labelledby="uploadNoteTitle">
  <div class="modal" style="max-width:560px;">
    <div class="modal-header">
      <div><h2 id="uploadNoteTitle">Upload Study Note</h2><p id="uploadNoteSubtitle">Add a new PDF note for students</p></div>
      <button class="modal-close" onclick="closeModal('uploadNoteModal')" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body">
      <form id="uploadNoteForm" novalidate>
        <input type="hidden" id="noteId">
        <div class="form-group">
          <label class="form-label" for="noteTitle">Note Title <span class="required">*</span></label>
          <input type="text" id="noteTitle" class="form-input" placeholder="e.g. Fundamentals of Nursing Practice" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="noteModule">Course / Module <span class="required">*</span></label>
          <select id="noteModule" class="form-select" required>
            <option value="">— Select course/module —</option>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="noteTopic">Topic</label>
            <select id="noteTopic" class="form-select" disabled>
              <option value="">— Select module first —</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="noteSubtopic">Subtopic</label>
            <select id="noteSubtopic" class="form-select" disabled>
              <option value="">— Select topic first —</option>
            </select>
          </div>
        </div>
        <p style="font-size:0.78rem;color:#7a8a9a;margin-top:-10px;margin-bottom:14px;">Students will find this note under this course, topic, and subtopic in their Study Notes tab and search.</p>
        <div class="form-group">
          <label class="form-label" id="noteFileLabel">PDF File <span class="required">*</span></label>
          <div class="upload-area" id="uploadArea" role="button" tabindex="0" aria-label="Click to upload PDF">
            <input type="file" id="pdfFileInput" accept=".pdf" aria-label="Upload PDF file">
            <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
            <h4>Click to upload or drag & drop</h4>
            <p id="noteFileHint">PDF files only, max 50MB</p>
          </div>
          <div class="uploaded-file" id="uploadedFileInfo" style="display:none;">
            <i class="fas fa-file-pdf" aria-hidden="true"></i>
            <span class="file-name">filename.pdf</span>
            <span class="file-size">0 MB</span>
            <button type="button" class="remove-file" aria-label="Remove file" onclick="document.getElementById('uploadedFileInfo').style.display='none';document.getElementById('pdfFileInput').value='';"><i class="fas fa-times" aria-hidden="true"></i></button>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="noteDesc">Description</label>
          <textarea id="noteDesc" class="form-input" rows="3" placeholder="Brief description of the note content…" style="resize:vertical;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-full" id="noteSubmitBtn">
          <i class="fas fa-upload" aria-hidden="true"></i> Publish Note
        </button>
      </form>
    </div>
  </div>
</div>

<!-- ── Schedule Class Modal ─────────────────────────────────────── -->
<div class="modal-overlay" id="addClassModal" role="dialog" aria-modal="true" aria-labelledby="addClassTitle">
  <div class="modal" style="max-width:560px;">
    <div class="modal-header">
      <div><h2 id="addClassTitle">Schedule Live Class</h2><p id="addClassSubtitle">Add a new Telegram class session for students</p></div>
      <button class="modal-close" onclick="closeModal('addClassModal')" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body">
      <form id="scheduleClassForm" novalidate>
        <input type="hidden" id="classId">
        <div class="form-group">
          <label class="form-label" for="classTitle">Class Title <span class="required">*</span></label>
          <input type="text" id="classTitle" class="form-input" placeholder="e.g. Cardiac Case Review" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="classModule">Module <span class="required">*</span></label>
          <select id="classModule" class="form-select" required>
            <option value="">— Select module —</option>
          </select>
          <p style="font-size:0.78rem;color:#7a8a9a;margin-top:4px;">Students will see this class under this module in their Live Classes tab.</p>
        </div>
        <div class="form-group">
          <label class="form-label" for="classTutor">Tutor <span class="required">*</span></label>
          <input type="text" id="classTutor" class="form-input" placeholder="e.g. Dr. Sarah Namukasa" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="classDate">Date <span class="required">*</span></label>
            <input type="date" id="classDate" class="form-input" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="classTime">Time <span class="required">*</span></label>
            <input type="text" id="classTime" class="form-input" placeholder="e.g. 6:00 PM – 8:00 PM" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="classStatus">Status <span class="required">*</span></label>
          <select id="classStatus" class="form-select" required>
            <option value="upcoming">Upcoming</option>
            <option value="live">Live Now</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="classTelegram">Telegram Link</label>
          <input type="url" id="classTelegram" class="form-input" placeholder="https://t.me/…">
        </div>
        <button type="submit" class="btn btn-primary btn-full" id="classSubmitBtn">
          <i class="fas fa-calendar-plus" aria-hidden="true"></i> Schedule Class
        </button>
      </form>
    </div>
  </div>
</div>

<!-- ── Add/Edit Curriculum Module Modal ─────────────────────────── -->
<div class="modal-overlay" id="moduleModal" role="dialog" aria-modal="true" aria-labelledby="moduleModalTitle">
  <div class="modal" style="max-width:520px;">
    <div class="modal-header">
      <div><h2 id="moduleModalTitle">Add Module</h2><p>Modules power registration, notes, and classes</p></div>
      <button class="modal-close" onclick="closeModal('moduleModal')" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body">
      <form id="moduleForm" novalidate>
        <input type="hidden" id="moduleId">
        <div class="form-group">
          <label class="form-label" for="moduleCourse">Course <span class="required">*</span></label>
          <input type="text" id="moduleCourse" class="form-input" list="moduleCourseList" placeholder="e.g. Diploma in Nursing (Extension)" required>
          <datalist id="moduleCourseList"></datalist>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="moduleYear">Year <span class="required">*</span></label>
            <input type="text" id="moduleYear" class="form-input" list="moduleYearList" placeholder="e.g. Year 1" required>
            <datalist id="moduleYearList"><option value="Year 1"><option value="Year 2"><option value="Year 3"></datalist>
          </div>
          <div class="form-group">
            <label class="form-label" for="moduleSemester">Semester <span class="required">*</span></label>
            <input type="text" id="moduleSemester" class="form-input" list="moduleSemesterList" placeholder="e.g. Semester 1" required>
            <datalist id="moduleSemesterList"><option value="Semester 1"><option value="Semester 2"></datalist>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="moduleCode">Code <span class="required">*</span></label>
            <input type="text" id="moduleCode" class="form-input" placeholder="e.g. DNE 115" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="moduleTitle">Title <span class="required">*</span></label>
            <input type="text" id="moduleTitle" class="form-input" placeholder="e.g. Practicals" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-full">
          <i class="fas fa-save" aria-hidden="true"></i> Save Module
        </button>
      </form>
    </div>
  </div>
</div>

<!-- ── Add User Modal ─────────────────────────────────────────── -->
<div class="modal-overlay" id="addUserModal" role="dialog" aria-modal="true" aria-labelledby="addUserTitle">
  <div class="modal">
    <div class="modal-header">
      <div><h2 id="addUserTitle">Add Tutor / Admin</h2><p>Create a new staff account</p></div>
      <button class="modal-close" onclick="closeModal('addUserModal')" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body">
      <form id="addUserForm" novalidate>
        <div class="form-group">
          <label class="form-label" for="newUserName">Full Name <span class="required">*</span></label>
          <input type="text" id="newUserName" class="form-input" placeholder="e.g. Dr. James Ssali" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="newUserEmail">Email Address <span class="required">*</span></label>
          <input type="email" id="newUserEmail" class="form-input" placeholder="tutor@nursespro.ac.ug" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="newUserRole">Role <span class="required">*</span></label>
          <select id="newUserRole" class="form-select" required>
            <option value="tutor">Tutor</option>
            <option value="superadmin">Super Admin</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="newUserPassword">Temporary Password <span class="required">*</span></label>
          <input type="text" id="newUserPassword" class="form-input" placeholder="They must change on first login" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full" onclick="addNewUser(event)">
          <i class="fas fa-user-plus" aria-hidden="true"></i> Create Account
        </button>
      </form>
    </div>
  </div>
</div>

<script src="<?= asset_v('js/utils.js') ?>"></script>
<script src="<?= asset_v('js/curriculum.js') ?>"></script>
<script src="<?= asset_v('js/auth.js') ?>"></script>
<script src="<?= asset_v('js/payment.js') ?>"></script>
<script src="<?= asset_v('js/admin.js') ?>"></script>
<script>
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('active'); document.body.style.overflow = ''; }
}
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('active'); document.body.style.overflow = 'hidden'; }
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) { e.target.classList.remove('active'); document.body.style.overflow = ''; }
});

// Load recent payments in overview
document.addEventListener('DOMContentLoaded', async () => {
  const res = await fetch('api/payments.php').then(r => r.json());
  const payments = (res.payments || []).slice(0, 4);
  const list = document.getElementById('overviewPaymentsList');
  if (list && payments.length) {
    list.innerHTML = payments.map(p => `
      <div class="activity-item">
        <div class="activity-dot green"></div>
        <div>
          <div class="activity-text">${sanitize(p.student_name || 'Unknown')} – UGX ${Number(p.amount).toLocaleString()} (${sanitize(p.method)})</div>
          <div class="activity-time">${new Date(p.paid_at).toLocaleDateString()}</div>
        </div>
      </div>`).join('');
  }
});

async function addNewUser(e) {
  e.preventDefault();
  const name  = document.getElementById('newUserName').value.trim();
  const email = document.getElementById('newUserEmail').value.trim();
  const role  = document.getElementById('newUserRole').value;
  const pw    = document.getElementById('newUserPassword').value;
  if (!name || !email || !pw) { Toast.error('Please fill in all fields.'); return; }

  const res = await fetch('api/users.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'add', name, email, role, password: pw })
  }).then(r => r.json());

  if (!res.success) { Toast.error(res.message || 'Could not create account.'); return; }
  closeModal('addUserModal');
  Toast.success(`${role === 'tutor' ? 'Tutor' : 'Admin'} account created for ${name}.`);
  document.getElementById('addUserForm').reset();
  renderUsersTable();
}
</script>
</body>
</html>
