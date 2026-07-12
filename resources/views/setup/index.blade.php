<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup Wizard — {{ config('app.name', 'Trade-Swing') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { background: #050f1a; }
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        .check-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #1a3a4a; }
        .check-row:last-child { border-bottom: none; }
        .badge-ok  { background:#22c55e22; color:#22c55e; font-size:10px; padding:2px 10px; border-radius:9999px; text-transform:uppercase; letter-spacing:.1em; }
        .badge-fail{ background:#ef444422; color:#ef4444; font-size:10px; padding:2px 10px; border-radius:9999px; text-transform:uppercase; letter-spacing:.1em; }
        .input-wrap { background:#0a1929; border:1px solid #1a3a4a; border-radius:8px; padding:12px 16px; }
        .input-wrap label { display:block; color:#64899a; font-size:9px; letter-spacing:.3em; text-transform:uppercase; margin-bottom:4px; }
        .input-wrap input, .input-wrap select {
            width:100%; background:transparent; outline:none; color:#fff; font-size:14px;
        }
        .input-wrap select option { background:#0a1929; }
        .btn-primary { background:#00c9a7; color:#050f1a; font-weight:700; padding:14px; border-radius:8px; width:100%; text-transform:uppercase; letter-spacing:.15em; font-size:12px; cursor:pointer; transition:.2s; border:none; display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-primary:hover { background:#00a88a; }
        .btn-primary:disabled { opacity:.5; cursor:not-allowed; }
        .btn-outline { background:transparent; color:#64899a; font-weight:600; padding:14px 24px; border-radius:8px; font-size:12px; cursor:pointer; transition:.2s; border:1px solid #1a3a4a; letter-spacing:.1em; text-transform:uppercase; }
        .btn-outline:hover { border-color:#64899a; color:#fff; }
        .progress-step { display:flex; flex-direction:column; align-items:center; gap:4px; flex:1; }
        .progress-dot { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; transition:.3s; }
        .progress-dot.done  { background:#00c9a7; color:#050f1a; }
        .progress-dot.active{ background:#00c9a7; color:#050f1a; box-shadow:0 0 0 4px rgba(0,201,167,.2); }
        .progress-dot.pending{ background:#0a1929; color:#64899a; border:1px solid #1a3a4a; }
        .progress-line { flex:1; height:1px; background:#1a3a4a; margin-top:-18px; }
        .progress-line.done { background:#00c9a7; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .spin { animation: spin 1s linear infinite; }
        .card { background:#0d1f35; border:1px solid #1a3a4a; border-radius:12px; padding:20px; }
    </style>
</head>
<body>

<div class="min-h-screen flex flex-col items-center justify-start py-8 px-4">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-[#00c9a7] flex items-center justify-center mx-auto mb-3">
            <x-icon name="chart-line" class="text-[#050f1a] text-2xl" />
        </div>
        <p class="text-white text-xl font-bold tracking-widest uppercase">{{ config('app.name', 'Trade-Swing') }}</p>
        <p class="text-[#64899a] text-xs tracking-[.3em] uppercase mt-1">Setup Wizard</p>
    </div>

    {{-- Progress Bar --}}
    <div class="w-full max-w-2xl mb-6 px-2">
        <div class="flex items-center">
            @php $steps = ['Requirements','Database','App','Payment','Support','Admin','Install']; @endphp
            @foreach($steps as $i => $label)
                <div class="progress-step" id="prog-step-{{ $i }}">
                    <div class="progress-dot {{ $i === 0 ? 'active' : 'pending' }}" id="prog-dot-{{ $i }}">{{ $i + 1 }}</div>
                    <span style="font-size:9px; color:#64899a; letter-spacing:.05em; text-transform:uppercase; white-space:nowrap;">{{ $label }}</span>
                </div>
                @if(!$loop->last)
                <div class="progress-line" id="prog-line-{{ $i }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Wizard Card --}}
    <div class="w-full max-w-2xl">

        {{-- ─── STEP 0: Requirements ─────────────────────────────── --}}
        <div class="step-panel active card" id="step-0">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">System Requirements</p>
            <p class="text-[#64899a] text-xs mb-4">Checking that your server meets all prerequisites.</p>

            <div class="mb-4">
                <div class="check-row">
                    <span class="text-[#64899a] text-xs">PHP Version (≥ 8.2)</span>
                    <span>
                        <span class="text-[#64899a] text-xs mr-2">{{ $checks['phpVersion'] }}</span>
                        <span class="{{ $checks['phpOk'] ? 'badge-ok' : 'badge-fail' }}">{{ $checks['phpOk'] ? 'Pass' : 'Fail' }}</span>
                    </span>
                </div>
                @foreach($checks['extChecks'] as $ext => $loaded)
                <div class="check-row">
                    <span class="text-[#64899a] text-xs">Extension: {{ $ext }}</span>
                    <span class="{{ $loaded ? 'badge-ok' : 'badge-fail' }}">{{ $loaded ? 'Loaded' : 'Missing' }}</span>
                </div>
                @endforeach
                @foreach($checks['permissions'] as $path => $writable)
                <div class="check-row">
                    <span class="text-[#64899a] text-xs">Writable: {{ $path }}</span>
                    <span class="{{ $writable ? 'badge-ok' : 'badge-fail' }}">{{ $writable ? 'OK' : 'Not Writable' }}</span>
                </div>
                @endforeach
            </div>

            @if(!$checks['allPass'])
            <div style="background:#ef444411; border:1px solid #ef444433; border-radius:8px; padding:12px 16px;" class="mb-4">
                <p class="text-[#ef4444] text-xs">Some requirements are not met. Please fix the issues above before continuing.</p>
            </div>
            @endif

            <button class="btn-primary" onclick="goTo(1)" {{ !$checks['allPass'] ? 'disabled' : '' }}>
                Continue <x-icon name="arrow-right" />
            </button>
        </div>

        {{-- ─── STEP 1: Database ─────────────────────────────────── --}}
        <div class="step-panel card" id="step-1">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">Database Configuration</p>
            <p class="text-[#64899a] text-xs mb-4">Enter your MySQL database credentials.</p>

            <div class="flex flex-col gap-3 mb-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="input-wrap">
                        <label>DB Host</label>
                        <input type="text" name="db_host" value="127.0.0.1" placeholder="127.0.0.1">
                    </div>
                    <div class="input-wrap">
                        <label>DB Port</label>
                        <input type="number" name="db_port" value="3306" placeholder="3306">
                    </div>
                </div>
                <div class="input-wrap">
                    <label>Database Name</label>
                    <input type="text" name="db_database" placeholder="e.g. agridigital">
                </div>
                <div class="input-wrap">
                    <label>Username</label>
                    <input type="text" name="db_username" placeholder="e.g. root">
                </div>
                <div class="input-wrap">
                    <label>Password</label>
                    <input type="password" name="db_password" placeholder="Leave blank if none">
                </div>
            </div>

            <div id="db-feedback" class="mb-4 hidden"></div>

            <div class="flex gap-3">
                <button class="btn-outline" onclick="goTo(0)"><x-icon name="arrow-left" class="mr-1" /> Back</button>
                <button class="btn-primary flex-1" id="btn-test-db" onclick="testDbConnection()">
                    <x-icon name="plug" /> Test Connection
                </button>
            </div>
        </div>

        {{-- ─── STEP 2: App Settings ─────────────────────────────── --}}
        <div class="step-panel card" id="step-2">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">Application Settings</p>
            <p class="text-[#64899a] text-xs mb-4">Basic configuration for your platform.</p>

            <div class="flex flex-col gap-3 mb-4">
                <div class="input-wrap">
                    <label>App Name</label>
                    <input type="text" name="app_name" placeholder="e.g. Trade-Swing" value="Trade-Swing">
                </div>
                <div class="input-wrap">
                    <label>App URL (no trailing slash)</label>
                    <input type="text" name="app_url" placeholder="https://yourdomain.com">
                </div>
                <div class="input-wrap">
                    <label>Environment</label>
                    <select name="app_env">
                        <option value="production">Production</option>
                        <option value="local">Local / Development</option>
                    </select>
                </div>
                <div class="input-wrap">
                    <label>App Download URL <span style="color:#1a3a4a">(optional)</span></label>
                    <input type="text" name="app_download_url" placeholder="https://yourdomain.com/app">
                </div>
                <div class="input-wrap">
                    <label>WhatsApp Group URL <span style="color:#1a3a4a">(optional)</span></label>
                    <input type="text" name="whatsapp_group_url" placeholder="https://chat.whatsapp.com/...">
                </div>
            </div>

            <div class="flex gap-3">
                <button class="btn-outline" onclick="goTo(1)"><x-icon name="arrow-left" class="mr-1" /> Back</button>
                <button class="btn-primary flex-1" onclick="validateAndGo(2, 3)">
                    Continue <x-icon name="arrow-right" />
                </button>
            </div>
        </div>

        {{-- ─── STEP 3: Payment (Palpluss) ───────────────────────── --}}
        <div class="step-panel card" id="step-3">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">Payment Gateway — Palpluss</p>
            <p class="text-[#64899a] text-xs mb-4">Enter your Palpluss API credentials for M-Pesa STK push and payouts.</p>

            <div class="flex flex-col gap-3 mb-4">
                <div class="input-wrap">
                    <label>Palpluss Base URL</label>
                    <input type="text" name="palpluss_base_url" value="https://api.palpluss.com/v1" placeholder="https://api.palpluss.com/v1">
                </div>
                <div class="input-wrap">
                    <label>Auth Token (API Key)</label>
                    <input type="text" name="palpluss_auth" placeholder="pp_live_...">
                </div>
                <div class="input-wrap">
                    <label>Channel ID</label>
                    <input type="text" name="palpluss_channel_id" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                </div>
            </div>

            <div style="background:#f59e0b11; border:1px solid #f59e0b33; border-radius:8px; padding:12px 16px;" class="mb-4">
                <p class="text-[#f59e0b] text-xs"><x-icon name="triangle-exclamation" class="mr-1" /> Callback URL is auto-set to <code style="font-size:10px;">APP_URL/callback</code> — no manual entry needed.</p>
            </div>

            <div class="flex gap-3">
                <button class="btn-outline" onclick="goTo(2)"><x-icon name="arrow-left" class="mr-1" /> Back</button>
                <button class="btn-primary flex-1" onclick="validateAndGo(3, 4)">
                    Continue <x-icon name="arrow-right" />
                </button>
            </div>
        </div>

        {{-- ─── STEP 4: Support Contacts ─────────────────────────── --}}
        <div class="step-panel card" id="step-4">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">Support & Contact</p>
            <p class="text-[#64899a] text-xs mb-4">These appear on the forgot password page and account screen.</p>

            <div class="flex flex-col gap-3 mb-4">
                <div class="input-wrap">
                    <label>WhatsApp Support URL</label>
                    <input type="text" name="support_url" placeholder="https://wa.me/2547...">
                </div>
                <div class="input-wrap">
                    <label>Support Email</label>
                    <input type="email" name="support_email" placeholder="support@yourdomain.com">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="input-wrap">
                        <label>Support Phone</label>
                        <input type="text" name="support_phone" placeholder="07xxxxxxxx">
                    </div>
                    <div class="input-wrap">
                        <label>Network</label>
                        <select name="support_network">
                            <option value="Safaricom">Safaricom</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Telkom">Telkom</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button class="btn-outline" onclick="goTo(3)"><x-icon name="arrow-left" class="mr-1" /> Back</button>
                <button class="btn-primary flex-1" onclick="goTo(5)">
                    Continue <x-icon name="arrow-right" />
                </button>
            </div>
        </div>

        {{-- ─── STEP 5: Admin Account ─────────────────────────────── --}}
        <div class="step-panel card" id="step-5">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">Admin Account</p>
            <p class="text-[#64899a] text-xs mb-4">Create the first administrator account.</p>

            <div class="flex flex-col gap-3 mb-4">
                <div class="input-wrap">
                    <label>Admin Email</label>
                    <input type="email" name="admin_email" placeholder="admin@yourdomain.com">
                </div>
                <div class="input-wrap">
                    <label>Phone Number</label>
                    <input type="text" name="admin_phone" placeholder="07xxxxxxxx">
                </div>
                <div class="input-wrap">
                    <label>Password (min 8 chars)</label>
                    <input type="password" id="admin_password" name="admin_password" placeholder="••••••••">
                </div>
                <div class="input-wrap">
                    <label>Confirm Password</label>
                    <input type="password" id="admin_password_confirm" placeholder="••••••••">
                </div>
            </div>

            <div id="admin-error" class="mb-4 hidden"></div>

            <div class="flex gap-3">
                <button class="btn-outline" onclick="goTo(4)"><x-icon name="arrow-left" class="mr-1" /> Back</button>
                <button class="btn-primary flex-1" onclick="validateAdmin()">
                    Continue <x-icon name="arrow-right" />
                </button>
            </div>
        </div>

        {{-- ─── STEP 6: Install ──────────────────────────────────── --}}
        <div class="step-panel card" id="step-6">
            <p class="text-white text-sm font-semibold tracking-wide mb-1">Ready to Install</p>
            <p class="text-[#64899a] text-xs mb-4">Review your configuration and click Install.</p>

            {{-- Summary --}}
            <div id="review-summary" class="mb-5 flex flex-col gap-2 text-xs"></div>

            <div id="install-log" class="hidden mb-4" style="background:#0a1929; border:1px solid #1a3a4a; border-radius:8px; padding:14px;">
                <div id="install-steps" class="flex flex-col gap-2 text-xs text-[#64899a]"></div>
            </div>

            <div id="install-error" class="hidden mb-4" style="background:#ef444411; border:1px solid #ef444433; border-radius:8px; padding:12px 16px;">
                <p class="text-[#ef4444] text-xs" id="install-error-msg"></p>
            </div>

            <div class="flex gap-3">
                <button class="btn-outline" id="btn-back-install" onclick="goTo(5)"><x-icon name="arrow-left" class="mr-1" /> Back</button>
                <button class="btn-primary flex-1" id="btn-install" onclick="runInstall()">
                    <x-icon name="rocket" /> Install Now
                </button>
            </div>
        </div>

    </div>

    <p class="text-[#1a3a4a] text-xs mt-8">{{ config('app.name', 'Trade-Swing') }} · Setup Wizard</p>
</div>

<script>
// ── State ────────────────────────────────────────────────────────────────────
let currentStep = 0;
const totalSteps = 7;
let dbTested = false;

// ── Navigation ────────────────────────────────────────────────────────────────
function goTo(n) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('step-' + n).classList.add('active');

    // Update progress dots
    for (let i = 0; i < totalSteps; i++) {
        const dot = document.getElementById('prog-dot-' + i);
        if (i < n)       { dot.className = 'progress-dot done'; dot.innerHTML = '✓'; }
        else if (i === n){ dot.className = 'progress-dot active'; dot.innerHTML = (i + 1); }
        else             { dot.className = 'progress-dot pending'; dot.innerHTML = (i + 1); }

        if (i < totalSteps - 1) {
            const line = document.getElementById('prog-line-' + i);
            line.className = 'progress-line' + (i < n ? ' done' : '');
        }
    }

    if (n === 6) buildReview();
    currentStep = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function val(name) {
    const el = document.querySelector('[name="' + name + '"]');
    return el ? el.value.trim() : '';
}

function showError(id, msg) {
    const el = document.getElementById(id);
    el.innerHTML = '<p style="color:#ef4444;font-size:12px;">' + msg + '</p>';
    el.classList.remove('hidden');
}

function clearError(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.innerHTML = '';
}

function validateAndGo(fromStep, toStep) {
    const required = {
        2: [['app_name','App Name'], ['app_url','App URL']],
        3: [['palpluss_auth','Auth Token'], ['palpluss_channel_id','Channel ID']],
    };
    const fields = required[fromStep] || [];
    for (const [name, label] of fields) {
        if (!val(name)) { alert(label + ' is required.'); return; }
    }
    goTo(toStep);
}

// ── Step 1: DB Test ──────────────────────────────────────────────────────────
function testDbConnection() {
    const btn = document.getElementById('btn-test-db');
    const fb  = document.getElementById('db-feedback');
    clearError('db-feedback');

    const payload = {
        db_host:     val('db_host'),
        db_port:     val('db_port'),
        db_database: val('db_database'),
        db_username: val('db_username'),
        db_password: val('db_password'),
    };

    btn.disabled = true;
    btn.innerHTML = '<svg class="spin" style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg> Testing…';

    fetch('{{ route("setup.test-db") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            dbTested = true;
            fb.innerHTML = '<p style="color:#22c55e; font-size:12px;"><x-icon name="circle-check" class="mr-1" />' + data.message + '</p>';
            fb.classList.remove('hidden');
            btn.innerHTML = '<x-icon name="circle-check" /> Connected — Continue';
            btn.onclick = function() { goTo(2); };
        } else {
            dbTested = false;
            fb.innerHTML = '<p style="color:#ef4444; font-size:12px;"><x-icon name="circle-xmark" class="mr-1" />' + data.message + '</p>';
            fb.classList.remove('hidden');
            btn.innerHTML = '<x-icon name="plug" /> Retry Connection';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<x-icon name="plug" /> Retry Connection';
        fb.innerHTML = '<p style="color:#ef4444; font-size:12px;">Request failed. Check your network.</p>';
        fb.classList.remove('hidden');
    });
}

// ── Step 5: Admin Validation ──────────────────────────────────────────────────
function validateAdmin() {
    const email = val('admin_email');
    const phone = val('admin_phone');
    const pass  = document.getElementById('admin_password').value;
    const conf  = document.getElementById('admin_password_confirm').value;

    if (!email) { showError('admin-error', 'Admin email is required.'); return; }
    if (!phone) { showError('admin-error', 'Phone number is required.'); return; }
    if (pass.length < 8) { showError('admin-error', 'Password must be at least 8 characters.'); return; }
    if (pass !== conf)   { showError('admin-error', 'Passwords do not match.'); return; }

    clearError('admin-error');
    goTo(6);
}

// ── Step 6: Review Summary ────────────────────────────────────────────────────
function buildReview() {
    const rows = [
        ['App Name',      val('app_name')],
        ['App URL',       val('app_url')],
        ['Environment',   val('app_env')],
        ['Database',      val('db_database') + ' @ ' + val('db_host') + ':' + val('db_port')],
        ['DB User',       val('db_username')],
        ['Palpluss Auth', val('palpluss_auth').substring(0,12) + '…'],
        ['Channel ID',    val('palpluss_channel_id')],
        ['Admin Email',   val('admin_email')],
        ['Admin Phone',   val('admin_phone')],
    ];

    const html = rows.map(([k, v]) =>
        `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #1a3a4a;">
            <span style="color:#64899a;">${k}</span>
            <span style="color:#fff;font-size:12px;max-width:60%;text-align:right;word-break:break-all;">${v || '—'}</span>
        </div>`
    ).join('');

    document.getElementById('review-summary').innerHTML = html;
}

// ── Step 6: Run Install ───────────────────────────────────────────────────────
function runInstall() {
    const btn  = document.getElementById('btn-install');
    const back = document.getElementById('btn-back-install');
    const log  = document.getElementById('install-log');
    const steps = document.getElementById('install-steps');

    document.getElementById('install-error').classList.add('hidden');
    btn.disabled = true;
    back.disabled = true;
    btn.innerHTML = '<svg class="spin" style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg> Installing…';
    log.classList.remove('hidden');

    const logStep = (msg, ok = null) => {
        const icon = ok === null ? '⏳' : (ok ? '✅' : '❌');
        steps.innerHTML += `<div>${icon} ${msg}</div>`;
    };

    logStep('Writing .env configuration…');
    logStep('Connecting to database…');
    logStep('Running database migrations…');
    logStep('Creating admin account…');

    const payload = {
        db_host:             val('db_host'),
        db_port:             val('db_port'),
        db_database:         val('db_database'),
        db_username:         val('db_username'),
        db_password:         val('db_password'),
        app_name:            val('app_name'),
        app_url:             val('app_url'),
        app_env:             val('app_env'),
        app_download_url:    val('app_download_url'),
        whatsapp_group_url:  val('whatsapp_group_url'),
        palpluss_auth:       val('palpluss_auth'),
        palpluss_channel_id: val('palpluss_channel_id'),
        palpluss_base_url:   val('palpluss_base_url'),
        support_url:         val('support_url'),
        support_email:       val('support_email'),
        support_phone:       val('support_phone'),
        support_network:     val('support_network'),
        admin_email:         val('admin_email'),
        admin_phone:         val('admin_phone'),
        admin_password:      document.getElementById('admin_password').value,
    };

    fetch('{{ route("setup.install") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(data => {
        steps.innerHTML = '';
        if (data.success) {
            logStep('.env written', true);
            logStep('Database migrations complete', true);
            logStep('Admin account created', true);
            logStep('Installation complete!', true);
            btn.innerHTML = '<x-icon name="circle-check" /> Installed — Redirecting…';
            setTimeout(() => { window.location.href = data.redirect; }, 1800);
        } else {
            logStep('Installation failed: ' + data.message, false);
            document.getElementById('install-error-msg').textContent = data.message;
            document.getElementById('install-error').classList.remove('hidden');
            btn.disabled = false;
            back.disabled = false;
            btn.innerHTML = '<x-icon name="rocket" /> Retry Install';
        }
    })
    .catch(err => {
        steps.innerHTML = '';
        logStep('Network error: ' + err.message, false);
        document.getElementById('install-error-msg').textContent = 'Request failed. Check server logs.';
        document.getElementById('install-error').classList.remove('hidden');
        btn.disabled = false;
        back.disabled = false;
        btn.innerHTML = '<x-icon name="rocket" /> Retry Install';
    });
}
</script>
</body>
</html>
