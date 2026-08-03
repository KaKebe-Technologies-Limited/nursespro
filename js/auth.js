/**
 * NursesPro Academy - Authentication Module
 * Talks to the real PHP session-backed API in api/auth.php.
 *
 * Pattern used across the app: call `await Auth.init()` once at the top of a
 * page's init flow to populate the cache, then use the synchronous getters
 * (getCurrentUser, isAuthenticated, hasActiveAccess) anywhere after that.
 */

const Auth = (() => {
  let cachedUser = null;

  async function apiPost(action, payload = {}) {
    return fetch('api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, ...payload })
    }).then(r => r.json());
  }

  // ── Init (fetch current session user) ────────────────────────────────────
  async function init() {
    const res = await fetch('api/auth.php?action=me').then(r => r.json());
    cachedUser = res.user || null;
    return cachedUser;
  }

  // ── Register ──────────────────────────────────────────────────────────────
  async function register(data) {
    const res = await apiPost('register', data);
    if (res.success) cachedUser = res.user;
    return res;
  }

  // ── Login ────────────────────────────────────────────────────────────────
  async function login(email, password) {
    const res = await apiPost('login', { email, password });
    if (res.success) cachedUser = res.user;
    return res;
  }

  // ── Logout ───────────────────────────────────────────────────────────────
  async function logout() {
    await apiPost('logout');
    cachedUser = null;
    window.location.href = 'index.php';
  }

  // ── Get Current User (sync — reads the cache populated by init/login) ─────
  function getCurrentUser() { return cachedUser; }

  // ── Is Authenticated ──────────────────────────────────────────────────────
  function isAuthenticated() { return !!cachedUser; }

  // ── Has Active Access ─────────────────────────────────────────────────────
  function hasActiveAccess() {
    if (!cachedUser) return false;
    if (['superadmin', 'tutor'].includes(cachedUser.role)) return true;
    if (!cachedUser.access_expiry) return false;
    return !isExpired(cachedUser.access_expiry);
  }

  // ── Guard protected pages (PHP already redirects server-side; this just
  //    hydrates the client-side cache and is a client-side safety net) ──────
  async function guardPage() {
    const user = await init();
    if (!user) { window.location.href = 'index.php'; return null; }
    return user;
  }

  return { init, register, login, logout, getCurrentUser, isAuthenticated, hasActiveAccess, guardPage };
})();
