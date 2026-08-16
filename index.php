<?php
require_once __DIR__ . '/includes/auth_guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="NursesPro Academy – Quality online nursing and midwifery education for Ugandan students. PDF notes, live classes, and tutor support. Create a free account to see pricing.">
  <meta property="og:title" content="NursesPro Academy – Study Smart. Learn Anywhere.">
  <meta property="og:description" content="Access nursing & midwifery notes, live Telegram classes and exam revision. Create a free account to see pricing.">
  <title>NursesPro Academy – Study Smart. Learn Anywhere.</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= asset_v('css/style.css') ?>">
  <link rel="stylesheet" href="<?= asset_v('css/home.css') ?>">
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
      <a href="#courses" class="hn-link">Courses</a>
      <a href="notes.php" class="hn-link">Browse Notes</a>
      <a href="#features" class="hn-link">What You Get</a>
      <a href="#contact" class="hn-link">Contact</a>
      <a href="#" class="hn-btn-outline" id="loginBtnNav">Login</a>
      <a href="#" class="hn-btn-solid" id="signupBtnNav">Sign Up</a>
    </div>
    <button class="hn-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>
<!-- Mobile drawer -->
<div class="hn-drawer-overlay" id="mobileOverlay"></div>
<div class="hn-drawer" id="mobileMenu" role="dialog" aria-label="Mobile navigation">
  <div class="hn-drawer-header">
    <img src="assets/images/logo-nav.svg" alt="NursesPro Academy" height="36">
    <button class="hn-drawer-close" id="drawerClose" aria-label="Close menu"><i class="fas fa-times"></i></button>
  </div>
  <a href="#courses" class="hn-drawer-link">Courses</a>
  <a href="notes.php" class="hn-drawer-link">Browse Notes</a>
  <a href="#features" class="hn-drawer-link">What You Get</a>
  <a href="#contact" class="hn-drawer-link">Contact</a>
  <hr class="hn-drawer-divider">
  <a href="#" class="hn-btn-outline" id="loginBtnMobile" style="text-align:center;display:block;">Login</a>
  <a href="#" class="hn-btn-solid" id="signupBtnMobile" style="text-align:center;display:block;margin-top:10px;">Sign Up Free</a>
</div>

<!-- ═══ HERO ═══ -->
<section class="hn-hero" id="hero">
  <div class="hn-hero-inner">
    <div class="hn-hero-label">WELCOME TO NURSESPRO ACADEMY</div>
    <h1 class="hn-hero-title">Excel in Nursing with<br><span class="hn-teal">Uganda's #1 Platform</span></h1>
    <p class="hn-hero-sub">
      Secure study notes and live classes for nursing and midwifery students.
    </p>
    <div class="hn-hero-cta">
      <button class="hn-btn-solid hn-btn-lg hn-pulse" id="heroSignupBtn">
        <i class="fas fa-rocket"></i> Get Started Free
      </button>
      <button class="hn-btn-outline hn-btn-lg" id="heroLoginBtn">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </div>
    <!-- Course quick-links exactly like nursesrevisionuganda.com -->
    <div class="hn-course-links">
      <a href="notes.php" class="hn-course-pill" id="pill-cert">Certificate in Nursing ›</a>
      <a href="notes.php" class="hn-course-pill" id="pill-ext">Diploma – Extension ›</a>
      <a href="notes.php" class="hn-course-pill" id="pill-direct">Diploma – Direct ›</a>
      <a href="notes.php" class="hn-course-pill" id="pill-mid">Diploma in Midwifery ›</a>
    </div>
  </div>
</section>

<!-- ═══ QUICK FEATURE ICONS ═══ -->
<section class="hn-icons-row" aria-label="Quick features">
  <div class="hn-icons-inner">
    <div class="hn-icon-item">
      <div class="hn-icon-box"><i class="fas fa-file-pdf"></i></div>
      <span>Study<br>Notes</span>
    </div>
    <div class="hn-icon-item">
      <div class="hn-icon-box"><i class="fab fa-telegram-plane"></i></div>
      <span>Live<br>Classes</span>
    </div>
    <div class="hn-icon-item">
      <div class="hn-icon-box"><i class="fas fa-question-circle"></i></div>
      <span>Questions<br>&amp; Answers</span>
    </div>
    <div class="hn-icon-item">
      <div class="hn-icon-box"><i class="fas fa-pencil-alt"></i></div>
      <span>Exam<br>Revision</span>
    </div>
    <div class="hn-icon-item">
      <div class="hn-icon-box"><i class="fas fa-user-md"></i></div>
      <span>Expert<br>Tutors</span>
    </div>
    <div class="hn-icon-item">
      <div class="hn-icon-box"><i class="fas fa-mobile-alt"></i></div>
      <span>Mobile<br>Friendly</span>
    </div>
  </div>
</section>

<!-- ═══ COURSES ═══ -->
<section class="hn-section" id="courses">
  <div class="hn-container">
    <h2 class="hn-section-title">Browse by <span class="hn-teal">Course</span></h2>
    <p class="hn-section-sub">Pick your programme to get started.</p>
    <div class="hn-courses-grid">
      <div class="hn-course-card">
        <div class="hn-course-icon"><i class="fas fa-graduation-cap"></i></div>
        <h3>Certificate in Nursing</h3>
        <p>Foundation-level nursing.</p>
        <a href="notes.php" class="hn-card-link" id="courseCert">Access Notes ›</a>
      </div>
      <div class="hn-course-card hn-course-card--featured">
        <div class="hn-course-icon"><i class="fas fa-hospital-user"></i></div>
        <div class="hn-featured-tag">Most Popular</div>
        <h3>Diploma in Nursing – Direct</h3>
        <p>Full-time direct-entry diploma.</p>
        <a href="notes.php" class="hn-card-link" id="courseDirect">Access Notes ›</a>
      </div>
      <div class="hn-course-card">
        <div class="hn-course-icon"><i class="fas fa-user-nurse"></i></div>
        <h3>Diploma in Nursing – Extension</h3>
        <p>Part-time, for working health workers.</p>
        <a href="notes.php" class="hn-card-link" id="courseExt">Access Notes ›</a>
      </div>
      <div class="hn-course-card">
        <div class="hn-course-icon"><i class="fas fa-baby"></i></div>
        <h3>Diploma in Midwifery</h3>
        <p>Maternal and child health.</p>
        <a href="notes.php" class="hn-card-link" id="courseMid">Access Notes ›</a>
      </div>
    </div>
  </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="hn-section hn-section--tinted" id="how-it-works">
  <div class="hn-container">
    <h2 class="hn-section-title">How It <span class="hn-teal">Works</span></h2>
    <p class="hn-section-sub">Three steps to full access.</p>
    <div class="hn-steps">
      <div class="hn-step">
        <div class="hn-step-num">1</div>
        <h4>Create Account</h4>
        <p>Takes under 2 minutes.</p>
      </div>
      <div class="hn-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="hn-step">
        <div class="hn-step-num">2</div>
        <h4>Make Payment</h4>
        <p>Mobile Money or card, via Pesapal.</p>
      </div>
      <div class="hn-step-arrow"><i class="fas fa-chevron-right"></i></div>
      <div class="hn-step">
        <div class="hn-step-num">3</div>
        <h4>Start Learning</h4>
        <p>Notes and live classes, 6 months.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ WHAT YOU GET ═══ -->
<section class="hn-section" id="features">
  <div class="hn-container">
    <h2 class="hn-section-title">What You <span class="hn-teal">Get</span></h2>
    <p class="hn-section-sub">Everything in one subscription.</p>
    <div class="hn-features-grid">
      <div class="hn-feature">
        <i class="fas fa-file-pdf hn-feature-icon"></i>
        <div>
          <h4>Secure Study Notes</h4>
          <p>Organised by course and year.</p>
        </div>
      </div>
      <div class="hn-feature">
        <i class="fab fa-telegram-plane hn-feature-icon"></i>
        <div>
          <h4>Live Telegram Classes</h4>
          <p>Interactive sessions with tutors.</p>
        </div>
      </div>
      <div class="hn-feature">
        <i class="fas fa-pencil-alt hn-feature-icon"></i>
        <div>
          <h4>Exam Revision</h4>
          <p>Past papers and practice quizzes.</p>
        </div>
      </div>
      <div class="hn-feature">
        <i class="fas fa-user-md hn-feature-icon"></i>
        <div>
          <h4>Expert Tutor Support</h4>
          <p>Nursing and midwifery professionals.</p>
        </div>
      </div>
      <div class="hn-feature">
        <i class="fas fa-mobile-alt hn-feature-icon"></i>
        <div>
          <h4>Learn Anywhere</h4>
          <p>Phone, tablet, or computer.</p>
        </div>
      </div>
      <div class="hn-feature">
        <i class="fas fa-lock hn-feature-icon"></i>
        <div>
          <h4>Secure &amp; Private</h4>
          <p>Watermarked, download-protected notes.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CONTACT STRIP ═══ -->
<section class="hn-contact-strip" id="contact">
  <div class="hn-container">
    <div class="hn-contact-inner">
      <div class="hn-contact-text">
        <h3>Need help?</h3>
        <p>Talk to us directly.</p>
      </div>
      <div class="hn-contact-actions">
        <a href="https://wa.me/256392972444" class="hn-contact-btn hn-wa" target="_blank" rel="noopener noreferrer">
          <i class="fab fa-whatsapp"></i> 0392 972 444
        </a>
        <a href="https://wa.me/256760167722" class="hn-contact-btn hn-wa" target="_blank" rel="noopener noreferrer">
          <i class="fab fa-whatsapp"></i> 0760 167 722
        </a>
        <a href="tel:+256392972444" class="hn-contact-btn hn-call">
          <i class="fas fa-phone"></i> Call Us
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FOOTER ═══ -->
<footer class="hn-footer">
  <div class="hn-container">
    <div class="hn-footer-grid">
      <div class="hn-footer-brand">
        <img src="assets/images/logo-nav.svg" alt="NursesPro Academy" height="38" style="filter:brightness(0) invert(1);opacity:.85;">
        <p>Nursing and midwifery education for Ugandan students.</p>
        <div class="hn-footer-socials">
          <a href="https://wa.me/256392972444" aria-label="WhatsApp" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i></a>
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
          <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
      <div class="hn-footer-col">
        <h5>Courses</h5>
        <a href="notes.php">Certificate in Nursing</a>
        <a href="notes.php">Diploma – Direct</a>
        <a href="notes.php">Diploma – Extension</a>
        <a href="notes.php">Diploma in Midwifery</a>
      </div>
      <div class="hn-footer-col">
        <h5>Platform</h5>
        <a href="#features">What You Get</a>
        <a href="notes.php">Browse Notes</a>
        <a href="#" id="footerLoginLink">Student Login</a>
        <a href="#">FAQ</a>
      </div>
      <div class="hn-footer-col">
        <h5>Legals</h5>
        <a href="#">Terms of Service</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Disclaimer</a>
        <a href="#">About Us</a>
      </div>
    </div>
    <div class="hn-footer-bottom">
      <p>© <?= date('Y') ?> NursesPro Academy. All rights reserved.</p>
      <p>Kampala, Uganda · <a href="tel:+256392972444">0392 972 444</a> · <a href="tel:+256760167722">0760 167 722</a></p>
      <p class="hn-footer-credit">Powered by Kakebe Technologies</p>
    </div>
  </div>
</footer>

<!-- ═══════════════════════════════════
     LOGIN MODAL
════════════════════════════════════ -->
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
        <div style="text-align:right;margin-bottom:20px;">
          <a href="#" style="font-size:0.85rem;color:var(--accent-teal);">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" id="loginSubmitBtn">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
      </form>
      <div style="background:#f0f8ff;border-radius:8px;padding:12px;margin-top:16px;font-size:0.82rem;">
        <strong>Demo Accounts</strong><br>
        <div id="demoAccountList" style="margin-top:6px;">
          <div class="demo-account-item" data-email="admin@nursespro.ac.ug" data-password="Admin1234">Admin: admin@nursespro.ac.ug / Admin1234</div>
          <div class="demo-account-item" data-email="john@demo.com" data-password="Tutor1234">Tutor: john@demo.com / Tutor1234</div>
          <div class="demo-account-item" data-email="sarah@demo.com" data-password="Student1234">Student: sarah@demo.com / Student1234</div>
        </div>
      </div>
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

<!-- ═══ PAYMENT MODAL ═══ -->
<div class="modal-overlay" id="paymentModal" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
  <div class="modal">
    <div class="modal-header">
      <div><h2 id="paymentModalTitle">Complete Payment</h2><p>Activate your 6-month access</p></div>
      <button class="modal-close" onclick="closeModal('paymentModal')" aria-label="Close"><i class="fas fa-times"></i></button>
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
            <i class="fas fa-phone input-icon"></i>
            <input type="tel" id="paymentPhone" class="form-input" placeholder="07XX XXX XXX">
          </div>
        </div>
        <div class="payment-steps-info">
          You'll be redirected to <strong>Pesapal</strong>'s secure payment page — pay via MTN, Airtel, or card.
        </div>
        <button class="btn btn-primary btn-full btn-lg" id="confirmPaymentBtn" style="margin-top:20px;">
          <i class="fas fa-lock"></i> Continue to Pesapal
        </button>
        <p style="text-align:center;margin-top:10px;font-size:0.8rem;color:#7a8a9a;">
          <i class="fas fa-shield-alt" style="color:var(--success-green)"></i> Secured by Pesapal
        </p>
      </div>
      <div id="paymentStepProcessing" style="display:none;flex-direction:column;align-items:center;padding:40px 0;gap:16px;">
        <div style="width:56px;height:56px;border:4px solid var(--light-blue);border-top-color:var(--primary-blue);border-radius:50%;animation:spin 1s linear infinite;"></div>
        <p style="font-weight:600;color:var(--primary-blue);" id="paymentProcessingText">Redirecting to Pesapal…</p>
        <p style="font-size:0.85rem;color:#5a6a7a;">Please wait. Do not close this window.</p>
      </div>
      <div id="paymentStepSuccess" style="display:none;">
        <div class="payment-success">
          <div class="success-icon"><i class="fas fa-check"></i></div>
          <h3 style="color:var(--success-green);margin-bottom:8px;">Payment Successful!</h3>
          <p style="color:#5a6a7a;margin-bottom:20px;">Your 6-month access is now active.</p>
          <div style="background:var(--light-blue);border-radius:8px;padding:16px;margin-bottom:20px;">
            <strong>Access valid until:</strong><br>
            <span id="paymentExpiryDate" style="color:var(--primary-blue);font-weight:700;font-size:1.1rem;"></span>
          </div>
          <button class="btn btn-primary btn-full btn-lg" id="goToDashboardBtn">
            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

<script src="<?= asset_v('js/utils.js') ?>"></script>
<script src="<?= asset_v('js/curriculum.js') ?>"></script>
<script src="<?= asset_v('js/auth.js') ?>"></script>
<script src="<?= asset_v('js/payment.js') ?>"></script>
<script src="<?= asset_v('js/index.js') ?>"></script>
</body>
</html>
