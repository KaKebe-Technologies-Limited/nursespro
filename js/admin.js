/**
 * NursesPro Academy - Admin Dashboard
 * All tables here are backed by real PHP API endpoints over MySQL.
 */

let studentsCache = [];

// ─── Admin Init ───────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  const [user] = await Promise.all([Auth.guardPage(), initCurriculum()]);
  if (!user) return;

  initAdminUI(user);
  initAdminSidebar();
  initAdminNavigation(user);
  showAdminPage('overview');
});

// ─── Admin Sidebar ────────────────────────────────────────────────────────────
function initAdminSidebar() {
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('open'); }
  function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }

  toggle  && toggle.addEventListener('click', openSidebar);
  overlay && overlay.addEventListener('click', closeSidebar);

  const logoutBtn = document.getElementById('logoutBtn');
  logoutBtn && logoutBtn.addEventListener('click', () => Auth.logout());
}

// ─── Admin Navigation ─────────────────────────────────────────────────────────
function initAdminNavigation(user) {
  document.querySelectorAll('[data-admin-page]').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const page = link.dataset.adminPage;
      // Tutor access restrictions
      const superAdminOnly = ['students','transactions','curriculum','users','settings','reports'];
      if (user.role === 'tutor' && superAdminOnly.includes(page)) {
        Toast.warning('This section is only available to Super Admins.');
        return;
      }
      showAdminPage(page);
      document.querySelectorAll('[data-admin-page]').forEach(l => l.classList.remove('active'));
      document.querySelectorAll(`[data-admin-page="${page}"]`).forEach(l => l.classList.add('active'));
      // Close sidebar on mobile
      if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
      }
    });
  });
}

function showAdminPage(pageId) {
  document.querySelectorAll('.admin-page-section').forEach(s => s.style.display = 'none');
  const target = document.getElementById('adminPage-' + pageId);
  if (target) target.style.display = 'block';

  const titles = {
    overview: 'Dashboard Overview', students: 'Student Management',
    notes: 'Content Management – Notes', classes: 'Live Class Management',
    transactions: 'Payment Transactions', curriculum: 'Curriculum Management',
    users: 'User Management', reports: 'Reports & Analytics', settings: 'System Settings'
  };
  const titleEl = document.getElementById('pageTitle');
  if (titleEl) titleEl.textContent = titles[pageId] || 'Admin';

  // Lazy render
  if (pageId === 'overview')      renderAdminOverview();
  if (pageId === 'students')      renderStudentsTable();
  if (pageId === 'notes')         renderNotesTable();
  if (pageId === 'classes')       renderClassesTable();
  if (pageId === 'transactions')  renderTransactionsTable();
  if (pageId === 'curriculum')    renderCurriculumTable();
  if (pageId === 'users')         renderUsersTable();
}

// ─── Overview ─────────────────────────────────────────────────────────────────
async function renderAdminOverview() {
  const stats = await fetch('api/overview_stats.php').then(r => r.json());

  const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  set('adminStatStudents', stats.totalStudents);
  set('adminStatActive', stats.activeStudents);
  set('adminStatNotes', stats.notesCount);

  // Revenue is only present in the response for superadmin — tutors never see it.
  if ('revenue' in stats) {
    set('adminStatRevenue', 'UGX ' + Number(stats.revenue).toLocaleString());
  }
}

// ─── Students Table ───────────────────────────────────────────────────────────
async function renderStudentsTable(searchQuery = '', courseFilter = '') {
  const tbody = document.getElementById('studentsTableBody');
  if (!tbody) return;

  const params = new URLSearchParams();
  if (searchQuery) params.set('search', searchQuery);
  if (courseFilter) params.set('course', courseFilter);
  const res = await fetch('api/students.php?' + params.toString()).then(r => r.json());
  studentsCache = res.students || [];

  if (!studentsCache.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#7a8a9a;padding:30px;">No students found.</td></tr>';
    return;
  }
  tbody.innerHTML = studentsCache.map(u => {
    const expired = !u.access_expiry || isExpired(u.access_expiry);
    const statusClass = expired ? 'pending' : 'paid';
    const statusText  = expired ? 'Expired' : 'Active';
    return `<tr>
      <td><span class="table-avatar">${getInitials(u.name)}</span><span class="table-name">${sanitize(u.name)}</span></td>
      <td>${sanitize(u.email)}</td>
      <td>${sanitize(u.course || '—')}</td>
      <td>${sanitize(u.year || '—')}</td>
      <td><span class="payment-status ${statusClass}">${statusText}</span></td>
      <td>${u.access_expiry ? formatDate(u.access_expiry) : '—'}</td>
      <td>
        <div class="table-actions">
          <button class="table-action-btn view" onclick="viewStudent('${u.id}')" title="View"><i class="fas fa-eye"></i></button>
          <button class="table-action-btn edit" onclick="grantAccess('${u.id}')" title="Grant Access"><i class="fas fa-key"></i></button>
          <button class="table-action-btn delete" onclick="removeStudent('${u.id}')" title="Remove"><i class="fas fa-trash"></i></button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function viewStudent(id) {
  const u = studentsCache.find(s => String(s.id) === String(id));
  if (!u) return;
  Toast.info(`Student: ${u.name} | ${u.email} | ${u.course}`);
}

async function grantAccess(id) {
  if (!confirm('Manually grant 6-month access to this student?')) return;
  const res = await fetch('api/students.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'grant-access', id })
  }).then(r => r.json());
  if (!res.success) { Toast.error(res.message || 'Could not grant access.'); return; }
  renderStudentsTable();
  Toast.success('Access granted for 6 months.');
}

async function removeStudent(id) {
  if (!confirm('Are you sure you want to remove this student? This cannot be undone.')) return;
  const res = await fetch('api/students.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'remove', id })
  }).then(r => r.json());
  if (!res.success) { Toast.error(res.message || 'Could not remove student.'); return; }
  renderStudentsTable();
  Toast.success('Student removed.');
}

// ─── Notes Table ──────────────────────────────────────────────────────────────
let notesCache = [];

async function renderNotesTable() {
  const tbody = document.getElementById('notesTableBody');
  if (!tbody) return;
  const res = await fetch('api/notes.php').then(r => r.json());
  const notes = notesCache = res.notes || [];

  if (!notes.length) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#7a8a9a;padding:30px;">No notes published yet.</td></tr>';
    return;
  }
  tbody.innerHTML = notes.map(n => {
    const mod = getModuleInfo(n.module);
    return `
    <tr>
      <td><strong>${sanitize(n.title)}</strong></td>
      <td>${sanitize(mod ? `${mod.year} · ${mod.semester}` : '—')}</td>
      <td><span class="note-type-badge nursing">${sanitize(n.module || '—')}</span></td>
      <td>${n.views}</td>
      <td>${formatDate(n.created_at)}</td>
      <td>
        <div class="table-actions">
          <button class="table-action-btn edit" onclick="editNote('${n.id}')" title="Edit"><i class="fas fa-edit"></i></button>
          <button class="table-action-btn delete" onclick="deleteNote('${n.id}')" title="Delete"><i class="fas fa-trash"></i></button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

async function deleteNote(id) {
  if (!confirm('Delete this note? Students will lose access to it.')) return;
  const res = await fetch('api/notes.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete', id })
  }).then(r => r.json());
  if (!res.success) { Toast.error(res.message || 'Could not delete note.'); return; }
  renderNotesTable();
  Toast.success('Note deleted.');
}

function editNote(id) {
  const note = notesCache.find(n => String(n.id) === String(id));
  if (!note) return;

  document.getElementById('uploadNoteTitle').textContent = 'Edit Study Note';
  document.getElementById('uploadNoteSubtitle').textContent = 'Update this note\'s details, or replace the PDF';
  document.getElementById('noteId').value = note.id;
  document.getElementById('noteTitle').value = note.title;
  document.getElementById('noteModule').value = note.module;
  document.getElementById('noteDesc').value = note.description || '';
  document.getElementById('noteFileLabel').innerHTML = 'PDF File';
  document.getElementById('noteFileHint').textContent = 'Leave empty to keep the current file. PDF only, max 50MB.';
  document.getElementById('noteSubmitBtn').innerHTML = '<i class="fas fa-save" aria-hidden="true"></i> Save Changes';
  document.getElementById('uploadedFileInfo').style.display = 'none';
  document.getElementById('pdfFileInput').value = '';

  loadTopicsForModule(note.module, note.topic_id, note.subtopic_id);
  openModal('uploadNoteModal');
}

function resetUploadNoteModal() {
  document.getElementById('uploadNoteTitle').textContent = 'Upload Study Note';
  document.getElementById('uploadNoteSubtitle').textContent = 'Add a new PDF note for students';
  document.getElementById('noteId').value = '';
  document.getElementById('noteFileLabel').innerHTML = 'PDF File <span class="required">*</span>';
  document.getElementById('noteFileHint').textContent = 'PDF files only, max 50MB';
  document.getElementById('noteSubmitBtn').innerHTML = '<i class="fas fa-upload" aria-hidden="true"></i> Publish Note';
  document.getElementById('uploadNoteForm').reset();
  document.getElementById('uploadedFileInfo').style.display = 'none';
  resetTopicSubtopicSelects();
}

// ─── Course/Module -> Topic -> Subtopic cascade (shared by upload/edit) ─────────
let currentNoteTopics = [];

function resetTopicSubtopicSelects() {
  const topicSelect = document.getElementById('noteTopic');
  const subtopicSelect = document.getElementById('noteSubtopic');
  if (topicSelect) { topicSelect.innerHTML = '<option value="">— Select module first —</option>'; topicSelect.disabled = true; }
  if (subtopicSelect) { subtopicSelect.innerHTML = '<option value="">— Select topic first —</option>'; subtopicSelect.disabled = true; }
  currentNoteTopics = [];
}

async function loadTopicsForModule(moduleCode, selectedTopicId = null, selectedSubtopicId = null) {
  const topicSelect = document.getElementById('noteTopic');
  const subtopicSelect = document.getElementById('noteSubtopic');
  if (!topicSelect || !subtopicSelect) return;

  if (!moduleCode) { resetTopicSubtopicSelects(); return; }

  topicSelect.innerHTML = '<option value="">Loading…</option>';
  topicSelect.disabled = true;
  subtopicSelect.innerHTML = '<option value="">— Select topic first —</option>';
  subtopicSelect.disabled = true;

  const res = await fetch('api/topics.php?module=' + encodeURIComponent(moduleCode)).then(r => r.json());
  currentNoteTopics = res.topics || [];

  topicSelect.innerHTML = '<option value="">— No topic (general) —</option>' +
    currentNoteTopics.map(t => `<option value="${t.id}">${sanitize(t.title)}</option>`).join('');
  topicSelect.disabled = false;

  if (selectedTopicId) {
    topicSelect.value = selectedTopicId;
    populateSubtopicSelect(selectedTopicId, selectedSubtopicId);
  }
}

function populateSubtopicSelect(topicId, selectedSubtopicId = null) {
  const subtopicSelect = document.getElementById('noteSubtopic');
  if (!subtopicSelect) return;
  const topic = currentNoteTopics.find(t => String(t.id) === String(topicId));
  const subtopics = topic ? topic.subtopics : [];

  if (!subtopics.length) {
    subtopicSelect.innerHTML = '<option value="">— No subtopics for this topic —</option>';
    subtopicSelect.disabled = true;
    return;
  }
  subtopicSelect.innerHTML = '<option value="">— No subtopic (general) —</option>' +
    subtopics.map(s => `<option value="${s.id}">${s.code ? sanitize(s.code) + ' — ' : ''}${sanitize(s.title)}</option>`).join('');
  subtopicSelect.disabled = false;
  if (selectedSubtopicId) subtopicSelect.value = selectedSubtopicId;
}

// ─── Upload / Edit Note Form ────────────────────────────────────────────────────
function initUploadForm() {
  const form = document.getElementById('uploadNoteForm');
  const uploadArea = document.getElementById('uploadArea');
  const fileInput  = document.getElementById('pdfFileInput');
  const uploadedFile = document.getElementById('uploadedFileInfo');
  const moduleSelect = document.getElementById('noteModule');
  const topicSelect = document.getElementById('noteTopic');
  const subtopicSelect = document.getElementById('noteSubtopic');

  if (moduleSelect) {
    moduleSelect.innerHTML = '<option value="">— Select course/module —</option>' +
      getAllModules().map(m => `<option value="${sanitize(m.code)}">${sanitize(m.code)} — ${sanitize(m.title)} (${sanitize(m.year)}, ${sanitize(m.semester)})</option>`).join('');
    moduleSelect.addEventListener('change', () => loadTopicsForModule(moduleSelect.value));
  }
  topicSelect && topicSelect.addEventListener('change', () => populateSubtopicSelect(topicSelect.value));

  document.getElementById('uploadNoteModal')?.querySelector('.modal-close')
    ?.addEventListener('click', resetUploadNoteModal);
  document.querySelectorAll('button[onclick*="uploadNoteModal"]').forEach(btn => {
    btn.addEventListener('click', resetUploadNoteModal);
  });

  uploadArea && uploadArea.addEventListener('click', () => fileInput && fileInput.click());
  uploadArea && uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('dragover'); });
  uploadArea && uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
  uploadArea && uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    if (e.dataTransfer.files[0]) handleFileSelect(e.dataTransfer.files[0]);
  });

  fileInput && fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) handleFileSelect(fileInput.files[0]);
  });

  function handleFileSelect(file) {
    if (file.type !== 'application/pdf') { Toast.error('Please select a PDF file.'); return; }
    if (file.size > 50 * 1024 * 1024) { Toast.error('File size must be under 50MB.'); return; }
    if (uploadedFile) {
      uploadedFile.style.display = 'flex';
      uploadedFile.querySelector('.file-name').textContent = file.name;
      uploadedFile.querySelector('.file-size').textContent = (file.size / (1024*1024)).toFixed(2) + ' MB';
    }
  }

  form && form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id          = document.getElementById('noteId')?.value;
    const title       = document.getElementById('noteTitle')?.value.trim();
    const moduleCode  = document.getElementById('noteModule')?.value;
    const topicId     = document.getElementById('noteTopic')?.value;
    const subtopicId  = document.getElementById('noteSubtopic')?.value;
    const description = document.getElementById('noteDesc')?.value.trim();
    if (!title || !moduleCode) { Toast.error('Please fill in the title and select a module.'); return; }
    if (!id && !fileInput?.files[0]) { Toast.error('Please attach a PDF file.'); return; }

    const formData = new FormData();
    formData.append('action', id ? 'update' : 'upload');
    if (id) formData.append('id', id);
    formData.append('title', title);
    formData.append('module', moduleCode);
    if (topicId) formData.append('topic', topicId);
    if (subtopicId) formData.append('subtopic', subtopicId);
    formData.append('description', description);
    if (fileInput?.files[0]) formData.append('file', fileInput.files[0]);

    const res = await fetch('api/notes.php', { method: 'POST', body: formData }).then(r => r.json());
    if (!res.success) { Toast.error(res.message || 'Could not save note.'); return; }

    closeModal('uploadNoteModal');
    renderNotesTable();
    Toast.success(res.message || (id ? 'Note updated.' : 'Note published successfully!'));
    resetUploadNoteModal();
  });
}

// ─── Classes Table ────────────────────────────────────────────────────────────
let classesCache = [];

async function renderClassesTable() {
  const tbody = document.getElementById('classesTableBody');
  if (!tbody) return;
  const res = await fetch('api/classes.php').then(r => r.json());
  const classes = classesCache = res.classes || [];

  if (!classes.length) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#7a8a9a;padding:30px;">No classes scheduled yet.</td></tr>';
    return;
  }
  const statusLabel = { upcoming: 'Upcoming', live: '🔴 LIVE', completed: 'Completed' };
  tbody.innerHTML = classes.map(c => `
    <tr>
      <td><strong>${sanitize(c.title)}</strong></td>
      <td><span class="note-type-badge nursing">${sanitize(c.module || '—')}</span></td>
      <td>${sanitize(c.tutor_name)}</td>
      <td>${formatDate(c.class_date)}</td>
      <td>${sanitize(c.class_time)}</td>
      <td><span class="class-status-badge ${c.status}">${statusLabel[c.status] || c.status}</span></td>
      <td>${c.telegram_link ? `<a href="${sanitize(c.telegram_link)}" target="_blank" rel="noopener noreferrer" style="color:#0088cc;font-size:0.82rem;">${sanitize(c.telegram_link.replace('https://',''))}</a>` : '<span style="color:#7a8a9a;font-size:0.82rem;">—</span>'}</td>
      <td>
        <div class="table-actions">
          <button class="table-action-btn edit" onclick="editClass('${c.id}')" title="Edit"><i class="fas fa-edit"></i></button>
          <button class="table-action-btn delete" onclick="deleteClass('${c.id}')" title="Delete"><i class="fas fa-trash"></i></button>
        </div>
      </td>
    </tr>`).join('');
}

async function deleteClass(id) {
  if (!confirm('Delete this class? Students will lose access to its Telegram link.')) return;
  const res = await fetch('api/classes.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete', id })
  }).then(r => r.json());
  if (!res.success) { Toast.error(res.message || 'Could not delete class.'); return; }
  renderClassesTable();
  Toast.success('Class deleted.');
}

function editClass(id) {
  const cls = classesCache.find(c => String(c.id) === String(id));
  if (!cls) return;

  document.getElementById('addClassTitle').textContent = 'Edit Live Class';
  document.getElementById('addClassSubtitle').textContent = 'Update this class session';
  document.getElementById('classId').value = cls.id;
  document.getElementById('classTitle').value = cls.title;
  document.getElementById('classModule').value = cls.module;
  document.getElementById('classTutor').value = cls.tutor_name;
  document.getElementById('classDate').value = cls.class_date;
  document.getElementById('classTime').value = cls.class_time;
  document.getElementById('classStatus').value = cls.status;
  document.getElementById('classTelegram').value = cls.telegram_link || '';
  document.getElementById('classSubmitBtn').innerHTML = '<i class="fas fa-save" aria-hidden="true"></i> Save Changes';

  openModal('addClassModal');
}

function resetScheduleClassModal() {
  document.getElementById('addClassTitle').textContent = 'Schedule Live Class';
  document.getElementById('addClassSubtitle').textContent = 'Add a new Telegram class session for students';
  document.getElementById('classId').value = '';
  document.getElementById('classSubmitBtn').innerHTML = '<i class="fas fa-calendar-plus" aria-hidden="true"></i> Schedule Class';
  document.getElementById('scheduleClassForm').reset();
}

// ─── Schedule / Edit Class Form ─────────────────────────────────────────────────
function initScheduleClassForm() {
  const form = document.getElementById('scheduleClassForm');
  const moduleSelect = document.getElementById('classModule');

  if (moduleSelect) {
    moduleSelect.innerHTML = '<option value="">— Select module —</option>' +
      getAllModules().map(m => `<option value="${sanitize(m.code)}">${sanitize(m.code)} — ${sanitize(m.title)} (${sanitize(m.year)}, ${sanitize(m.semester)})</option>`).join('');
  }

  document.getElementById('addClassModal')?.querySelector('.modal-close')
    ?.addEventListener('click', resetScheduleClassModal);
  document.querySelectorAll('button[onclick*="addClassModal"]').forEach(btn => {
    btn.addEventListener('click', resetScheduleClassModal);
  });

  form && form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id       = document.getElementById('classId')?.value;
    const title    = document.getElementById('classTitle')?.value.trim();
    const module   = document.getElementById('classModule')?.value;
    const tutor    = document.getElementById('classTutor')?.value.trim();
    const date     = document.getElementById('classDate')?.value;
    const time     = document.getElementById('classTime')?.value.trim();
    const telegram = document.getElementById('classTelegram')?.value.trim();
    const status   = document.getElementById('classStatus')?.value;

    if (!title || !module || !tutor || !date || !time || !status) { Toast.error('Please fill in all required fields.'); return; }

    const res = await fetch('api/classes.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: id ? 'update' : 'schedule', id, title, module, tutor, date, time, status, telegram })
    }).then(r => r.json());
    if (!res.success) { Toast.error(res.message || 'Could not save class.'); return; }

    closeModal('addClassModal');
    renderClassesTable();
    Toast.success(res.message || (id ? 'Class updated.' : 'Class scheduled!'));
    resetScheduleClassModal();
  });
}

// ─── Curriculum Table ─────────────────────────────────────────────────────────
async function renderCurriculumTable() {
  const tbody = document.getElementById('curriculumTableBody');
  if (!tbody) return;
  await initCurriculum(); // refresh cache
  const modules = getAllModules();

  if (!modules.length) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#7a8a9a;padding:30px;">No modules yet.</td></tr>';
    return;
  }
  tbody.innerHTML = modules.map(m => `
    <tr>
      <td>${sanitize(m.course)}</td>
      <td>${sanitize(m.year)}</td>
      <td>${sanitize(m.semester)}</td>
      <td><strong>${sanitize(m.code)}</strong></td>
      <td>${sanitize(m.title)}</td>
      <td>
        <div class="table-actions">
          <button class="table-action-btn edit" onclick="editModule(${m.id})" title="Edit"><i class="fas fa-edit"></i></button>
          <button class="table-action-btn delete" onclick="deleteModule(${m.id})" title="Delete"><i class="fas fa-trash"></i></button>
        </div>
      </td>
    </tr>`).join('');
}

function initModuleForm() {
  const addBtn = document.getElementById('addModuleBtn');
  const form = document.getElementById('moduleForm');

  addBtn && addBtn.addEventListener('click', () => openModuleEditor());

  form && form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('moduleId').value;
    const course = document.getElementById('moduleCourse').value.trim();
    const year = document.getElementById('moduleYear').value.trim();
    const semester = document.getElementById('moduleSemester').value.trim();
    const code = document.getElementById('moduleCode').value.trim();
    const title = document.getElementById('moduleTitle').value.trim();
    if (!course || !year || !semester || !code || !title) { Toast.error('Please fill in all fields.'); return; }

    const res = await fetch('api/curriculum.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: id ? 'update' : 'add', id, course, year, semester, code, title })
    }).then(r => r.json());
    if (!res.success) { Toast.error(res.message || 'Could not save module.'); return; }

    closeModal('moduleModal');
    renderCurriculumTable();
    Toast.success(id ? 'Module updated.' : 'Module added.');
    form.reset();
  });
}

function openModuleEditor(id = null) {
  const mod = id ? getAllModules().find(m => m.id === id) : null;
  document.getElementById('moduleModalTitle').textContent = mod ? 'Edit Module' : 'Add Module';
  document.getElementById('moduleId').value = mod ? mod.id : '';
  document.getElementById('moduleCourse').value = mod ? mod.course : '';
  document.getElementById('moduleYear').value = mod ? mod.year : '';
  document.getElementById('moduleSemester').value = mod ? mod.semester : '';
  document.getElementById('moduleCode').value = mod ? mod.code : '';
  document.getElementById('moduleTitle').value = mod ? mod.title : '';

  const courseList = document.getElementById('moduleCourseList');
  if (courseList) {
    const courses = [...new Set(getAllModules().map(m => m.course))];
    courseList.innerHTML = courses.map(c => `<option value="${sanitize(c)}">`).join('');
  }

  openModal('moduleModal');
}

function editModule(id) { openModuleEditor(id); }

async function deleteModule(id) {
  if (!confirm('Delete this module? Notes and classes tagged to it will also be removed.')) return;
  const res = await fetch('api/curriculum.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete', id })
  }).then(r => r.json());
  if (!res.success) { Toast.error(res.message || 'Could not delete module.'); return; }
  renderCurriculumTable();
  Toast.success('Module deleted.');
}

// ─── Transactions Table ───────────────────────────────────────────────────────
async function renderTransactionsTable() {
  const tbody = document.getElementById('transactionsTableBody');
  if (!tbody) return;
  const res = await fetch('api/payments.php').then(r => r.json());
  const payments = res.payments || [];

  if (!payments.length) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#7a8a9a;padding:30px;">No transactions found.</td></tr>';
    return;
  }
  tbody.innerHTML = payments.map(p => `<tr>
      <td><strong>${sanitize(p.reference)}</strong></td>
      <td>${sanitize(p.student_name || 'Unknown')}</td>
      <td>UGX ${Number(p.amount).toLocaleString()}</td>
      <td>${sanitize(p.method)}</td>
      <td><span class="payment-status ${p.status}">${p.status.charAt(0).toUpperCase() + p.status.slice(1)}</span></td>
      <td>${formatDate(p.paid_at)}</td>
    </tr>`).join('');
}

// ─── Users Table ──────────────────────────────────────────────────────────────
async function renderUsersTable() {
  const tbody = document.getElementById('usersTableBody');
  if (!tbody) return;
  const res = await fetch('api/users.php').then(r => r.json());
  const users = res.users || [];
  tbody.innerHTML = users.map(u => `
    <tr>
      <td><span class="table-avatar">${getInitials(u.name)}</span><span class="table-name">${sanitize(u.name)}</span></td>
      <td>${sanitize(u.email)}</td>
      <td><span class="payment-status ${u.role === 'superadmin' ? 'paid' : 'pending'}">${formatRole(u.role)}</span></td>
      <td>${formatDate(u.created_at)}</td>
      <td>
        <div class="table-actions">
          <button class="table-action-btn delete" onclick="removeUser('${u.id}')" title="Remove"><i class="fas fa-trash"></i></button>
        </div>
      </td>
    </tr>`).join('');
}

async function removeUser(id) {
  const currentUser = Auth.getCurrentUser();
  if (String(id) === String(currentUser.id)) { Toast.error("You can't remove your own account."); return; }
  if (!confirm('Remove this user?')) return;
  const res = await fetch('api/users.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'remove', id })
  }).then(r => r.json());
  if (!res.success) { Toast.error(res.message || 'Could not remove user.'); return; }
  renderUsersTable();
  Toast.success('User removed.');
}

// ─── Admin UI Init ────────────────────────────────────────────────────────────
function initAdminUI(user) {
  document.querySelectorAll('[data-user-name]').forEach(el => el.textContent = user.name);
  document.querySelectorAll('[data-user-initials]').forEach(el => el.textContent = getInitials(user.name));
  document.querySelectorAll('[data-user-role]').forEach(el => el.textContent = formatRole(user.role));

  // Restrict tutor access in UI
  if (user.role === 'tutor') {
    document.querySelectorAll('.superadmin-only').forEach(el => el.style.display = 'none');
  }

  // Search handler
  const searchInput = document.getElementById('studentSearch');
  searchInput && searchInput.addEventListener('input', () =>
    renderStudentsTable(searchInput.value, document.getElementById('courseFilter')?.value || ''));

  const courseFilter = document.getElementById('courseFilter');
  courseFilter && courseFilter.addEventListener('change', () =>
    renderStudentsTable(searchInput?.value || '', courseFilter.value));

  initUploadForm();
  initScheduleClassForm();
  initModuleForm();
}

// ─── Export (placeholder) ─────────────────────────────────────────────────────
function exportReport(type) {
  Toast.info(`Exporting ${type} report... In production, this downloads a CSV from the server.`);
}

// ─── Modal helpers ────────────────────────────────────────────────────────────
function openModal(id) {
  const el = document.getElementById(id);
  el && el.classList.add('active');
}
function closeModal(id) {
  const el = document.getElementById(id);
  el && el.classList.remove('active');
}

// ─── Helpers shared ──────────────────────────────────────────────────────────
function getInitials(name) {
  return (name || '').split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
}
function formatRole(role) {
  return { superadmin:'Super Admin', tutor:'Tutor', student:'Student' }[role] || role;
}
