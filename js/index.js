/**
 * NursesPro Academy - Index Page Controller
 */

document.addEventListener('DOMContentLoaded', async () => {
  await Promise.all([Auth.init(), initCurriculum()]);
  initNavbar();
  initModalTriggers();
  initLoginForm();
  initRegisterForm();
  initPaymentModal();
  initSmoothScroll();
  initDemoAccountHints();
  checkAlreadyLoggedIn();
});

// ─── Demo Account Hints (hover to autofill login form) ─────────────────────────
function initDemoAccountHints() {
  const email = document.getElementById('loginEmail');
  const password = document.getElementById('loginPassword');
  if (!email || !password) return;

  document.querySelectorAll('.demo-account-item').forEach(item => {
    item.setAttribute('tabindex', '0');
    item.setAttribute('role', 'button');
    const fill = () => {
      email.value = item.dataset.email;
      password.value = item.dataset.password;
      clearFieldError(email);
      clearFieldError(password);
    };
    item.addEventListener('mouseenter', fill);
    item.addEventListener('focus', fill);
    item.addEventListener('click', fill);
  });
}

// ─── Navbar Scroll ────────────────────────────────────────────────────────────
function initNavbar() {
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });
}

// ─── Modal Helpers ─────────────────────────────────────────────────────────────
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('active'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('active'); document.body.style.overflow = ''; }
}

// Close on overlay click
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('active');
    document.body.style.overflow = '';
  }
});
// Close on Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.active').forEach(m => {
      m.classList.remove('active');
      document.body.style.overflow = '';
    });
  }
});

// ─── Modal Triggers ───────────────────────────────────────────────────────────
function initModalTriggers() {
  // Login buttons
  ['loginBtnNav','loginBtnMobile','heroLoginBtn','footerLoginLink','switchToLogin'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', (e) => {
      e.preventDefault(); closeModal('registerModal'); openModal('loginModal');
    });
  });
  // Signup buttons
  ['signupBtnNav','signupBtnMobile','heroSignupBtn','switchToSignup'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', (e) => {
      e.preventDefault();
      const user = Auth.getCurrentUser();
      if (user) { openPaymentModal(); return; }
      closeModal('loginModal'); openModal('registerModal');
    });
  });

  // Hamburger / mobile drawer
  const hamburger    = document.getElementById('hamburger');
  const mobileMenu   = document.getElementById('mobileMenu');
  const mobileOverlay= document.getElementById('mobileOverlay');
  const drawerClose  = document.getElementById('drawerClose');

  function openMenu() {
    mobileMenu.classList.add('open');
    hamburger.classList.add('open');
    hamburger.setAttribute('aria-expanded', 'true');
    mobileOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    mobileMenu.classList.remove('open');
    hamburger.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    mobileOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }
  function toggleMenu() { mobileMenu.classList.contains('open') ? closeMenu() : openMenu(); }

  hamburger    ?.addEventListener('click', toggleMenu);
  mobileOverlay?.addEventListener('click', closeMenu);
  drawerClose  ?.addEventListener('click', closeMenu);

  // Close drawer on any internal link click
  document.querySelectorAll('.hn-drawer-link, .hn-drawer .hn-btn-outline, .hn-drawer .hn-btn-solid').forEach(link => {
    link.addEventListener('click', closeMenu);
  });

  // Course pills/cards are plain links to notes.php — no login required to browse.

  function openPaymentModal() {
    const user = Auth.getCurrentUser();
    const modal = document.getElementById('paymentModal');
    if (!modal) return;
    if (!user) { openModal('loginModal'); return; }
    const phoneEl = document.getElementById('paymentPhone');
    if (phoneEl && user.phone) phoneEl.value = user.phone;
    const stepForm = document.getElementById('paymentStepForm');
    const stepProc = document.getElementById('paymentStepProcessing');
    const stepSucc = document.getElementById('paymentStepSuccess');
    if (stepForm) stepForm.style.display = 'block';
    if (stepProc) stepProc.style.display = 'none';
    if (stepSucc) stepSucc.style.display = 'none';
    openModal('paymentModal');
  }
}

// ─── Login Form ───────────────────────────────────────────────────────────────
function initLoginForm() {
  const form = document.getElementById('loginForm');
  const pwToggle = document.getElementById('loginPwToggle');

  pwToggle?.addEventListener('click', () => {
    const pw = document.getElementById('loginPassword');
    const icon = pwToggle.querySelector('i');
    if (pw.type === 'password') { pw.type = 'text'; icon.className = 'fas fa-eye-slash'; }
    else { pw.type = 'password'; icon.className = 'fas fa-eye'; }
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    let valid = true;
    const email = document.getElementById('loginEmail');
    const password = document.getElementById('loginPassword');
    const submitBtn = document.getElementById('loginSubmitBtn');

    if (!email.value.trim() || !validateEmail(email.value)) {
      showFieldError(email, 'Please enter a valid email address.');
      valid = false;
    } else clearFieldError(email);

    if (!password.value || password.value.length < 6) {
      showFieldError(password, 'Please enter your password.');
      valid = false;
    } else clearFieldError(password);

    if (!valid) return;

    // Loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in…';

    const result = await Auth.login(email.value.trim(), password.value);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';

    if (!result.success) {
      showFieldError(password, result.message);
      Toast.error(result.message);
      return;
    }
    Toast.success('Welcome back, ' + result.user.name.split(' ')[0] + '!');
    closeModal('loginModal');

    setTimeout(() => {
      if (['superadmin','tutor'].includes(result.user.role)) {
        window.location.href = 'admin-dashboard.php';
      } else {
        window.location.href = 'dashboard.php';
      }
    }, 800);
  });
}

// ─── Register Form ────────────────────────────────────────────────────────────
function initRegisterForm() {
  const form = document.getElementById('registerForm');
  const pw   = document.getElementById('regPassword');
  const pwStrength = document.getElementById('pwStrength');
  const strengthFill = document.getElementById('strengthFill');
  const strengthText = document.getElementById('strengthText');

  // Modules preview — updates whenever course/year/semester line up with real curriculum data
  const courseSel = document.getElementById('regCourse');
  const yearSel = document.getElementById('regYear');
  const semSel = document.getElementById('regSemester');
  const preview = document.getElementById('regModulesPreview');

  function updateModulesPreview() {
    if (!preview || typeof getCurriculumModules !== 'function') return;
    const modules = getCurriculumModules(courseSel.value, yearSel.value, semSel.value);
    if (!modules) { preview.style.display = 'none'; preview.innerHTML = ''; return; }
    preview.style.display = 'block';
    preview.innerHTML = `<strong style="color:var(--primary-blue);">Modules you'll be taking:</strong>
      <ul style="margin:8px 0 0;padding-left:18px;line-height:1.7;">
        ${modules.map(m => `<li>${sanitize(m.code)} — ${sanitize(m.title)}</li>`).join('')}
      </ul>`;
  }
  [courseSel, yearSel, semSel].forEach(el => el?.addEventListener('change', updateModulesPreview));

  // Password toggle helpers
  [['regPwToggle','regPassword'],['regConfPwToggle','regConfirmPassword']].forEach(([btnId, inputId]) => {
    document.getElementById(btnId)?.addEventListener('click', () => {
      const input = document.getElementById(inputId);
      const icon  = document.getElementById(btnId).querySelector('i');
      if (input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash'; }
      else { input.type = 'password'; icon.className = 'fas fa-eye'; }
    });
  });

  // Password strength meter
  pw?.addEventListener('input', () => {
    if (!pw.value) { pwStrength.style.display = 'none'; return; }
    pwStrength.style.display = 'block';
    const s = getPasswordStrength(pw.value);
    strengthFill.style.width = s.width;
    strengthFill.style.background = s.color;
    strengthText.textContent = s.text;
    strengthText.style.color = s.color;
  });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    let valid = true;

    const fields = [
      { id:'regName', err:'regNameErr', check: v => v.length >= 3, msg:'Full name must be at least 3 characters.' },
      { id:'regEmail', err:'regEmailErr', check: validateEmail, msg:'Please enter a valid email address.' },
      { id:'regPhone', err:'regPhoneErr', check: validatePhone, msg:'Enter a valid Ugandan phone number (07XX or 08XX).' },
      { id:'regRegNumber', err:'regRegErr', check: v => v.length >= 3, msg:'Please enter your registration number.' },
      { id:'regCourse', err:'regCourseErr', check: v => v !== '', msg:'Please select your course.' },
      { id:'regYear', err:'regYearErr', check: v => v !== '', msg:'Please select your year of study.' },
      { id:'regSemester', err:'regSemErr', check: v => v !== '', msg:'Please select your semester.' },
      { id:'regInstitution', err:'regInstErr', check: v => v.length >= 3, msg:'Please enter your institution name.' },
      { id:'regPassword', err:'regPwErr', check: validatePassword, msg:'Password must be at least 8 characters.' },
    ];

    fields.forEach(f => {
      const input = document.getElementById(f.id);
      const errEl = document.getElementById(f.err);
      if (!f.check(input.value.trim())) {
        input.classList.add('error');
        if (errEl) { errEl.textContent = f.msg; errEl.classList.add('show'); }
        valid = false;
      } else {
        input.classList.remove('error');
        if (errEl) errEl.classList.remove('show');
      }
    });

    // Confirm password
    const conf = document.getElementById('regConfirmPassword');
    const confErr = document.getElementById('regConfPwErr');
    if (conf.value !== document.getElementById('regPassword').value) {
      conf.classList.add('error');
      confErr.textContent = 'Passwords do not match.';
      confErr.classList.add('show');
      valid = false;
    } else { conf.classList.remove('error'); confErr.classList.remove('show'); }

    // Terms
    const terms = document.getElementById('regTerms');
    const termsErr = document.getElementById('regTermsErr');
    if (!terms.checked) {
      termsErr.textContent = 'You must accept the Terms of Service to continue.';
      termsErr.classList.add('show');
      valid = false;
    } else termsErr.classList.remove('show');

    if (!valid) { Toast.warning('Please fix the errors above.'); return; }

    const submitBtn = document.getElementById('registerSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account…';

    const result = await Auth.register({
      name:        document.getElementById('regName').value.trim(),
      email:       document.getElementById('regEmail').value.trim().toLowerCase(),
      phone:       document.getElementById('regPhone').value.trim(),
      regNumber:   document.getElementById('regRegNumber').value.trim(),
      course:      document.getElementById('regCourse').value,
      year:        document.getElementById('regYear').value,
      semester:    document.getElementById('regSemester').value,
      institution: document.getElementById('regInstitution').value.trim(),
      password:    document.getElementById('regPassword').value,
    });

    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';

    if (!result.success) {
      Toast.error(result.message);
      return;
    }

    Toast.success('Account created! Redirecting to payment…');
    closeModal('registerModal');

    // Show payment modal
    setTimeout(() => {
      const phoneEl = document.getElementById('paymentPhone');
      if (phoneEl) phoneEl.value = result.user.phone;
      openModal('paymentModal');
    }, 600);
  });
}

// ─── Smooth Scroll ────────────────────────────────────────────────────────────
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
      const href = a.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
}

// ─── Already Logged In ────────────────────────────────────────────────────────
function checkAlreadyLoggedIn() {
  const user = Auth.getCurrentUser();
  if (!user) return;
  // Update nav to show dashboard link
  const navLinks = document.getElementById('navLinks');
  if (navLinks) {
    navLinks.innerHTML += `
      <a href="${user.role !== 'student' ? 'admin-dashboard.php' : 'dashboard.php'}" class="btn btn-primary btn-sm">
        <i class="fas fa-tachometer-alt"></i> My Dashboard
      </a>`;
  }
}
