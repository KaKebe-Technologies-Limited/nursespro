/**
 * NursesPro Academy - My Notes (student personal notes)
 *
 * Lets a student write their own study notes, organize them into
 * categories, search them, export any note as a PDF, or print it.
 * Backed by the student_notes table via api/my_notes.php.
 */

const MyNotes = (() => {
  let currentUser = null;
  let activeCategory = 'all';
  let searchQuery = '';
  let notesCache = [];

  function init(user) {
    currentUser = user;
    document.getElementById('newMyNoteBtn')?.addEventListener('click', () => openEditor());

    document.getElementById('myNotesSearch')?.addEventListener('input', (e) => {
      searchQuery = e.target.value.trim().toLowerCase();
      renderList();
    });

    document.getElementById('myNoteForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      save();
    });
  }

  // ── Storage ────────────────────────────────────────────────────────────
  async function fetchAll() {
    const res = await fetch('api/my_notes.php').then(r => r.json());
    notesCache = res.notes || [];
    return notesCache;
  }

  function getCategories() {
    const cats = new Set(notesCache.map(n => n.category || 'General'));
    return [...cats].sort((a, b) => a.localeCompare(b));
  }

  function getById(id) {
    return notesCache.find(n => String(n.id) === String(id)) || null;
  }

  // ── CRUD ───────────────────────────────────────────────────────────────
  async function save() {
    const id = document.getElementById('myNoteId').value;
    const title = document.getElementById('myNoteTitle').value.trim();
    const category = document.getElementById('myNoteCategory').value.trim() || 'General';
    const content = document.getElementById('myNoteContent').value;

    if (!title) { Toast.error('Please enter a title for your note.'); return; }

    const res = await fetch('api/my_notes.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: id ? 'update' : 'create', id, title, category, content })
    }).then(r => r.json());

    if (!res.success) { Toast.error(res.message || 'Could not save note.'); return; }
    closeModal('myNoteModal');
    Toast.success(id ? 'Note updated.' : 'Note created.');
    render();
  }

  async function remove(id) {
    if (!confirm('Delete this note? This cannot be undone.')) return;
    await fetch('api/my_notes.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', id })
    }).then(r => r.json());
    Toast.success('Note deleted.');
    render();
  }

  // ── Editor Modal ──────────────────────────────────────────────────────
  function openEditor(id = null) {
    const note = id ? getById(id) : null;
    document.getElementById('myNoteModalTitle').textContent = note ? 'Edit Note' : 'New Note';
    document.getElementById('myNoteId').value = note ? note.id : '';
    document.getElementById('myNoteTitle').value = note ? note.title : '';
    document.getElementById('myNoteCategory').value = note ? note.category : '';
    document.getElementById('myNoteContent').value = note ? note.content : '';

    const datalist = document.getElementById('myNoteCategoryList');
    if (datalist) {
      datalist.innerHTML = getCategories().map(c => `<option value="${sanitize(c)}"></option>`).join('');
    }

    openModal('myNoteModal');
    setTimeout(() => document.getElementById('myNoteTitle')?.focus(), 100);
  }

  // ── Render ────────────────────────────────────────────────────────────
  async function render() {
    await fetchAll();
    renderList();
  }

  function renderList() {
    renderCategoryFilters();

    const grid = document.getElementById('myNotesGrid');
    if (!grid) return;

    let notes = notesCache;
    if (activeCategory !== 'all') notes = notes.filter(n => (n.category || 'General') === activeCategory);
    if (searchQuery) {
      notes = notes.filter(n =>
        n.title.toLowerCase().includes(searchQuery) ||
        (n.content || '').toLowerCase().includes(searchQuery));
    }
    notes = [...notes].sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));

    if (!notes.length) {
      grid.innerHTML = `<p style="color:#7a8a9a;text-align:center;padding:40px 0;grid-column:1/-1;">
        ${notesCache.length ? 'No notes match your search/filter.' : 'You haven’t written any notes yet. Click "New Note" to get started.'}
      </p>`;
      return;
    }

    grid.innerHTML = notes.map(n => {
      const snippet = (n.content || '').replace(/\s+/g, ' ').trim().slice(0, 120);
      return `
      <div class="note-card" style="cursor:default;">
        <div class="note-card-header">
          <span class="note-type-badge nursing">${sanitize(n.category || 'General')}</span>
        </div>
        <h4>${sanitize(n.title)}</h4>
        <p>${sanitize(snippet) || '<em>No content yet.</em>'}${snippet.length >= 120 ? '…' : ''}</p>
        <div class="note-card-footer">
          <span><i class="fas fa-calendar-alt" aria-hidden="true"></i> ${formatDate(n.updated_at)}</span>
        </div>
        <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;">
          <button class="btn btn-outline btn-sm" onclick="MyNotes.openEditor('${n.id}')"><i class="fas fa-edit" aria-hidden="true"></i> Edit</button>
          <button class="btn btn-outline btn-sm" onclick="MyNotes.exportPdf('${n.id}')"><i class="fas fa-file-pdf" aria-hidden="true"></i> Export PDF</button>
          <button class="btn btn-outline btn-sm" onclick="MyNotes.print('${n.id}')"><i class="fas fa-print" aria-hidden="true"></i> Print</button>
          <button class="btn btn-outline btn-sm" style="color:#e74c3c;border-color:#e74c3c;" onclick="MyNotes.remove('${n.id}')"><i class="fas fa-trash" aria-hidden="true"></i></button>
        </div>
      </div>`;
    }).join('');
  }

  function renderCategoryFilters() {
    const wrap = document.getElementById('myNotesCategoryFilters');
    if (!wrap) return;
    const cats = getCategories();
    const chips = ['all', ...cats];
    wrap.innerHTML = chips.map(c => `
      <button class="filter-btn my-notes-filter-btn ${c === activeCategory ? 'active' : ''}" data-cat="${sanitize(c)}">
        ${c === 'all' ? 'All' : sanitize(c)}
      </button>`).join('');
    wrap.querySelectorAll('.my-notes-filter-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        activeCategory = btn.dataset.cat;
        renderList();
      });
    });
  }

  // ── Export as PDF ─────────────────────────────────────────────────────
  function exportPdf(id) {
    const note = getById(id);
    if (!note) return;
    if (!window.jspdf) { Toast.error('PDF export is unavailable right now. Please try again.'); return; }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'pt', format: 'a4' });
    const marginX = 56;
    const pageWidth = doc.internal.pageSize.getWidth();
    const maxWidth = pageWidth - marginX * 2;
    const pageHeight = doc.internal.pageSize.getHeight();

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);
    doc.setTextColor(30, 45, 110);
    doc.text(doc.splitTextToSize(note.title, maxWidth), marginX, 60);

    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(120);
    doc.text(`${note.category || 'General'}  •  Updated ${formatDate(note.updated_at)}  •  ${currentUser.name}`, marginX, 80);

    doc.setDrawColor(220);
    doc.line(marginX, 92, pageWidth - marginX, 92);

    doc.setFontSize(12);
    doc.setTextColor(20, 30, 45);
    let y = 118;
    const lineHeight = 16;
    const paragraphs = (note.content || '(No content)').split('\n');

    paragraphs.forEach(paragraph => {
      const lines = doc.splitTextToSize(paragraph || ' ', maxWidth);
      lines.forEach(line => {
        if (y > pageHeight - 60) { doc.addPage(); y = 60; }
        doc.text(line, marginX, y);
        y += lineHeight;
      });
    });

    const filename = (note.title || 'note').replace(/[^a-z0-9]+/gi, '_').toLowerCase().slice(0, 60) || 'note';
    doc.save(`${filename}.pdf`);
    Toast.success('Note exported as PDF.');
  }

  // ── Print ─────────────────────────────────────────────────────────────
  function print(id) {
    const note = getById(id);
    if (!note) return;
    const area = document.getElementById('myNotePrintArea');
    if (!area) return;

    area.innerHTML = `
      <div class="note-print-title">${sanitize(note.title)}</div>
      <div class="note-print-meta">${sanitize(note.category || 'General')} &nbsp;&bull;&nbsp; Updated ${formatDate(note.updated_at)} &nbsp;&bull;&nbsp; ${sanitize(currentUser.name)}</div>
      <hr class="note-print-rule">
      <div class="note-print-content">${sanitize(note.content || '(No content)')}</div>`;

    window.print();
  }

  return { init, render, openEditor, remove, exportPdf, print };
})();
