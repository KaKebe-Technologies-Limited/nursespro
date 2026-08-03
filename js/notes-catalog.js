/**
 * NursesPro Academy - Public Notes Catalog Controller
 * Powers notes.php: search/filter across api/public_notes.php (no login),
 * and opens PDFs in preview mode via PDFViewer.open(id, title, {preview:true}).
 */

let ncModulesLoaded = false;
let ncNotesCache = [];
let ncRequestId = 0;

// Shared utils.js has no generic debounce (dashboard.js's is a local closure) —
// this one gives each call site its own timer so search/tutor inputs don't cancel each other.
function makeDebounce(fn, delay = 350) {
  let timer;
  return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
}

document.addEventListener('DOMContentLoaded', async () => {
  const user = await Auth.init();
  PDFViewer.init(user);
  initNavbar();
  initModalTriggers();
  initLoginForm();
  initRegisterForm();
  initFilters();
  checkAlreadyLoggedIn();
  loadNotes();
});

// ─── Modal Helpers ───────────────────────────────────────────────────────────
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('active'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('active'); document.body.style.overflow = ''; }
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('active');
    document.body.style.overflow = '';
  }
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.active').forEach(m => {
      m.classList.remove('active');
      document.body.style.overflow = '';
    });
  }
});

function openAuthModal(which) {
  PDFViewer.close();
  closeModal('loginModal'); closeModal('registerModal');
  openModal(which);
}

// ─── Navbar / Drawer ──────────────────────────────────────────────────────────
function initNavbar() {
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });
}

function initModalTriggers() {
  ['loginBtnNav', 'loginBtnMobile', 'pdfLockLoginBtn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', (e) => { e.preventDefault(); openAuthModal('loginModal'); });
  });
  ['signupBtnNav', 'signupBtnMobile', 'ctaSignupBtn', 'pdfLockSignupBtn'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', (e) => { e.preventDefault(); openAuthModal('registerModal'); });
  });
  document.getElementById('switchToLogin')?.addEventListener('click', (e) => { e.preventDefault(); closeModal('registerModal'); openModal('loginModal'); });
  document.getElementById('switchToSignup')?.addEventListener('click', (e) => { e.preventDefault(); closeModal('loginModal'); openModal('registerModal'); });

  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileOverlay = document.getElementById('mobileOverlay');
  const drawerClose = document.getElementById('drawerClose');

  function openMenu() {
    mobileMenu.classList.add('open'); hamburger.classList.add('open');
    hamburger.setAttribute('aria-expanded', 'true'); mobileOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    mobileMenu.classList.remove('open'); hamburger.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false'); mobileOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }
  hamburger?.addEventListener('click', () => mobileMenu.classList.contains('open') ? closeMenu() : openMenu());
  mobileOverlay?.addEventListener('click', closeMenu);
  drawerClose?.addEventListener('click', closeMenu);
  document.querySelectorAll('.hn-drawer-link, .hn-drawer .hn-btn-outline, .hn-drawer .hn-btn-solid').forEach(link => {
    link.addEventListener('click', closeMenu);
  });
}

function checkAlreadyLoggedIn() {
  const user = Auth.getCurrentUser();
  if (!user) return;
  const navLinks = document.getElementById('navLinks');
  if (navLinks) {
    navLinks.innerHTML += `
      <a href="${user.role !== 'student' ? 'admin-dashboard.php' : 'dashboard.php'}" class="btn btn-primary btn-sm">
        <i class="fas fa-tachometer-alt"></i> My Dashboard
      </a>`;
  }
}

// ─── Login / Register Forms ──────────────────────────────────────────────────
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

    if (!email.value.trim() || !validateEmail(email.value)) { showFieldError(email, 'Please enter a valid email address.'); valid = false; }
    else clearFieldError(email);
    if (!password.value || password.value.length < 6) { showFieldError(password, 'Please enter your password.'); valid = false; }
    else clearFieldError(password);
    if (!valid) return;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in…';
    const result = await Auth.login(email.value.trim(), password.value);
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';

    if (!result.success) { showFieldError(password, result.message); Toast.error(result.message); return; }
    Toast.success('Welcome back, ' + result.user.name.split(' ')[0] + '!');
    closeModal('loginModal');
    setTimeout(() => {
      window.location.href = ['superadmin', 'tutor'].includes(result.user.role) ? 'admin-dashboard.php' : 'dashboard.php';
    }, 800);
  });
}

function initRegisterForm() {
  const form = document.getElementById('registerForm');
  const pw = document.getElementById('regPassword');
  const pwStrength = document.getElementById('pwStrength');
  const strengthFill = document.getElementById('strengthFill');
  const strengthText = document.getElementById('strengthText');

  [['regPwToggle', 'regPassword'], ['regConfPwToggle', 'regConfirmPassword']].forEach(([btnId, inputId]) => {
    document.getElementById(btnId)?.addEventListener('click', () => {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(btnId).querySelector('i');
      if (input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash'; }
      else { input.type = 'password'; icon.className = 'fas fa-eye'; }
    });
  });

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
      { id: 'regName', err: 'regNameErr', check: v => v.length >= 3, msg: 'Full name must be at least 3 characters.' },
      { id: 'regEmail', err: 'regEmailErr', check: validateEmail, msg: 'Please enter a valid email address.' },
      { id: 'regPhone', err: 'regPhoneErr', check: validatePhone, msg: 'Enter a valid Ugandan phone number (07XX or 08XX).' },
      { id: 'regRegNumber', err: 'regRegErr', check: v => v.length >= 3, msg: 'Please enter your registration number.' },
      { id: 'regCourse', err: 'regCourseErr', check: v => v !== '', msg: 'Please select your course.' },
      { id: 'regYear', err: 'regYearErr', check: v => v !== '', msg: 'Please select your year of study.' },
      { id: 'regSemester', err: 'regSemErr', check: v => v !== '', msg: 'Please select your semester.' },
      { id: 'regInstitution', err: 'regInstErr', check: v => v.length >= 3, msg: 'Please enter your institution name.' },
      { id: 'regPassword', err: 'regPwErr', check: validatePassword, msg: 'Password must be at least 8 characters.' },
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

    const conf = document.getElementById('regConfirmPassword');
    const confErr = document.getElementById('regConfPwErr');
    if (conf.value !== document.getElementById('regPassword').value) {
      conf.classList.add('error'); confErr.textContent = 'Passwords do not match.'; confErr.classList.add('show'); valid = false;
    } else { conf.classList.remove('error'); confErr.classList.remove('show'); }

    const terms = document.getElementById('regTerms');
    const termsErr = document.getElementById('regTermsErr');
    if (!terms.checked) {
      termsErr.textContent = 'You must accept the Terms of Service to continue.'; termsErr.classList.add('show'); valid = false;
    } else termsErr.classList.remove('show');

    if (!valid) { Toast.warning('Please fix the errors above.'); return; }

    const submitBtn = document.getElementById('registerSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account…';

    const result = await Auth.register({
      name: document.getElementById('regName').value.trim(),
      email: document.getElementById('regEmail').value.trim().toLowerCase(),
      phone: document.getElementById('regPhone').value.trim(),
      regNumber: document.getElementById('regRegNumber').value.trim(),
      course: document.getElementById('regCourse').value,
      year: document.getElementById('regYear').value,
      semester: document.getElementById('regSemester').value,
      institution: document.getElementById('regInstitution').value.trim(),
      password: document.getElementById('regPassword').value,
    });

    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';

    if (!result.success) { Toast.error(result.message); return; }

    Toast.success('Account created! Taking you to your dashboard…');
    closeModal('registerModal');
    setTimeout(() => { window.location.href = 'dashboard.php'; }, 800);
  });
}

// ─── Filters & Search ─────────────────────────────────────────────────────────
const ncState = { q: '', module: '', topic: '', subtopic: '', year: '', semester: '', uploader: '' };

function initFilters() {
  document.getElementById('ncFiltersToggle')?.addEventListener('click', () => {
    document.getElementById('ncFilters')?.classList.toggle('open');
  });

  document.getElementById('ncSearchQuery')?.addEventListener('input', makeDebounce((e) => {
    ncState.q = e.target.value.trim();
    loadNotes();
  }));

  document.getElementById('ncFilterTutor')?.addEventListener('input', makeDebounce((e) => {
    ncState.uploader = e.target.value.trim();
    loadNotes();
  }));

  document.getElementById('ncFilterYear')?.addEventListener('change', (e) => { ncState.year = e.target.value; loadNotes(); });
  document.getElementById('ncFilterSemester')?.addEventListener('change', (e) => { ncState.semester = e.target.value; loadNotes(); });

  document.getElementById('ncFilterModule')?.addEventListener('change', async (e) => {
    ncState.module = e.target.value;
    ncState.topic = ''; ncState.subtopic = '';
    await loadTopicsForFilter(ncState.module);
    loadNotes();
  });
  document.getElementById('ncFilterTopic')?.addEventListener('change', async (e) => {
    ncState.topic = e.target.value;
    ncState.subtopic = '';
    populateSubtopicFilter(ncState.topic);
    loadNotes();
  });
  document.getElementById('ncFilterSubtopic')?.addEventListener('change', (e) => {
    ncState.subtopic = e.target.value;
    loadNotes();
  });

  document.getElementById('ncClearFilters')?.addEventListener('click', () => {
    ncState.q = ''; ncState.module = ''; ncState.topic = ''; ncState.subtopic = '';
    ncState.year = ''; ncState.semester = ''; ncState.uploader = '';
    document.getElementById('ncSearchQuery').value = '';
    document.getElementById('ncFilterTutor').value = '';
    document.getElementById('ncFilterYear').value = '';
    document.getElementById('ncFilterSemester').value = '';
    document.getElementById('ncFilterModule').value = '';
    resetSelect('ncFilterTopic', 'All Topics');
    resetSelect('ncFilterSubtopic', 'All Subtopics');
    loadNotes();
  });
}

function resetSelect(id, placeholder) {
  const sel = document.getElementById(id);
  if (sel) sel.innerHTML = `<option value="">${placeholder}</option>`;
}

let ncCurrentTopics = [];
async function loadTopicsForFilter(moduleCode) {
  resetSelect('ncFilterTopic', 'All Topics');
  resetSelect('ncFilterSubtopic', 'All Subtopics');
  ncCurrentTopics = [];
  if (!moduleCode) return;
  const res = await fetch('api/topics.php?module=' + encodeURIComponent(moduleCode)).then(r => r.json());
  ncCurrentTopics = res.topics || [];
  const sel = document.getElementById('ncFilterTopic');
  if (sel) {
    ncCurrentTopics.forEach(t => {
      sel.insertAdjacentHTML('beforeend', `<option value="${t.id}">${sanitize(t.title)}</option>`);
    });
  }
}

function populateSubtopicFilter(topicId) {
  resetSelect('ncFilterSubtopic', 'All Subtopics');
  const topic = ncCurrentTopics.find(t => String(t.id) === String(topicId));
  const sel = document.getElementById('ncFilterSubtopic');
  if (!sel || !topic) return;
  (topic.subtopics || []).forEach(s => {
    sel.insertAdjacentHTML('beforeend', `<option value="${s.id}">${sanitize(s.title)}</option>`);
  });
}

function populateModuleFilter(notes) {
  if (ncModulesLoaded) return;
  const sel = document.getElementById('ncFilterModule');
  if (!sel) return;
  const seen = new Map();
  notes.forEach(n => { if (n.module && !seen.has(n.module)) seen.set(n.module, n.module_title); });
  [...seen.entries()].sort((a, b) => a[0].localeCompare(b[0])).forEach(([code, title]) => {
    sel.insertAdjacentHTML('beforeend', `<option value="${sanitize(code)}">${sanitize(code)} — ${sanitize(title || '')}</option>`);
  });
  ncModulesLoaded = true;
}

async function loadNotes() {
  const grid = document.getElementById('ncNotesGrid');
  const countEl = document.getElementById('ncResultsCount');
  if (!grid) return;
  const thisRequestId = ++ncRequestId;

  const params = new URLSearchParams();
  if (ncState.q) params.set('q', ncState.q);
  if (ncState.module) params.set('module', ncState.module);
  if (ncState.topic) params.set('topic', ncState.topic);
  if (ncState.subtopic) params.set('subtopic', ncState.subtopic);
  if (ncState.year) params.set('year', ncState.year);
  if (ncState.semester) params.set('semester', ncState.semester);
  if (ncState.uploader) params.set('uploader', ncState.uploader);

  const res = await fetch('api/public_notes.php?' + params.toString()).then(r => r.json());
  if (thisRequestId !== ncRequestId) return;

  const notes = res.notes || [];
  ncNotesCache = notes;

  if (!ncModulesLoaded) {
    const allRes = params.toString() ? await fetch('api/public_notes.php').then(r => r.json()) : res;
    if (thisRequestId !== ncRequestId) return;
    populateModuleFilter(allRes.notes || notes);
  }

  if (countEl) countEl.textContent = `${notes.length} note${notes.length === 1 ? '' : 's'} found`;

  if (!notes.length) {
    grid.innerHTML = `<p style="grid-column:1/-1;text-align:center;color:#7a8a9a;padding:40px 0;">
      No notes match your search. Try a different keyword or clear your filters.
    </p>`;
    return;
  }

  grid.innerHTML = notes.map(n => {
    const breadcrumb = [n.module, n.topic_title, n.subtopic_title].filter(Boolean).join(' › ');
    return `
    <div class="note-card" data-note-id="${n.id}">
      <div class="note-card-header">
        <span class="note-type-badge nursing">${sanitize(n.module || 'General')}</span>
      </div>
      <h4>${sanitize(n.title)}</h4>
      <p style="font-size:0.78rem;color:#7a8a9a;margin-bottom:6px;">${sanitize(breadcrumb)}</p>
      <p>${sanitize(n.description || '')}</p>
      <div class="note-card-footer">
        <span class="note-views"><i class="fas fa-eye"></i> ${n.views || 0} views</span>
        <span><i class="fas fa-calendar-alt"></i> ${formatDate(n.created_at)}</span>
      </div>
      ${n.uploader_name ? `<div style="font-size:0.76rem;color:#7a8a9a;margin-top:6px;"><i class="fas fa-user-md" aria-hidden="true"></i> ${sanitize(n.uploader_name)}</div>` : ''}
      <div class="note-card-preview-hint"><i class="fas fa-unlock-alt" aria-hidden="true"></i> Free preview — read now</div>
    </div>`;
  }).join('');

  grid.querySelectorAll('.note-card').forEach(card => {
    card.addEventListener('click', () => {
      const note = ncNotesCache.find(n => String(n.id) === card.dataset.noteId);
      if (note) PDFViewer.open(note.id, note.title, { preview: true });
    });
  });
}
