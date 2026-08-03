<?php
require_once __DIR__ . '/includes/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Browse and search NursesPro Academy's full nursing and midwifery notes catalog — free preview, no login required.">
  <title>Browse Notes – NursesPro Academy</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= asset_v('css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/home.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/dashboard.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/notes-catalog.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/responsive.css') ?>">
</head>
<body class="home-page">

<!-- ═══ NAVBAR ═══ -->
<nav class="hn-nav" id="navbar" role="navigation" aria-label="Main navigation">
  <div class="hn-nav-inner">
    <a href="index.php" class="hn-brand" aria-label="NursesPro Academy home">
      <img src="assets/images/logo-nav.svg" alt="NursesPro Academy" height="42" loading="eager">
    </a>
    <div class="hn-nav-links" id="navLinks">
      <a href="index.php#courses" class="hn-link">Courses</a>
      <a href="notes.php" class="hn-link" aria-current="page">Browse Notes</a>
      <a href="#" class="hn-btn-outline" id="loginBtnNav">Login</a>
      <a href="#" class="hn-btn-solid" id="signupBtnNav">Sign Up</a>
    </div>
    <button class="hn-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>
<div class="hn-drawer-overlay" id="mobileOverlay"></div>
<div class="hn-drawer" id="mobileMenu" role="dialog" aria-label="Mobile navigation">
  <div class="hn-drawer-header">
    <img src="assets/images/logo-nav.svg" alt="NursesPro Academy" height="36">
    <button class="hn-drawer-close" id="drawerClose" aria-label="Close menu"><i class="fas fa-times"></i></button>
  </div>
  <a href="index.php#courses" class="hn-drawer-link">Courses</a>
  <a href="notes.php" class="hn-drawer-link">Browse Notes</a>
  <hr class="hn-drawer-divider">
  <a href="#" class="hn-btn-outline" id="loginBtnMobile" style="text-align:center;display:block;">Login</a>
  <a href="#" class="hn-btn-solid" id="signupBtnMobile" style="text-align:center;display:block;margin-top:10px;">Sign Up Free</a>
</div>

<!-- ═══ CATALOG HEADER ═══ -->
<section class="nc-header">
  <div class="hn-container">
    <h1>Browse All Study Notes</h1>
    <p>Search the full NursesPro Academy notes library by topic, subtopic, module, semester, or tutor. Read a free preview of every note — no account needed.</p>
    <div class="nc-search-bar">
      <div class="nc-search-input">
        <i class="fas fa-search" aria-hidden="true"></i>
        <input type="text" id="ncSearchQuery" placeholder="Search by title, topic, subtopic, module, tutor…">
      </div>
      <button class="btn btn-outline" id="ncFiltersToggle" type="button">
        <i class="fas fa-sliders-h" aria-hidden="true"></i> Filters
      </button>
    </div>
    <div class="nc-filters" id="ncFilters">
      <div class="nc-filter-field">
        <label for="ncFilterModule">Module</label>
        <select id="ncFilterModule"><option value="">All Modules</option></select>
      </div>
      <div class="nc-filter-field">
        <label for="ncFilterTopic">Topic</label>
        <select id="ncFilterTopic"><option value="">All Topics</option></select>
      </div>
      <div class="nc-filter-field">
        <label for="ncFilterSubtopic">Subtopic</label>
        <select id="ncFilterSubtopic"><option value="">All Subtopics</option></select>
      </div>
      <div class="nc-filter-field">
        <label for="ncFilterYear">Year</label>
        <select id="ncFilterYear">
          <option value="">Any Year</option>
          <option value="Year 1">Year 1</option>
          <option value="Year 2">Year 2</option>
          <option value="Year 3">Year 3</option>
        </select>
      </div>
      <div class="nc-filter-field">
        <label for="ncFilterSemester">Semester</label>
        <select id="ncFilterSemester">
          <option value="">Any Semester</option>
          <option value="Semester 1">Semester 1</option>
          <option value="Semester 2">Semester 2</option>
        </select>
      </div>
      <div class="nc-filter-field">
        <label for="ncFilterTutor">Tutor</label>
        <input type="text" id="ncFilterTutor" placeholder="Tutor name">
      </div>
      <button class="btn btn-sm" id="ncClearFilters" type="button">Clear</button>
    </div>
  </div>
</section>

<!-- ═══ RESULTS ═══ -->
<section class="hn-section" style="padding-top:24px;">
  <div class="hn-container">
    <p class="nc-results-count" id="ncResultsCount"></p>
    <div class="notes-grid" id="ncNotesGrid">
      <p style="grid-column:1/-1;text-align:center;color:#7a8a9a;padding:40px 0;"><i class="fas fa-spinner fa-spin"></i> Loading notes…</p>
    </div>
  </div>
</section>

<!-- ═══ CTA STRIP ═══ -->
<section class="hn-contact-strip">
  <div class="hn-container">
    <div class="hn-contact-inner">
      <div class="hn-contact-text">
        <h3>Ready for full access?</h3>
        <p>Create a free account to keep reading past the preview, join live Telegram classes, and unlock every note. Pricing is shown once you're logged in.</p>
      </div>
      <div class="hn-contact-actions">
        <a href="#" class="hn-contact-btn hn-call" id="ctaSignupBtn"><i class="fas fa-user-plus"></i> Create Free Account</a>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FOOTER ═══ -->
<footer class="hn-footer">
  <div class="hn-container">
    <div class="hn-footer-bottom">
      <p>© <?= date('Y') ?> NursesPro Academy. All rights reserved.</p>
      <p>Kampala, Uganda · <a href="tel:+256392972444">0392 972 444</a> · <a href="tel:+256760167722">0760 167 722</a></p>
    </div>
  </div>
</footer>

<!-- PDF Viewer Overlay (preview mode) -->
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
    <div class="pdf-preview-lock" id="pdfPreviewLock">
      <div class="pdf-preview-lock-card">
        <i class="fas fa-lock" aria-hidden="true"></i>
        <h3>Continue reading free</h3>
        <p>You've reached the end of the free preview. Create a free account or log in to keep reading the full note.</p>
        <div class="pdf-preview-lock-actions">
          <button class="btn btn-primary" id="pdfLockSignupBtn"><i class="fas fa-user-plus"></i> Create Free Account</button>
          <button class="btn btn-outline" id="pdfLockLoginBtn"><i class="fas fa-sign-in-alt"></i> I Already Have an Account</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══ LOGIN MODAL ═══ -->
<div class="modal-overlay" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">
  <div class="modal">
    <div class="modal-header">
      <div>
        <h2 id="loginModalTitle">Welcome Back</h2>
        <p>Sign in to your NursesPro Academy account</p>
      </div>
      <button class="modal-close" onclick="closeModal('loginModal')" aria-label="Close"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="loginForm" novalidate>
        <div class="form-group">
          <label class="form-label" for="loginEmail">Email Address <span class="required">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" id="loginEmail" class="form-input" placeholder="your@email.com" autocomplete="email" required>
          </div>
          <p class="form-error" id="loginEmailErr"></p>
        </div>
        <div class="form-group">
          <label class="form-label" for="loginPassword">Password <span class="required">*</span></label>
          <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" id="loginPassword" class="form-input" placeholder="Enter your password" autocomplete="current-password" required>
            <button type="button" class="password-toggle" id="loginPwToggle" aria-label="Show password"><i class="fas fa-eye"></i></button>
          </div>
          <p class="form-error" id="loginPasswordErr"></p>
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" id="loginSubmitBtn">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
      </form>
      <div class="form-switch">No account? <a href="#" id="switchToSignup">Sign Up Here</a></div>
    </div>
  </div>
</div>

<!-- ═══ REGISTER MODAL ═══ -->
<div class="modal-overlay" id="registerModal" role="dialog" aria-modal="true" aria-labelledby="registerModalTitle">
  <div class="modal" style="max-width:600px;">
    <div class="modal-header">
      <div><h2 id="registerModalTitle">Create Your Account</h2><p>Join students learning online across Uganda</p></div>
      <button class="modal-close" onclick="closeModal('registerModal')" aria-label="Close"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="registerForm" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="regName">Full Name <span class="required">*</span></label>
            <input type="text" id="regName" class="form-input" placeholder="e.g. Sarah Nakato" autocomplete="name" required>
            <p class="form-error" id="regNameErr"></p>
          </div>
          <div class="form-group">
            <label class="form-label" for="regEmail">Email <span class="required">*</span></label>
            <input type="email" id="regEmail" class="form-input" placeholder="your@email.com" autocomplete="email" required>
            <p class="form-error" id="regEmailErr"></p>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="regPhone">Phone Number <span class="required">*</span></label>
            <input type="tel" id="regPhone" class="form-input" placeholder="07XX XXX XXX" autocomplete="tel" required>
            <p class="form-hint">Used for Mobile Money payment</p>
            <p class="form-error" id="regPhoneErr"></p>
          </div>
          <div class="form-group">
            <label class="form-label" for="regRegNumber">Student/Reg Number <span class="required">*</span></label>
            <input type="text" id="regRegNumber" class="form-input" placeholder="e.g. NUR22001" required>
            <p class="form-error" id="regRegErr"></p>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="regCourse">Course Enrolled <span class="required">*</span></label>
          <select id="regCourse" class="form-select" required>
            <option value="">— Select your course —</option>
            <option>Certificate in Nursing</option>
            <option>Diploma in Nursing (Extension)</option>
            <option>Diploma in Nursing (Direct)</option>
            <option>Diploma in Midwifery (Extension)</option>
            <option>Diploma in Midwifery (Direct)</option>
          </select>
          <p class="form-error" id="regCourseErr"></p>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="regYear">Year of Study <span class="required">*</span></label>
            <select id="regYear" class="form-select" required>
              <option value="">— Year —</option>
              <option>Year 1</option><option>Year 2</option><option>Year 3</option>
            </select>
            <p class="form-error" id="regYearErr"></p>
          </div>
          <div class="form-group">
            <label class="form-label" for="regSemester">Semester <span class="required">*</span></label>
            <select id="regSemester" class="form-select" required>
              <option value="">— Semester —</option>
              <option>Semester 1</option><option>Semester 2</option>
            </select>
            <p class="form-error" id="regSemErr"></p>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="regInstitution">Institution Name <span class="required">*</span></label>
          <input type="text" id="regInstitution" class="form-input" placeholder="e.g. Mulago School of Nursing" required>
          <p class="form-error" id="regInstErr"></p>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="regPassword">Password <span class="required">*</span></label>
            <div class="input-wrapper">
              <input type="password" id="regPassword" class="form-input" placeholder="Min. 8 characters" autocomplete="new-password" required>
              <button type="button" class="password-toggle" id="regPwToggle" aria-label="Show password"><i class="fas fa-eye"></i></button>
            </div>
            <div class="password-strength" id="pwStrength" style="display:none;">
              <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
              <span class="strength-text" id="strengthText"></span>
            </div>
            <p class="form-error" id="regPwErr"></p>
          </div>
          <div class="form-group">
            <label class="form-label" for="regConfirmPassword">Confirm Password <span class="required">*</span></label>
            <div class="input-wrapper">
              <input type="password" id="regConfirmPassword" class="form-input" placeholder="Repeat password" autocomplete="new-password" required>
              <button type="button" class="password-toggle" id="regConfPwToggle" aria-label="Show password"><i class="fas fa-eye"></i></button>
            </div>
            <p class="form-error" id="regConfPwErr"></p>
          </div>
        </div>
        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" id="regTerms" required>
            <label for="regTerms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</label>
          </div>
          <p class="form-error" id="regTermsErr"></p>
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" id="registerSubmitBtn">
          <i class="fas fa-user-plus"></i> Create Account
        </button>
      </form>
      <div class="form-switch">Have an account? <a href="#" id="switchToLogin">Sign In</a></div>
    </div>
  </div>
</div>

<script src="<?= asset_v('js/utils.js') ?>"></script>
<script src="<?= asset_v('js/auth.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>if (typeof pdfjsLib !== 'undefined') pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
<script src="<?= asset_v('js/pdf-viewer.js') ?>"></script>
<script src="<?= asset_v('js/notes-catalog.js') ?>"></script>
</body>
</html>
