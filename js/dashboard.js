/**
 * NursesPro Academy - Student Dashboard
 * Notes/Classes are fetched live from api/notes.php / api/classes.php — the
 * same endpoints the admin/tutor "Upload New Note" / "Schedule Class" forms
 * write to, so newly published content shows up here immediately.
 */

let activeNotesModule = 'all';
let activeClassesModule = 'all';
let notesSearchQuery = '';
let notesSearchTutor = '';

// ─── Dashboard Init ───────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  const [user] = await Promise.all([Auth.guardPage(), initCurriculum()]);
  if (!user) return;

  if (user.role === 'student' && !Auth.hasActiveAccess()) {
    showPaywall(user);
    return;
  }

  initDashboard(user);
  initSidebar();
  initNavigation();
  PDFViewer.init(user);
  MyNotes.init(user);
  initPaymentModal();
  initAcademicDetailsForm();
  initNotesSearch();
  showPage('dashboard');
  checkAccessStatus(user);
  buildNotifications(user);
});

// ─── Study Notes Search ─────────────────────────────────────────────────────
function initNotesSearch() {
  let debounceTimer;
  const debounce = (fn) => { clearTimeout(debounceTimer); debounceTimer = setTimeout(fn, 350); };

  const searchInput = document.getElementById('notesSearch');
  const tutorInput = document.getElementById('notesSearchTutor');

  searchInput && searchInput.addEventListener('input', () => {
    debounce(() => { notesSearchQuery = searchInput.value.trim(); renderNotesSection(); });
  });
  tutorInput && tutorInput.addEventListener('input', () => {
    debounce(() => { notesSearchTutor = tutorInput.value.trim(); renderNotesSection(); });
  });
}

// ─── Paywall (blocks the whole portal until a student has active access) ──────
function showPaywall(user) {
  const wrapper = document.getElementById('dashboardWrapper');
  if (wrapper) wrapper.style.display = 'none';
  const overlay = document.getElementById('paywallOverlay');
  if (!overlay) return;
  overlay.style.display = 'flex';

  const phoneInput = document.getElementById('paywallPhone');
  if (phoneInput && user.phone) phoneInput.value = user.phone;

  const payBtn = document.getElementById('paywallPayBtn');
  payBtn && payBtn.addEventListener('click', async () => {
    const phone = phoneInput ? phoneInput.value.trim() : '';
    if (!phone || !validatePhone(phone)) { Toast.error('Please enter a valid phone number.'); return; }

    payBtn.disabled = true;
    payBtn.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Processing…';

    const res = await Payment.startPayment(phone);

    if (!res.success) {
      Toast.error(res.message || 'Could not start payment. Please try again.');
      payBtn.disabled = false;
      payBtn.innerHTML = '<i class="fas fa-credit-card" aria-hidden="true"></i> Pay Now';
      return;
    }

    if (res.mode === 'pesapal') {
      window.location.href = res.redirect_url;
      return;
    }

    // Demo mode: access was granted instantly server-side.
    Toast.success(res.message || 'Payment successful! Access granted.');
    setTimeout(() => location.reload(), 1200);
  });

  document.getElementById('paywallLogoutBtn')?.addEventListener('click', () => Auth.logout());

  const historyBtn = document.getElementById('paywallHistoryBtn');
  const historySection = document.getElementById('paywallHistorySection');
  let historyLoaded = false;
  historyBtn && historySection && historyBtn.addEventListener('click', async () => {
    const showing = historySection.style.display !== 'none';
    if (showing) {
      historySection.style.display = 'none';
      historyBtn.innerHTML = '<i class="fas fa-receipt" aria-hidden="true"></i> View Payment History';
      return;
    }
    historySection.style.display = 'block';
    historyBtn.innerHTML = '<i class="fas fa-chevron-up" aria-hidden="true"></i> Hide Payment History';
    if (!historyLoaded) {
      historyLoaded = true;
      await renderPaywallPayments();
    }
  });
}

async function renderPaywallPayments() {
  const table = document.getElementById('paywallPaymentsTableBody');
  if (!table) return;
  const payments = await Payment.getPayments();
  if (!payments.length) {
    table.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#7a8a9a;padding:20px;">No payment records found.</td></tr>';
    return;
  }
  table.innerHTML = payments.map(p => `
    <tr>
      <td><strong>${sanitize(p.reference)}</strong></td>
      <td>UGX ${Number(p.amount).toLocaleString()}</td>
      <td>${sanitize(p.method)}</td>
      <td>${formatDate(p.paid_at)}</td>
      <td><span class="payment-status ${p.status}">${p.status.charAt(0).toUpperCase() + p.status.slice(1)}</span></td>
      <td>${p.expiry_granted ? formatDate(p.expiry_granted) : '—'}</td>
    </tr>`).join('');
}

// ─── Sidebar & Navigation ─────────────────────────────────────────────────────
function initSidebar() {
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const closeBtn = document.getElementById('sidebarClose');

  function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow = 'hidden'; }
  function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow = ''; }

  toggle  && toggle.addEventListener('click', openSidebar);
  overlay && overlay.addEventListener('click', closeSidebar);
  closeBtn && closeBtn.addEventListener('click', closeSidebar);

  document.getElementById('logoutBtn') && document.getElementById('logoutBtn').addEventListener('click', () => Auth.logout());
}

function initNavigation() {
  const links = document.querySelectorAll('[data-page]');
  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const page = link.dataset.page;
      showPage(page);
      // Update active states
      document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
      document.querySelectorAll(`[data-page="${page}"]`).forEach(l => l.classList.add('active'));
      // Close sidebar on mobile
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      if (sidebar && window.innerWidth <= 768) {
        sidebar.classList.remove('open');
        overlay && overlay.classList.remove('open');
        document.body.style.overflow = '';
      }
    });
  });
}

function showPage(pageId) {
  document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
  const target = document.getElementById('page-' + pageId);
  if (target) target.style.display = 'block';
  // Update page title
  const titles = { dashboard: 'Dashboard', courses: 'My Courses', notes: 'Study Notes',
                   mynotes: 'My Notes', classes: 'Live Classes', revision: 'Exam Revision',
                   profile: 'My Profile', payments: 'Payment History' };
  const titleEl = document.getElementById('pageTitle');
  if (titleEl) titleEl.textContent = titles[pageId] || 'Dashboard';

  // Lazy-render sections
  if (pageId === 'notes') renderNotesSection();
  if (pageId === 'mynotes') MyNotes.render();
  if (pageId === 'classes') renderClassesSection();
  if (pageId === 'payments') renderPaymentsSection();
  if (pageId === 'profile') renderProfileSection();
}

// ─── Dashboard Overview ───────────────────────────────────────────────────────
function initDashboard(user) {
  // User info in sidebar/topbar
  document.querySelectorAll('[data-user-name]').forEach(el => el.textContent = user.name);
  document.querySelectorAll('[data-user-initials]').forEach(el => el.textContent = getInitials(user.name));
  document.querySelectorAll('[data-user-email]').forEach(el => el.textContent = user.email);
  document.querySelectorAll('[data-user-role]').forEach(el => el.textContent = formatRole(user.role));

  // Stats
  document.getElementById('statNotesViewed')     && (document.getElementById('statNotesViewed').textContent     = user.notes_viewed  || 0);
  document.getElementById('statClassesAttended') && (document.getElementById('statClassesAttended').textContent = user.classes_attended || 0);
  document.getElementById('statQuizzesTaken')    && (document.getElementById('statQuizzesTaken').textContent    = Math.floor(Math.random() * 8) + 1);
  document.getElementById('statDaysLeft')        && (document.getElementById('statDaysLeft').textContent        = user.access_expiry ? daysRemaining(user.access_expiry) : 0);

  // Recent activity
  renderActivityFeed();
  renderRecommendedCourses(user);
}

function checkAccessStatus(user) {
  const banner = document.getElementById('accessBanner');
  if (!banner) return;

  if (!user.access_expiry || isExpired(user.access_expiry)) {
    banner.className = 'access-banner expired';
    banner.innerHTML = `
      <div class="access-banner-info">
        <div class="access-icon"><i class="fas fa-lock"></i></div>
        <div>
          <h3>Access Expired</h3>
          <p>Your subscription has expired. Renew now to continue learning.</p>
        </div>
      </div>
      <button class="btn btn-gold" onclick="openPaymentModal()"><i class="fas fa-credit-card"></i> Renew Access – 18,500 UGX</button>`;
    return;
  }

  const days = daysRemaining(user.access_expiry);
  const total = 180;
  const percent = Math.round((days / total) * 100);

  if (days <= 7) {
    banner.className = 'access-banner warning';
    banner.innerHTML = `
      <div class="access-banner-info">
        <div class="access-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
          <h3>Access Expiring Soon – ${days} days left</h3>
          <p>Your access expires on ${formatDate(user.access_expiry)}. Renew now to avoid interruption.</p>
          <div class="access-progress">
            <div class="progress-bar"><div class="progress-fill" style="width:${percent}%;background:var(--warning-orange)"></div></div>
          </div>
        </div>
      </div>
      <button class="btn btn-gold btn-sm" onclick="openPaymentModal()"><i class="fas fa-redo"></i> Renew Now</button>`;
  } else {
    banner.className = 'access-banner active';
    banner.innerHTML = `
      <div class="access-banner-info">
        <div class="access-icon"><i class="fas fa-check-circle"></i></div>
        <div>
          <h3>Access Active – ${days} days remaining</h3>
          <p>Valid until ${formatDate(user.access_expiry)}</p>
          <div class="access-progress">
            <div class="progress-bar"><div class="progress-fill" style="width:${percent}%"></div></div>
          </div>
        </div>
      </div>`;
  }
}

async function renderActivityFeed() {
  const list = document.getElementById('activityList');
  if (!list) return;
  const notes = await Payment.getPayments().catch(() => []);
  const activities = [];
  if (notes.length) {
    const latest = notes[0];
    activities.push({ text: `Payment confirmed – access valid until ${formatDate(latest.expiry_granted)}`, time: formatDate(latest.paid_at), dot: 'green' });
  }
  activities.push({ text: 'New study notes are added regularly — check the Study Notes tab.', time: '', dot: 'gold' });
  activities.push({ text: 'New live classes are scheduled weekly — check the Live Classes tab.', time: '', dot: 'blue' });

  list.innerHTML = activities.map(a => `
    <div class="activity-item">
      <div class="activity-dot ${a.dot}"></div>
      <div>
        <div class="activity-text">${sanitize(a.text)}</div>
        ${a.time ? `<div class="activity-time">${sanitize(a.time)}</div>` : ''}
      </div>
    </div>`).join('');
}

function renderRecommendedCourses(user) {
  const container = document.getElementById('coursesList');
  if (!container) return;
  const courses = [
    { name: 'Fundamentals of Nursing', subject: 'Core Nursing', icon: '🏥', progress: 65 },
    { name: 'Anatomy & Physiology', subject: 'Sciences', icon: '🧬', progress: 40 },
    { name: 'Midwifery Essentials', subject: 'Midwifery', icon: '👶', progress: 25 },
  ];
  container.innerHTML = courses.map(c => `
    <div class="course-item">
      <div class="course-icon">${c.icon}</div>
      <div class="course-info">
        <h4>${c.name}</h4>
        <p>${c.subject}</p>
        <div class="course-progress-bar"><div class="course-progress-fill" style="width:${c.progress}%"></div></div>
      </div>
      <div class="course-percent">${c.progress}%</div>
    </div>`).join('');
}

// ─── Notes Section ────────────────────────────────────────────────────────────
let notesRequestId = 0;

async function renderNotesSection() {
  const grid = document.getElementById('notesGrid');
  if (!grid) return;

  const thisRequestId = ++notesRequestId;

  const params = new URLSearchParams();
  if (notesSearchQuery) params.set('q', notesSearchQuery);
  if (notesSearchTutor) params.set('uploader', notesSearchTutor);
  if (activeNotesModule !== 'all') params.set('module', activeNotesModule);

  const res = await fetch('api/notes.php?' + params.toString()).then(r => r.json());
  const notes = res.notes || [];

  // Filter chips always reflect the FULL catalog, not the current search result,
  // so switching modules doesn't require clearing search first.
  const allRes = (notesSearchQuery || notesSearchTutor || activeNotesModule !== 'all')
    ? await fetch('api/notes.php').then(r => r.json())
    : res;

  // A newer search/filter request superseded this one while we were waiting —
  // don't let a slower, stale response overwrite the grid.
  if (thisRequestId !== notesRequestId) return;

  renderNotesFilters(allRes.notes || notes);

  if (!notes.length) {
    grid.innerHTML = `<p style="color:#7a8a9a;text-align:center;padding:40px 0;grid-column:1/-1;">
      No notes match your search/filter.
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
    </div>`;
  }).join('');

  grid.querySelectorAll('.note-card').forEach(card => {
    card.addEventListener('click', () => {
      const note = notes.find(n => String(n.id) === card.dataset.noteId);
      if (note) PDFViewer.open(note.id, note.title);
    });
  });
}

function renderNotesFilters(allNotes) {
  const wrap = document.querySelector('#page-notes .notes-filters');
  if (!wrap) return;
  const modules = [...new Set(allNotes.map(n => n.module).filter(Boolean))].sort();
  const chips = ['all', ...modules];

  wrap.innerHTML = chips.map(m => `
    <button class="filter-btn notes-filter-btn ${m === activeNotesModule ? 'active' : ''}" data-filter="${sanitize(m)}">
      ${m === 'all' ? 'All Notes' : sanitize(m)}
    </button>`).join('');

  wrap.querySelectorAll('.notes-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      activeNotesModule = btn.dataset.filter;
      renderNotesSection();
    });
  });
}

// ─── Classes Section ──────────────────────────────────────────────────────────
async function renderClassesSection() {
  const grid = document.getElementById('classesGrid');
  if (!grid) return;

  const res = await fetch('api/classes.php').then(r => r.json());
  const allClasses = res.classes || [];
  const hasAccess = res.hasAccess;
  renderClassesFilters(allClasses);

  const classes = activeClassesModule === 'all' ? allClasses : allClasses.filter(c => c.module === activeClassesModule);

  if (!classes.length) {
    grid.innerHTML = `<p style="color:#7a8a9a;text-align:center;padding:40px 0;grid-column:1/-1;">
      ${allClasses.length ? 'No classes scheduled for this module yet.' : 'No classes have been scheduled yet.'}
    </p>`;
    return;
  }

  grid.innerHTML = classes.map(c => {
    let linkArea;
    if (!hasAccess) {
      linkArea = `
        <div class="telegram-link-box" style="flex-direction:column;align-items:flex-start;gap:8px;">
          <span><i class="fas fa-lock" aria-hidden="true"></i> Renew your access to join this class</span>
          <button class="btn btn-gold btn-sm" onclick="openPaymentModal()"><i class="fas fa-credit-card" aria-hidden="true"></i> Renew Access</button>
        </div>`;
    } else if (c.status !== 'completed' && c.telegram_link) {
      linkArea = `
        <div class="telegram-link-box">
          <span><i class="fab fa-telegram" aria-hidden="true"></i> Join via Telegram:</span>
          <a href="${sanitize(c.telegram_link)}" target="_blank" rel="noopener noreferrer">${sanitize(c.telegram_link.replace('https://',''))}</a>
        </div>`;
    } else if (c.status === 'completed') {
      linkArea = `<p style="font-size:0.82rem;color:#7a8a9a;margin-top:12px;"><i class="fas fa-info-circle" aria-hidden="true"></i> This class has ended. Recording available on Telegram.</p>`;
    } else {
      linkArea = '';
    }

    return `
    <div class="class-card">
      <div class="class-card-header">
        <div>
          <span class="class-status-badge ${c.status}">${c.status === 'live' ? '🔴 LIVE NOW' : c.status === 'upcoming' ? '📅 Upcoming' : '✅ Completed'}</span>
          <span class="note-type-badge nursing" style="margin-left:6px;">${sanitize(c.module || 'General')}</span>
        </div>
      </div>
      <h3>${sanitize(c.title)}</h3>
      <div class="class-meta">
        <div class="class-meta-item"><i class="fas fa-user-md" aria-hidden="true"></i> ${sanitize(c.tutor_name)}</div>
        <div class="class-meta-item"><i class="fas fa-calendar" aria-hidden="true"></i> ${formatDate(c.class_date)}</div>
        <div class="class-meta-item"><i class="fas fa-clock" aria-hidden="true"></i> ${sanitize(c.class_time)}</div>
      </div>
      ${linkArea}
    </div>`;
  }).join('');
}

function renderClassesFilters(allClasses) {
  const wrap = document.getElementById('classesFilters');
  if (!wrap) return;
  const modules = [...new Set(allClasses.map(c => c.module).filter(Boolean))].sort();
  const chips = ['all', ...modules];

  wrap.innerHTML = chips.map(m => `
    <button class="filter-btn classes-filter-btn ${m === activeClassesModule ? 'active' : ''}" data-filter="${sanitize(m)}">
      ${m === 'all' ? 'All Classes' : sanitize(m)}
    </button>`).join('');

  wrap.querySelectorAll('.classes-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      activeClassesModule = btn.dataset.filter;
      renderClassesSection();
    });
  });
}

// ─── Payments Section ─────────────────────────────────────────────────────────
async function renderPaymentsSection() {
  const table = document.getElementById('paymentsTableBody');
  if (!table) return;
  const payments = await Payment.getPayments();
  if (!payments.length) {
    table.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#7a8a9a;padding:40px;">No payment records found.</td></tr>';
    return;
  }
  table.innerHTML = payments.map(p => `
    <tr>
      <td><strong>${sanitize(p.reference)}</strong></td>
      <td>UGX ${Number(p.amount).toLocaleString()}</td>
      <td>${sanitize(p.method)} Mobile Money</td>
      <td>${formatDate(p.paid_at)}</td>
      <td><span class="payment-status ${p.status}">${p.status.charAt(0).toUpperCase() + p.status.slice(1)}</span></td>
      <td>${p.expiry_granted ? formatDate(p.expiry_granted) : '—'}</td>
    </tr>`).join('');
}

// ─── Profile Section ──────────────────────────────────────────────────────────
function renderProfileSection() {
  const user = Auth.getCurrentUser();
  if (!user) return;
  const fields = {
    'prof-name': user.name, 'prof-email': user.email, 'prof-phone': user.phone,
    'prof-reg': user.reg_number, 'prof-institution': user.institution,
    'prof-joined': formatDate(user.created_at)
  };
  Object.entries(fields).forEach(([id, val]) => {
    const el = document.getElementById(id);
    if (el) el.textContent = val || '—';
  });
  document.querySelectorAll('[data-profile-initials]').forEach(el => el.textContent = getInitials(user.name));

  const courseSel = document.getElementById('profCourse');
  const yearSel = document.getElementById('profYear');
  const semSel = document.getElementById('profSemester');
  if (courseSel) courseSel.value = user.course;
  if (yearSel) yearSel.value = user.year;
  if (semSel) semSel.value = user.semester;

  // Current modules (from real curriculum data, where available)
  const modulesCard = document.getElementById('profModulesCard');
  const modulesList = document.getElementById('profModulesList');
  if (modulesCard && modulesList) {
    const modules = getCurriculumModules(user.course, user.year, user.semester);
    if (modules) {
      modulesCard.style.display = 'block';
      modulesList.innerHTML = modules.map(m => `
        <li style="display:flex;justify-content:space-between;gap:12px;padding:10px 12px;background:var(--light-blue);border-radius:8px;">
          <span style="font-weight:600;color:var(--primary-blue);">${sanitize(m.code)}</span>
          <span style="color:var(--text-dark);text-align:right;">${sanitize(m.title)}</span>
        </li>`).join('');
    } else {
      modulesCard.style.display = 'none';
    }
  }
}

function initAcademicDetailsForm() {
  const form = document.getElementById('academicDetailsForm');
  form && form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('api/profile.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'update-academic',
        course: document.getElementById('profCourse').value,
        year: document.getElementById('profYear').value,
        semester: document.getElementById('profSemester').value
      })
    }).then(r => r.json());

    if (!res.success) { Toast.error(res.message || 'Could not update academic details.'); return; }
    await Auth.init();
    renderProfileSection();
    Toast.success('Academic details updated.');
  });
}

// ─── Notifications ────────────────────────────────────────────────────────────
function buildNotifications(user) {
  const notifications = [];
  if (user.access_expiry && daysRemaining(user.access_expiry) <= 7) {
    notifications.push('Your access expires in ' + daysRemaining(user.access_expiry) + ' days!');
  }
  notifications.push('New study notes and live classes are added regularly.');
  // Show badge count
  const dot = document.querySelector('.notif-dot');
  if (dot && notifications.length > 0) dot.style.display = 'block';
}

// ─── Payment Modal (dashboard) ────────────────────────────────────────────────
function openPaymentModal() {
  const user = Auth.getCurrentUser();
  const modal = document.getElementById('paymentModal');
  if (!modal) return;
  const phoneEl = document.getElementById('paymentPhone');
  if (phoneEl && user) phoneEl.value = user.phone || '';
  // Reset steps
  const stepForm = document.getElementById('paymentStepForm');
  const stepProc = document.getElementById('paymentStepProcessing');
  const stepSucc = document.getElementById('paymentStepSuccess');
  if (stepForm) stepForm.style.display = 'block';
  if (stepProc) stepProc.style.display = 'none';
  if (stepSucc) stepSucc.style.display = 'none';
  modal.classList.add('active');
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function getInitials(name) {
  return (name || '').split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
}
function formatRole(role) {
  const map = { superadmin: 'Super Admin', tutor: 'Tutor', student: 'Student' };
  return map[role] || role;
}
