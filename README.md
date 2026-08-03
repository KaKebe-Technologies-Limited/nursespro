# NursesPro Academy 🎓

A complete, professional e-learning platform for nursing and midwifery students in Uganda.

## 🚀 Quick Start

1. Place the project folder in `xampp/htdocs/nursespro/`
2. Start XAMPP **Apache** and **MySQL**
3. Create the database and load the schema:
   ```bash
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS nursespro CHARACTER SET utf8mb4"
   mysql -u root nursespro < sql/schema.sql
   php sql/seed.php
   ```
   `sql/seed.php` is safe to re-run — it truncates and reseeds every table (curriculum
   modules, users with real bcrypt-hashed passwords, notes, classes, payments).
4. Visit: `http://localhost/nursespro/`

## 🔑 Demo Accounts

All passwords are real, bcrypt-hashed in the database (`password_hash()` / `password_verify()`),
not simulated. Auth is a real PHP session (`$_SESSION`), not a localStorage token.

| Role        | Email                    | Password    | Course / Year                              |
|-------------|---------------------------|-------------|---------------------------------------------|
| Super Admin | admin@nursespro.ac.ug     | Admin1234   | —                                             |
| Tutor       | john@demo.com             | Tutor1234   | —                                             |
| Student     | sarah@demo.com            | Student1234 | Dip. Midwifery (Direct) · Y1 S2 · active     |
| Student     | grace@demo.com            | Grace1234   | Dip. Nursing (Direct) · Y2 S2 · **expired**  |
| Student     | peter@demo.com            | Peter1234   | Dip. Nursing (Extension) · Y1 S1             |
| Student     | immaculate@demo.com       | Immy1234    | Dip. Nursing (Extension) · Y1 S1             |
| Student     | brian@demo.com            | Brian1234   | Dip. Nursing (Extension) · Y1 S2             |
| Student     | diana@demo.com            | Diana1234   | Dip. Nursing (Extension) · Y2 S1             |
| Student     | moses@demo.com            | Moses1234   | Dip. Nursing (Extension) · Y2 S1 · expired   |
| Student     | ritah@demo.com            | Ritah1234   | Dip. Nursing (Extension) · Y1 S2             |
| Student     | emmanuel@demo.com         | Emma1234    | Dip. Nursing (Direct) · Y1 S1                |

## 📁 Project Structure

```
nursespro/
├── index.php                # Public landing page
├── dashboard.php             # Student dashboard (server-side session guard)
├── admin-dashboard.php      # Admin/Tutor panel (server-side session guard)
├── config/
│   └── db.php                # PDO connection
├── includes/
│   └── auth_guard.php       # Session helpers: current_user(), require_login_*, require_role_*
├── api/                      # JSON API consumed by the frontend via fetch()
│   ├── auth.php               # login / register / logout / me
│   ├── profile.php            # update academic details / change password
│   ├── curriculum.php        # list + admin CRUD for course modules
│   ├── notes.php               # list + admin upload (real file)/delete
│   ├── notes_stream.php     # secure PDF streaming (session + access check)
│   ├── classes.php             # list + admin schedule/delete
│   ├── students.php           # superadmin: full student roster + grant/remove
│   ├── overview_stats.php   # superadmin+tutor: aggregate counts only
│   ├── payments.php           # list (own or all) + initiate (simulated MoMo)
│   ├── users.php               # superadmin: tutor/admin accounts
│   └── my_notes.php           # student's personal notes CRUD
├── sql/
│   ├── schema.sql              # CREATE TABLE statements
│   └── seed.php                  # seeds curriculum/users/notes/classes/payments
├── uploads/notes/            # uploaded/generated PDF files (served only via notes_stream.php)
├── css/
│   ├── style.css               # Main styles + modals + forms
│   ├── dashboard.css          # Student dashboard styles
│   ├── admin.css               # Admin panel styles
│   └── responsive.css        # All breakpoints
├── js/
│   ├── utils.js                  # Toast, validation, date helpers
│   ├── curriculum.js          # Curriculum cache (populated from api/curriculum.php)
│   ├── auth.js                   # Auth (fetch-based, session-backed)
│   ├── payment.js              # Payment flow (simulated MoMo, real DB writes)
│   ├── pdf-viewer.js           # Secure PDF viewer (PDF.js, streams via notes_stream.php)
│   ├── notes.js                  # My Notes (personal notes CRUD)
│   ├── dashboard.js            # Student dashboard logic
│   ├── admin.js                  # Admin panel logic (incl. curriculum management)
│   └── index.js                   # Landing page controller
└── README.md
```

## 💳 Payment Flow

Payments run through **Pesapal API v3** (`config/pesapal.php`, `includes/pesapal.php`,
`api/pesapal_initiate.php`, `pesapal_callback.php`, `api/pesapal_ipn.php`) — students pay by
Mobile Money (MTN, Airtel) or card on Pesapal's own hosted checkout page.

1. A student without active access sees a **paywall** on `dashboard.php` that blocks the
   entire portal — nothing else loads until they pay
2. Clicking **Pay Now** calls `api/pesapal_initiate.php`, which authenticates with Pesapal,
   registers the IPN URL (cached in `config/pesapal_ipn_id.txt` after the first call), and
   creates an order
3. The browser is redirected to Pesapal's hosted checkout (`redirect_url`)
4. After payment, Pesapal redirects back to `pesapal_callback.php`, which checks the real
   transaction status and activates access (`payments.status` → `paid`,
   `users.access_expiry` updated)
5. `api/pesapal_ipn.php` is the server-to-server confirmation endpoint for production —
   **note:** while running on `localhost`, Pesapal's servers can't reach this URL, so
   `pesapal_callback.php`'s direct status check is what actually confirms payment during
   local development. Once deployed to a public URL, delete
   `config/pesapal_ipn_id.txt` so the next payment re-registers the IPN with the real URL.

**Credentials**: real keys live in `config/pesapal.php` (gitignored — never commit this
file). `config/pesapal.example.php` is the template. Until keys are set,
`api/pesapal_initiate.php` falls back to an instant, clearly-labeled demo grant so the
paywall UX stays testable. `PESAPAL_ENV` in `config/pesapal.php` controls sandbox vs live —
**the credentials currently configured only authenticate against the live endpoint**, so
`PESAPAL_ENV` is set to `'live'`. Real payments made through this integration are real
transactions.

## 🔒 Security Features

- Real PHP sessions for auth (`password_hash()` / `password_verify()`, no more
  localStorage tokens)
- `dashboard.php` / `admin-dashboard.php` are guarded **server-side** — direct navigation
  while logged out (or logged in as the wrong role) redirects before any HTML renders,
  not just via client-side JS
- Role-based access control enforced in every `api/*.php` endpoint, not just hidden in
  the UI — e.g. a tutor account cannot fetch `api/students.php` even by calling it directly
- PDFs are never served as static files — `api/notes_stream.php` checks session +
  active subscription before streaming any note, mirroring the `/api/notes/:id/stream`
  pattern the original frontend comments already called for; `uploads/notes/` holds the
  raw files, but nothing in the app links to them directly
- PDF protection via PDF.js canvas rendering (no direct file access) + dynamic watermark
  (name + email) on each page
- Right-click and keyboard shortcut blocking (Ctrl+S, Ctrl+P, Ctrl+C) inside the viewer
- XSS sanitization on all rendered user input

## 📱 Responsive Breakpoints

- Desktop: 1200px+
- Tablet: 1024px
- Mobile: 768px (sidebar becomes drawer, bottom nav shows)
- Small: 480px

## 🎨 Color Palette

```css
--primary-blue:  #1a3a5c
--accent-gold:   #c9a84c
--secondary-blue: #2c5f8a
--light-blue:    #e8f0fe
--success-green: #27ae60
--warning-orange: #f39c12
```

## 📞 Contact

- Phone: 0392972444 / 0760167722
- Email: info@nursespro.ac.ug
