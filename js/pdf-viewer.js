/**
 * NursesPro Academy - Secure PDF Viewer
 *
 * Uses PDF.js for rendering. PDFs are rendered canvas-by-canvas to prevent
 * direct file access. All download/print shortcuts are blocked.
 *
 * PDFs are streamed via api/notes_stream.php?id=<note id>, which checks
 * session auth + active subscription server-side before returning any bytes —
 * the raw files under uploads/notes/ are never referenced directly by the client.
 */

// ─── PDF Viewer State ────────────────────────────────────────────────────────
const PDFViewer = (() => {
  let pdfDoc = null;
  let currentPage = 1;
  let totalPages  = 0;
  let scale       = 1.4;
  let currentUser = null;
  let renderTask  = null;
  let previewMode = false;
  let previewCap  = 0;
  let onLockedFn  = null;
  let overlay, canvas, ctx, pageInfo, titleEl, lockEl;

  function init(user) {
    currentUser = user;
    overlay = document.getElementById('pdfViewerOverlay');
    canvas  = document.getElementById('pdfCanvas');
    if (canvas) ctx = canvas.getContext('2d');
    pageInfo = document.getElementById('pdfPageInfo');
    titleEl  = document.getElementById('pdfViewerTitle');
    lockEl   = document.getElementById('pdfPreviewLock');

    bindControls();
    blockDefaultShortcuts();
  }

  function bindControls() {
    const closeBtn = document.getElementById('pdfCloseBtn');
    const prevBtn  = document.getElementById('pdfPrevPage');
    const nextBtn  = document.getElementById('pdfNextPage');
    const zoomIn   = document.getElementById('pdfZoomIn');
    const zoomOut  = document.getElementById('pdfZoomOut');

    closeBtn && closeBtn.addEventListener('click', close);
    prevBtn  && prevBtn.addEventListener('click', () => goToPage(currentPage - 1));
    nextBtn  && nextBtn.addEventListener('click', () => goToPage(currentPage + 1));
    zoomIn   && zoomIn.addEventListener('click', () => { scale = Math.min(scale + 0.2, 3.0); renderPage(currentPage); });
    zoomOut  && zoomOut.addEventListener('click', () => { scale = Math.max(scale - 0.2, 0.6); renderPage(currentPage); });

    // Close on overlay click (but not on canvas)
    overlay && overlay.addEventListener('click', (e) => {
      if (e.target === overlay) close();
    });
  }

  function blockDefaultShortcuts() {
    document.addEventListener('keydown', (e) => {
      if (!overlay || !overlay.classList.contains('active')) return;
      const blocked = ['KeyS','KeyP','KeyC','KeyA','F12'];
      if ((e.ctrlKey || e.metaKey) && blocked.includes(e.code)) {
        e.preventDefault();
        Toast.warning('This action is disabled for content protection.');
        return false;
      }
      if (e.code === 'ArrowRight' || e.code === 'PageDown') goToPage(currentPage + 1);
      if (e.code === 'ArrowLeft'  || e.code === 'PageUp')   goToPage(currentPage - 1);
      if (e.code === 'Escape') close();
    });

    // Disable right-click inside PDF area
    const wrapper = document.getElementById('pdfCanvasWrapper');
    wrapper && wrapper.addEventListener('contextmenu', (e) => e.preventDefault());
  }

  async function open(noteId, title, opts = {}) {
    previewMode = !!opts.preview;
    onLockedFn  = typeof opts.onLocked === 'function' ? opts.onLocked : null;
    previewCap  = 0;
    hideLock();

    if (!previewMode) {
      if (!currentUser) { Toast.error('Please login to view notes.'); return; }
      if (!Auth.hasActiveAccess()) { Toast.error('Your access has expired. Please renew.'); return; }
    }

    if (titleEl) titleEl.textContent = title || 'Study Notes';
    overlay && overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    const pdfUrl = previewMode
      ? `api/public_notes_preview.php?id=${encodeURIComponent(noteId)}`
      : `api/notes_stream.php?id=${encodeURIComponent(noteId)}`;

    try {
      // Load PDF via PDF.js
      const loadingTask = pdfjsLib.getDocument(pdfUrl);
      pdfDoc = await loadingTask.promise;
      totalPages  = pdfDoc.numPages;
      currentPage = 1;
      if (previewMode) previewCap = Math.max(1, Math.ceil(totalPages / 2));
      updatePageInfo();
      await renderPage(currentPage);
    } catch (err) {
      console.error('PDF load error:', err);
      // Demo fallback: show placeholder
      renderPlaceholder(title);
    }
  }

  async function renderPage(pageNum) {
    if (!pdfDoc || !canvas) return;
    if (renderTask) { try { renderTask.cancel(); } catch { /* already finished */ } }
    const page = await pdfDoc.getPage(pageNum);
    const viewport = page.getViewport({ scale });
    canvas.width  = viewport.width;
    canvas.height = viewport.height;
    renderTask = page.render({ canvasContext: ctx, viewport });
    try {
      await renderTask.promise;
    } catch (err) {
      if (err?.name === 'RenderingCancelledException') return;
      throw err;
    } finally {
      renderTask = null;
    }
    drawWatermark();
    updatePageInfo();
  }

  function drawWatermark() {
    if (!ctx) return;
    if (!currentUser && !previewMode) return;
    const watermark = currentUser
      ? `${currentUser.name} | ${currentUser.email} | NursesPro`
      : 'NursesPro Academy | Free Preview | nursespro.ac.ug';
    ctx.save();
    ctx.globalAlpha = 0.07;
    ctx.fillStyle   = '#1a3a5c';
    ctx.font        = 'bold 28px Poppins, sans-serif';
    ctx.textAlign   = 'center';
    const w = canvas.width, h = canvas.height;
    ctx.translate(w / 2, h / 2);
    ctx.rotate(-Math.PI / 6);
    ctx.fillText(watermark, 0, 0);
    ctx.fillText(watermark, 0, 80);
    ctx.fillText(watermark, 0, -80);
    ctx.restore();
  }

  function renderPlaceholder(title) {
    // Fallback if the PDF genuinely fails to load (network issue, missing file, etc.)
    const wrapper = document.getElementById('pdfCanvasWrapper');
    if (!wrapper) return;
    wrapper.innerHTML = `
      <div style="background:#fff;border-radius:8px;padding:60px 40px;text-align:center;max-width:600px;width:90%;position:relative;">
        <div style="font-size:4rem;margin-bottom:20px;">⚠️</div>
        <h3 style="color:#1a3a5c;margin-bottom:12px;font-size:1.3rem;">${sanitize(title)}</h3>
        <p style="color:#5a6a7a;font-size:0.95rem;">
          This note couldn't be loaded right now. Please close this viewer and try again.
        </p>
        <p style="margin-top:16px;font-size:0.8rem;color:#c9a84c;font-weight:600;">
          🔒 Protected by NursesPro Security • ${sanitize(currentUser.name)} • ${sanitize(currentUser.email)}
        </p>
      </div>`;
  }

  function goToPage(num) {
    if (!pdfDoc) return;
    if (num < 1 || num > totalPages) return;
    if (previewMode && previewCap && num > previewCap) { showLock(); return; }
    currentPage = num;
    renderPage(currentPage);
  }

  function showLock() {
    lockEl = document.getElementById('pdfPreviewLock');
    if (lockEl) lockEl.classList.add('active');
    else Toast.info('Free preview limit reached. Create an account to keep reading.');
    if (onLockedFn) onLockedFn();
  }

  function hideLock() {
    lockEl = document.getElementById('pdfPreviewLock');
    if (lockEl) lockEl.classList.remove('active');
  }

  function updatePageInfo() {
    if (!pageInfo) return;
    if (!pdfDoc) { pageInfo.textContent = 'Loading...'; return; }
    pageInfo.textContent = previewMode && previewCap
      ? `Page ${currentPage} of ${totalPages} (free preview: ${previewCap})`
      : `Page ${currentPage} of ${totalPages}`;
  }

  function close() {
    overlay && overlay.classList.remove('active');
    document.body.style.overflow = '';
    pdfDoc = null; currentPage = 1; totalPages = 0;
    previewMode = false; previewCap = 0; onLockedFn = null;
    hideLock();
    // Restore canvas wrapper
    const wrapper = document.getElementById('pdfCanvasWrapper');
    if (wrapper && !wrapper.querySelector('canvas')) {
      wrapper.innerHTML = '<canvas id="pdfCanvas"></canvas><div class="pdf-watermark"></div>';
      canvas = document.getElementById('pdfCanvas');
      if (canvas) ctx = canvas.getContext('2d');
    }
  }

  return { init, open, close };
})();
