<?php
require_once __DIR__ . '/includes/auth_guard.php';
$user = require_login_page();
if (in_array($user['role'], ['superadmin', 'tutor'], true)) {
  header('Location: admin-dashboard.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard – NursesPro Academy</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= asset_v('css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/dashboard.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/responsive.css') ?>">
</head>
<body>

<!-- ── Paywall (shown instead of the portal until a student has active access) ── -->
<div class="paywall-overlay" id="paywallOverlay" style="display:none;">
  <div class="paywall-card">
    <div class="paywall-icon"><i class="fas fa-lock" aria-hidden="true"></i></div>
    <h2>Payment Required</h2>
    <p>Your account is created, but you need an active subscription to access your student
      portal — study notes, live classes, and everything else.</p>
    <div class="paywall-amount">UGX 18,500 <span>/ 6 months</span></div>
    <div class="form-group" style="text-align:left;">
      <label class="form-label" for="paywallPhone">Mobile Money Number</label>
      <div class="input-wrapper">
        <i class="fas fa-phone input-icon" aria-hidden="true"></i>
        <input type="tel" id="paywallPhone" class="form-input" placeholder="07XX XXX XXX">
      </div>
    </div>
    <button class="btn btn-gold btn-full btn-lg" id="paywallPayBtn">
      <i class="fas fa-credit-card" aria-hidden="true"></i> Pay Now
    </button>
    <p class="paywall-hint">
      <i class="fas fa-shield-alt" aria-hidden="true"></i>
      You'll be redirected to Pesapal's secure payment page — Mobile Money or Card.
    </p>
    <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-top:20px;">
      <button class="paywall-signout" id="paywallHistoryBtn" style="margin-top:0;"><i class="fas fa-receipt" aria-hidden="true"></i> View Payment History</button>
      <span style="color:#c8d0d8;">•</span>
      <button class="paywall-signout" id="paywallLogoutBtn" style="margin-top:0;">Sign Out</button>
    </div>

    <div id="paywallHistorySection" style="display:none;margin-top:20px;text-align:left;border-top:1px solid #eef1f5;padding-top:18px;">
      <h3 style="font-size:0.9rem;color:var(--primary-blue);margin-bottom:10px;">Your Payment History</h3>
      <div class="payment-table-wrapper">
        <table class="payment-table" aria-label="Payment history" style="font-size:0.78rem;">
          <thead>
            <tr><th>Reference</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Access Until</th></tr>
          </thead>
          <tbody id="paywallPaymentsTableBody">
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#7a8a9a;">Loading…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="dashboard-wrapper" id="dashboardWrapper">

  <!-- ── Sidebar ─────────────────────────────────────────────── -->
  <aside class="sidebar" id="sidebar" role="navigation" aria-label="Student navigation">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <img src="assets/images/logo-mark.svg" alt="NursesPro Academy" width="36" height="36" style="border-radius:8px;flex-shrink:0;">
        NursesPro <span class="logo-badge">PRO</span>
      </div>
      <div class="sidebar-user">
        <div class="sidebar-avatar" data-user-initials aria-hidden="true">SN</div>
        <div class="sidebar-user-info">
          <h4 data-user-name>Loading…</h4>
          <p data-user-role>Student</p>
        </div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section-label">Main Menu</div>
      <a href="#" class="sidebar-link active" data-page="dashboard">
        <i class="fas fa-home" aria-hidden="true"></i> Dashboard
      </a>
      <a href="#" class="sidebar-link" data-page="courses">
        <i class="fas fa-book" aria-hidden="true"></i> My Courses
      </a>
      <a href="#" class="sidebar-link" data-page="notes">
        <i class="fas fa-file-pdf" aria-hidden="true"></i> Study Notes
      </a>
      <a href="#" class="sidebar-link" data-page="mynotes">
        <i class="fas fa-sticky-note" aria-hidden="true"></i> My Notes
      </a>
      <a href="#" class="sidebar-link" data-page="classes">
        <i class="fab fa-telegram-plane" aria-hidden="true"></i> Live Classes
        <span class="badge-count">2</span>
      </a>
      <a href="#" class="sidebar-link" data-page="revision">
        <i class="fas fa-pencil-alt" aria-hidden="true"></i> Exam Revision
      </a>
      <div class="nav-section-label">Account</div>
      <a href="#" class="sidebar-link" data-page="profile">
        <i class="fas fa-user-circle" aria-hidden="true"></i> My Profile
      </a>
      <a href="#" class="sidebar-link" data-page="payments">
        <i class="fas fa-receipt" aria-hidden="true"></i> Payment History
      </a>
    </nav>
    <div class="sidebar-footer">
      <button class="sidebar-logout" id="logoutBtn">
        <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Sign Out
      </button>
    </div>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ── Main Content ─────────────────────────────────────────── -->
  <div class="main-content">
    <!-- Topbar -->
    <header class="topbar" role="banner">
      <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
          <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <h1 class="page-title" id="pageTitle">Dashboard</h1>
      </div>
      <div class="topbar-right">
        <button class="notif-btn" aria-label="Notifications">
          <i class="fas fa-bell" aria-hidden="true"></i>
          <span class="notif-dot" style="display:none;" aria-hidden="true"></span>
        </button>
        <div class="topbar-user" role="button" tabindex="0">
          <div class="topbar-avatar" data-user-initials aria-hidden="true">SN</div>
          <span class="topbar-user-name" data-user-name>Student</span>
        </div>
      </div>
    </header>

    <main class="page-content" role="main">

      <!-- Access Banner -->
      <div id="accessBanner" class="access-banner active" role="alert"></div>

      <!-- ══ PAGE: DASHBOARD ══ -->
      <section class="page-section" id="page-dashboard">
        <div style="margin-bottom:24px;">
          <h2 style="font-size:1.4rem;font-weight:700;color:var(--primary-blue);">Welcome back, <span data-user-name>Student</span>! 👋</h2>
          <p style="color:#5a6a7a;font-size:0.95rem;margin-top:4px;">Here's what's happening with your studies today.</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-file-pdf" aria-hidden="true"></i></div>
            <div><div class="stat-value" id="statNotesViewed">0</div><div class="stat-label">Notes Viewed</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-video" aria-hidden="true"></i></div>
            <div><div class="stat-value" id="statClassesAttended">0</div><div class="stat-label">Classes Attended</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div>
            <div><div class="stat-value" id="statQuizzesTaken">0</div><div class="stat-label">Quizzes Taken</div></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            <div><div class="stat-value" id="statDaysLeft">0</div><div class="stat-label">Days Remaining</div></div>
          </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
          <div class="card">
            <div class="card-header">
              <h3><i class="fas fa-history icon-teal" aria-hidden="true"></i>Recent Activity</h3>
              <a href="#" data-page="notes">View All</a>
            </div>
            <div class="card-body">
              <div class="activity-list" id="activityList"></div>
            </div>
          </div>
          <div class="card">
            <div class="card-header">
              <h3><i class="fas fa-star icon-teal" aria-hidden="true"></i>My Courses</h3>
              <a href="#" data-page="courses">View All</a>
            </div>
            <div class="card-body">
              <div id="coursesList"></div>
            </div>
          </div>
        </div>

        <!-- Quick Access -->
        <div class="card" style="margin-top:0;">
          <div class="card-header">
            <h3><i class="fas fa-bolt icon-teal" aria-hidden="true"></i>Quick Access</h3>
          </div>
          <div class="card-body" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;">
            <a href="#" data-page="notes" class="btn btn-primary" style="flex-direction:column;gap:8px;padding:20px;text-align:center;">
              <i class="fas fa-file-pdf" style="font-size:1.5rem;" aria-hidden="true"></i> Study Notes
            </a>
            <a href="#" data-page="classes" class="btn btn-gold" style="flex-direction:column;gap:8px;padding:20px;text-align:center;">
              <i class="fab fa-telegram-plane" style="font-size:1.5rem;" aria-hidden="true"></i> Live Classes
            </a>
            <a href="#" data-page="revision" class="btn btn-outline" style="flex-direction:column;gap:8px;padding:20px;text-align:center;">
              <i class="fas fa-pencil-alt" style="font-size:1.5rem;" aria-hidden="true"></i> Exam Revision
            </a>
            <button onclick="openPaymentModal()" class="btn" style="background:#f0fdf4;color:var(--success-green);border:1px solid #bbf7d0;flex-direction:column;gap:8px;padding:20px;text-align:center;">
              <i class="fas fa-credit-card" style="font-size:1.5rem;" aria-hidden="true"></i> Renew Access
            </button>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: MY COURSES ══ -->
      <section class="page-section" id="page-courses" style="display:none;">
        <div style="background:var(--white);border-radius:var(--radius-md);box-shadow:var(--shadow-sm);padding:40px;text-align:center;">
          <div style="font-size:4rem;margin-bottom:16px;">🎓</div>
          <h3 style="color:var(--primary-blue);margin-bottom:12px;">Your Enrolled Course</h3>
          <p style="color:#5a6a7a;margin-bottom:8px;" id="enrolledCourseDisplay">Loading course…</p>
          <p style="font-size:0.85rem;color:#7a8a9a;">All study notes and live classes are tailored to your course and year of study.</p>
          <div style="margin-top:24px;display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="#" data-page="notes" class="btn btn-primary"><i class="fas fa-file-pdf" aria-hidden="true"></i> View Study Notes</a>
            <a href="#" data-page="classes" class="btn btn-outline"><i class="fab fa-telegram-plane" aria-hidden="true"></i> Live Classes</a>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: STUDY NOTES ══ -->
      <section class="page-section" id="page-notes" style="display:none;">
        <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
          <div>
            <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary-blue);">Study Notes</h2>
            <p style="color:#5a6a7a;font-size:0.85rem;">Click any note to open the secure viewer. No download permitted.</p>
          </div>
          <div style="display:flex;align-items:center;gap:8px;background:var(--light-blue);padding:8px 16px;border-radius:50px;font-size:0.82rem;color:var(--primary-blue);">
            <i class="fas fa-lock" aria-hidden="true"></i> Notes are secured & watermarked
          </div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px;">
          <div class="input-wrapper" style="max-width:320px;flex:1;min-width:220px;">
            <i class="fas fa-search input-icon" aria-hidden="true"></i>
            <input type="text" class="form-input" id="notesSearch" placeholder="Search notes, topics, tutor name…">
          </div>
          <input type="text" class="form-input" id="notesSearchTutor" placeholder="Filter by tutor/uploader" style="max-width:200px;">
        </div>
        <div class="notes-filters" role="group" aria-label="Filter notes by module"></div>
        <div class="notes-grid" id="notesGrid">
          <p style="color:#7a8a9a;text-align:center;padding:40px 0;">Loading notes…</p>
        </div>
      </section>

      <!-- ══ PAGE: MY NOTES ══ -->
      <section class="page-section" id="page-mynotes" style="display:none;">
        <div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
          <div>
            <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary-blue);">My Notes</h2>
            <p style="color:#5a6a7a;font-size:0.85rem;">Write your own notes, organize them by category, then export as PDF or print.</p>
          </div>
          <button class="btn btn-primary btn-sm" id="newMyNoteBtn">
            <i class="fas fa-plus" aria-hidden="true"></i> New Note
          </button>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:16px;">
          <div class="input-wrapper" style="max-width:280px;flex:1;min-width:200px;">
            <i class="fas fa-search input-icon" aria-hidden="true"></i>
            <input type="text" class="form-input" id="myNotesSearch" placeholder="Search my notes…">
          </div>
          <div class="notes-filters" role="group" aria-label="Filter my notes by category" id="myNotesCategoryFilters" style="margin:0;">
            <button class="filter-btn my-notes-filter-btn active" data-cat="all">All</button>
          </div>
        </div>

        <div class="notes-grid" id="myNotesGrid">
          <p style="color:#7a8a9a;text-align:center;padding:40px 0;">Loading your notes…</p>
        </div>
      </section>

      <!-- ══ PAGE: LIVE CLASSES ══ -->
      <section class="page-section" id="page-classes" style="display:none;">
        <div style="margin-bottom:20px;">
          <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary-blue);">Live Classes</h2>
          <p style="color:#5a6a7a;font-size:0.85rem;">Telegram links are exclusive to active subscribers and refresh weekly.</p>
        </div>
        <div class="notes-filters" role="group" aria-label="Filter classes by module" id="classesFilters"></div>
        <div class="class-cards-grid" id="classesGrid">
          <p style="color:#7a8a9a;">Loading classes…</p>
        </div>
      </section>

      <!-- ══ PAGE: EXAM REVISION ══ -->
      <section class="page-section" id="page-revision" style="display:none;">
        <div style="background:var(--white);border-radius:var(--radius-md);box-shadow:var(--shadow-sm);padding:40px;text-align:center;">
          <div style="font-size:4rem;margin-bottom:16px;">📝</div>
          <h3 style="color:var(--primary-blue);margin-bottom:12px;">Exam Revision Hub</h3>
          <p style="color:#5a6a7a;max-width:500px;margin:0 auto 24px;">Practice past papers and quizzes to prepare for your exams. This section is actively being built—check back soon!</p>
          <div style="background:var(--light-blue);border-radius:var(--radius-md);padding:24px;display:inline-block;text-align:left;max-width:400px;">
            <h4 style="color:var(--primary-blue);margin-bottom:12px;"><i class="fas fa-info-circle" aria-hidden="true"></i> Coming Soon</h4>
            <ul style="list-style:disc;padding-left:20px;color:#5a6a7a;font-size:0.9rem;line-height:2;">
              <li>Past paper questions & answers</li>
              <li>Topic-based quizzes</li>
              <li>Timed mock exams</li>
              <li>Performance analytics</li>
            </ul>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: PROFILE ══ -->
      <section class="page-section" id="page-profile" style="display:none;">
        <div class="profile-layout">
          <div class="profile-card">
            <div class="profile-card-top">
              <div class="profile-big-avatar" data-profile-initials aria-hidden="true">SN</div>
              <h3 data-user-name>Student Name</h3>
              <p data-user-role>Student</p>
            </div>
            <div class="profile-info-list">
              <div class="profile-info-item"><span class="label">Email</span><span class="value" id="prof-email">—</span></div>
              <div class="profile-info-item"><span class="label">Phone</span><span class="value" id="prof-phone">—</span></div>
              <div class="profile-info-item"><span class="label">Reg. Number</span><span class="value" id="prof-reg">—</span></div>
              <div class="profile-info-item"><span class="label">Institution</span><span class="value" id="prof-institution">—</span></div>
              <div class="profile-info-item"><span class="label">Joined</span><span class="value" id="prof-joined">—</span></div>
            </div>
          </div>
          <div>
            <div class="card" style="margin-bottom:20px;">
              <div class="card-header"><h3>Academic Details</h3></div>
              <div class="card-body">
                <p style="font-size:0.8rem;color:#7a8a9a;margin-bottom:16px;">Keep this up to date each semester — your Study Notes, Live Classes, and modules below are organized around it.</p>
                <div style="margin-bottom:16px;"><p style="font-size:0.8rem;color:#7a8a9a;">Full Name</p><p style="font-weight:600;color:var(--text-dark);margin-top:4px;" id="prof-name">—</p></div>
                <form id="academicDetailsForm">
                  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label" for="profCourse">Course</label>
                      <select id="profCourse" class="form-select">
                        <option>Diploma in Nursing (Extension)</option>
                        <option>Diploma in Nursing (Direct)</option>
                        <option>Diploma in Midwifery (Extension)</option>
                        <option>Diploma in Midwifery (Direct)</option>
                      </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                      <label class="form-label" for="profYear">Year of Study</label>
                      <select id="profYear" class="form-select">
                        <option>Year 1</option><option>Year 2</option><option>Year 3</option>
                      </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;grid-column:1/-1;max-width:calc(50% - 8px);">
                      <label class="form-label" for="profSemester">Semester</label>
                      <select id="profSemester" class="form-select">
                        <option>Semester 1</option><option>Semester 2</option>
                      </select>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm" style="margin-top:16px;">
                    <i class="fas fa-save" aria-hidden="true"></i> Update Academic Details
                  </button>
                </form>
              </div>
            </div>
            <div class="card" id="profModulesCard" style="display:none;margin-bottom:20px;">
              <div class="card-header"><h3>Current Modules</h3></div>
              <div class="card-body">
                <ul id="profModulesList" style="list-style:none;display:grid;gap:10px;"></ul>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><h3>Change Password</h3></div>
              <div class="card-body">
                <form id="changePasswordForm">
                  <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-input" id="currentPw" placeholder="Enter current password">
                  </div>
                  <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-input" id="newPw" placeholder="Min. 8 characters">
                  </div>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key" aria-hidden="true"></i> Update Password
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: PAYMENTS ══ -->
      <section class="page-section" id="page-payments" style="display:none;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
          <h2 style="font-size:1.2rem;font-weight:700;color:var(--primary-blue);">Payment History</h2>
          <button class="btn btn-primary btn-sm" onclick="openPaymentModal()">
            <i class="fas fa-plus" aria-hidden="true"></i> Make New Payment
          </button>
        </div>
        <div class="card">
          <div class="payment-table-wrapper">
            <table class="payment-table" aria-label="Payment history">
              <thead>
                <tr>
                  <th>Reference</th><th>Amount</th><th>Method</th>
                  <th>Date</th><th>Status</th><th>Access Until</th>
                </tr>
              </thead>
              <tbody id="paymentsTableBody">
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#7a8a9a;">Loading…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </main><!-- /page-content -->
  </div><!-- /main-content -->
</div><!-- /dashboard-wrapper -->

<!-- Bottom Nav (mobile) -->
<nav class="bottom-nav" role="navigation" aria-label="Quick navigation">
  <div class="bottom-nav-items">
    <a href="#" class="bottom-nav-item active" data-page="dashboard"><i class="fas fa-home" aria-hidden="true"></i><span>Home</span></a>
    <a href="#" class="bottom-nav-item" data-page="notes"><i class="fas fa-file-pdf" aria-hidden="true"></i><span>Notes</span></a>
    <a href="#" class="bottom-nav-item" data-page="classes"><i class="fab fa-telegram-plane" aria-hidden="true"></i><span>Classes</span></a>
    <a href="#" class="bottom-nav-item" data-page="profile"><i class="fas fa-user" aria-hidden="true"></i><span>Profile</span></a>
  </div>
</nav>

<!-- PDF Viewer Overlay -->
<div class="pdf-viewer-overlay" id="pdfViewerOverlay" role="dialog" aria-modal="true" aria-label="PDF Viewer">
  <div class="pdf-viewer-toolbar">
    <div class="pdf-toolbar-left">
      <button class="pdf-close-btn" id="pdfCloseBtn"><i class="fas fa-times" aria-hidden="true"></i> Close</button>
      <span class="pdf-title" id="pdfViewerTitle">Study Notes</span>
    </div>
    <div class="pdf-toolbar-center">
      <button class="pdf-nav-btn" id="pdfPrevPage" aria-label="Previous page"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
      <span class="pdf-page-info" id="pdfPageInfo">Page 1 of 1</span>
      <button class="pdf-nav-btn" id="pdfNextPage" aria-label="Next page"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
    </div>
    <div class="pdf-toolbar-right">
      <button class="pdf-nav-btn" id="pdfZoomOut" aria-label="Zoom out"><i class="fas fa-search-minus" aria-hidden="true"></i></button>
      <button class="pdf-nav-btn" id="pdfZoomIn" aria-label="Zoom in"><i class="fas fa-search-plus" aria-hidden="true"></i></button>
    </div>
  </div>
  <div class="pdf-canvas-wrapper" id="pdfCanvasWrapper">
    <canvas id="pdfCanvas"></canvas>
    <div class="pdf-watermark" id="pdfWatermark"></div>
  </div>
</div>

<!-- Payment Modal -->
<div class="modal-overlay" id="paymentModal" role="dialog" aria-modal="true" aria-labelledby="dashPayTitle">
  <div class="modal">
    <div class="modal-header">
      <div><h2 id="dashPayTitle">Renew Your Access</h2><p>Pay via Mobile Money – 18,500 UGX</p></div>
      <button class="modal-close" onclick="closeModal('paymentModal')" aria-label="Close payment"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body">
      <div id="paymentStepForm">
        <div class="payment-summary">
          <div class="amount-label">Amount to Pay</div>
          <div class="amount">UGX 18,500</div>
          <div class="amount-label" style="margin-top:4px;">6-Month Full Access</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="paymentPhone">Mobile Money Number <span class="required">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-phone input-icon" aria-hidden="true"></i>
            <input type="tel" id="paymentPhone" class="form-input" placeholder="07XX XXX XXX">
          </div>
        </div>
        <div class="payment-steps-info">
          You'll be redirected to Pesapal's secure payment page, where you can pay via
          <strong>MTN Mobile Money</strong>, <strong>Airtel Money</strong>, or <strong>card</strong>.
        </div>
        <button class="btn btn-primary btn-full btn-lg" id="confirmPaymentBtn" style="margin-top:20px;">
          <i class="fas fa-lock" aria-hidden="true"></i> Continue to Pesapal
        </button>
      </div>
      <div id="paymentStepProcessing" style="display:none;flex-direction:column;align-items:center;padding:40px 0;gap:16px;">
        <div style="width:60px;height:60px;border:4px solid var(--light-blue);border-top-color:var(--primary-blue);border-radius:50%;animation:spin 1s linear infinite;"></div>
        <p style="font-weight:600;color:var(--primary-blue);" id="paymentProcessingText">Redirecting to Pesapal…</p>
      </div>
      <div id="paymentStepSuccess" style="display:none;">
        <div class="payment-success">
          <div class="success-icon"><i class="fas fa-check" aria-hidden="true"></i></div>
          <h3 style="color:var(--success-green);font-size:1.3rem;margin-bottom:8px;">Payment Successful!</h3>
          <p style="color:#5a6a7a;margin-bottom:20px;">Your access has been renewed for 6 months.</p>
          <div style="background:var(--light-blue);border-radius:8px;padding:16px;margin-bottom:24px;">
            <p><strong>Access valid until:</strong><br>
            <span id="paymentExpiryDate" style="color:var(--primary-blue);font-weight:700;font-size:1.1rem;"></span></p>
          </div>
          <button class="btn btn-primary btn-full" id="goToDashboardBtn" onclick="closeModal('paymentModal');location.reload();">
            <i class="fas fa-check" aria-hidden="true"></i> Done
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- My Notes: Editor Modal -->
<div class="modal-overlay" id="myNoteModal" role="dialog" aria-modal="true" aria-labelledby="myNoteModalTitle">
  <div class="modal">
    <div class="modal-header">
      <div><h2 id="myNoteModalTitle">New Note</h2><p>Only visible to you</p></div>
      <button class="modal-close" onclick="closeModal('myNoteModal')" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="modal-body">
      <form id="myNoteForm">
        <input type="hidden" id="myNoteId">
        <div class="form-group">
          <label class="form-label" for="myNoteTitle">Title <span class="required">*</span></label>
          <input type="text" class="form-input" id="myNoteTitle" placeholder="e.g. Cardiac drug doses" maxlength="120" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="myNoteCategory">Category</label>
          <input type="text" class="form-input" id="myNoteCategory" list="myNoteCategoryList" placeholder="e.g. Pharmacology (or type a new one)">
          <datalist id="myNoteCategoryList"></datalist>
        </div>
        <div class="form-group">
          <label class="form-label" for="myNoteContent">Content</label>
          <textarea class="form-input" id="myNoteContent" rows="10" placeholder="Write your note here…" style="resize:vertical;font-family:inherit;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg">
          <i class="fas fa-save" aria-hidden="true"></i> Save Note
        </button>
      </form>
    </div>
  </div>
</div>

<!-- My Notes: hidden print template (shown only via @media print) -->
<div id="myNotePrintArea"></div>

<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

<!-- PDF.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>if (typeof pdfjsLib !== 'undefined') pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
<!-- jsPDF CDN (for exporting personal notes as PDF) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="<?= asset_v('js/utils.js') ?>"></script>
<script src="<?= asset_v('js/curriculum.js') ?>"></script>
<script src="<?= asset_v('js/auth.js') ?>"></script>
<script src="<?= asset_v('js/payment.js') ?>"></script>
<script src="<?= asset_v('js/notes.js') ?>"></script>
<script src="<?= asset_v('js/pdf-viewer.js') ?>"></script>
<script src="<?= asset_v('js/dashboard.js') ?>"></script>
<script>
// Additional dashboard helpers
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('active'); document.body.style.overflow = ''; }
}
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('active'); document.body.style.overflow = 'hidden'; }
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('active');
    document.body.style.overflow = '';
  }
});
// Profile: update enrolled course display
document.addEventListener('DOMContentLoaded', async () => {
  const user = await Auth.init();
  if (user) {
    const el = document.getElementById('enrolledCourseDisplay');
    if (el) el.textContent = user.course + ' · ' + user.year + ' · ' + user.semester;
    // change password
    const form = document.getElementById('changePasswordForm');
    form && form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const cur = document.getElementById('currentPw').value;
      const nw  = document.getElementById('newPw').value;
      const res = await fetch('api/profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'change-password', currentPassword: cur, newPassword: nw })
      }).then(r => r.json());
      if (!res.success) { Toast.error(res.message || 'Could not update password.'); return; }
      Toast.success('Password updated successfully.');
      form.reset();
    });
  }
});
</script>
</body>
</html>
