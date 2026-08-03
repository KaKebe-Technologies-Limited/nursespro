/**
 * NursesPro Academy - Utility Functions
 * Shared helpers used across all pages
 */

// ─── Toast Notifications ────────────────────────────────────────────────────
const Toast = (() => {
  let container = null;

  function getContainer() {
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    return container;
  }

  function show(message, type = 'info', duration = 4000) {
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle',
                    warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <i class="fas ${icons[type] || icons.info} toast-icon"></i>
      <span class="toast-message">${message}</span>
      <button class="toast-close" aria-label="Close"><i class="fas fa-times"></i></button>`;
    getContainer().appendChild(toast);
    toast.querySelector('.toast-close').addEventListener('click', () => dismiss(toast));
    if (duration > 0) setTimeout(() => dismiss(toast), duration);
    return toast;
  }

  function dismiss(toast) {
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 300);
  }

  return { show,
    success: (msg, d) => show(msg, 'success', d),
    error:   (msg, d) => show(msg, 'error', d),
    warning: (msg, d) => show(msg, 'warning', d),
    info:    (msg, d) => show(msg, 'info', d) };
})();

// ─── Date Helpers ────────────────────────────────────────────────────────────
function formatDate(date) {
  const d = new Date(date);
  return d.toLocaleDateString('en-UG', { year: 'numeric', month: 'long', day: 'numeric' });
}

function addMonths(date, months) {
  const d = new Date(date);
  d.setMonth(d.getMonth() + months);
  return d;
}

function daysRemaining(expiryDate) {
  const now = new Date();
  const expiry = new Date(expiryDate);
  const diff = expiry - now;
  return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));
}

function isExpired(expiryDate) {
  return new Date() > new Date(expiryDate);
}

// ─── Form Validation ─────────────────────────────────────────────────────────
function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePhone(phone) {
  return /^(07|08|03)\d{8}$/.test(phone.replace(/\s+/g, ''));
}

function validatePassword(password) {
  return password.length >= 8;
}

function showFieldError(input, message) {
  input.classList.add('error');
  input.classList.remove('success');
  let err = input.parentElement.querySelector('.form-error');
  if (!err) {
    err = document.createElement('p');
    err.className = 'form-error';
    input.parentElement.appendChild(err);
  }
  err.textContent = message;
  err.classList.add('show');
}

function clearFieldError(input) {
  input.classList.remove('error');
  input.classList.add('success');
  const err = input.parentElement.querySelector('.form-error');
  if (err) err.classList.remove('show');
}

function attachValidation(input, validator, errorMsg) {
  input.addEventListener('blur', () => {
    if (!validator(input.value.trim())) showFieldError(input, errorMsg);
    else clearFieldError(input);
  });
  input.addEventListener('input', () => {
    if (input.classList.contains('error') && validator(input.value.trim()))
      clearFieldError(input);
  });
}

// ─── Password Strength ───────────────────────────────────────────────────────
function getPasswordStrength(password) {
  let score = 0;
  if (password.length >= 8) score++;
  if (password.length >= 12) score++;
  if (/[A-Z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  if (/[^A-Za-z0-9]/.test(password)) score++;
  if (score <= 1) return { level: 'weak', color: '#e74c3c', width: '25%', text: 'Weak' };
  if (score <= 2) return { level: 'fair', color: '#f39c12', width: '50%', text: 'Fair' };
  if (score <= 3) return { level: 'good', color: '#2ecc71', width: '75%', text: 'Good' };
  return { level: 'strong', color: '#27ae60', width: '100%', text: 'Strong' };
}

// ─── Sanitize Input (XSS prevention) ─────────────────────────────────────────
function sanitize(str) {
  const div = document.createElement('div');
  div.appendChild(document.createTextNode(str));
  return div.innerHTML;
}

// ─── LocalStorage Helpers ────────────────────────────────────────────────────
const Store = {
  get: (key) => { try { return JSON.parse(localStorage.getItem(key)); } catch { return null; } },
  set: (key, value) => { localStorage.setItem(key, JSON.stringify(value)); },
  remove: (key) => { localStorage.removeItem(key); },
  clear: () => { localStorage.clear(); }
};
