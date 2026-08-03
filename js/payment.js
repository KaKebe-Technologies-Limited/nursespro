/**
 * NursesPro Academy - Payment Processing Module
 * Payments go through Pesapal (api/pesapal_initiate.php), which supports Mobile
 * Money (MTN, Airtel) and cards via Pesapal's own hosted checkout page — no
 * separate MTN/Airtel picker needed on our side.
 *
 * Until real Pesapal credentials are added (config/pesapal.php), the backend
 * falls back to an instant, clearly-labeled demo grant so the app stays
 * testable — see api/pesapal_initiate.php.
 */

const Payment = (() => {
  const AMOUNT = 18500;

  async function getPayments() {
    const res = await fetch('api/payments.php').then(r => r.json());
    return res.payments || [];
  }

  // Starts a Pesapal order. On success, either redirects the browser to
  // Pesapal's checkout (real mode) or resolves with the granted expiry (demo mode).
  async function startPayment(phone) {
    return fetch('api/pesapal_initiate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ phone })
    }).then(r => r.json());
  }

  return { getPayments, startPayment, AMOUNT };
})();

// ─── Payment Modal Controller ─────────────────────────────────────────────────
function initPaymentModal() {
  const overlay = document.getElementById('paymentModal');
  if (!overlay) return;

  const phoneInput = document.getElementById('paymentPhone');
  const confirmBtn = document.getElementById('confirmPaymentBtn');
  const stepForm    = document.getElementById('paymentStepForm');
  const stepProcessing = document.getElementById('paymentStepProcessing');
  const stepSuccess    = document.getElementById('paymentStepSuccess');

  confirmBtn && confirmBtn.addEventListener('click', async () => {
    const phone = phoneInput ? phoneInput.value.trim() : '';
    if (!phone || !validatePhone(phone)) {
      Toast.error('Please enter a valid phone number.');
      return;
    }

    stepForm && (stepForm.style.display = 'none');
    stepProcessing && (stepProcessing.style.display = 'flex');

    const res = await Payment.startPayment(phone);

    if (!res.success) {
      stepProcessing && (stepProcessing.style.display = 'none');
      stepForm && (stepForm.style.display = 'block');
      Toast.error(res.message || 'Could not start payment. Please try again.');
      return;
    }

    if (res.mode === 'pesapal') {
      const text = document.getElementById('paymentProcessingText');
      if (text) text.textContent = 'Redirecting to Pesapal…';
      window.location.href = res.redirect_url;
      return;
    }

    // Demo mode: access was granted instantly server-side.
    await Auth.init();
    stepProcessing && (stepProcessing.style.display = 'none');
    if (stepSuccess) {
      stepSuccess.style.display = 'block';
      const expiryEl = document.getElementById('paymentExpiryDate');
      if (expiryEl) expiryEl.textContent = formatDate(res.expiry);
    }
    Toast.success(res.message || 'Payment successful! Access granted for 6 months.');
  });

  // Go to dashboard after success (demo mode)
  const goToDash = document.getElementById('goToDashboardBtn');
  goToDash && goToDash.addEventListener('click', () => {
    window.location.href = 'dashboard.php';
  });
}
