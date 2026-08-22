<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $stmt = db()->prepare('select id, username, password from admins where username = :username limit 1');
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            unset($admin['password']);
            $_SESSION['admin'] = $admin;
            set_auth_cookie('admin', (int)$admin['id']);
            header('Location: /admin/dashboard.php');
            exit;
        }
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="description" content="Administrator login for the Student Result Management System.">
    <title>Admin Login | Student Result Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-login-page{padding:28px 16px;background:radial-gradient(circle at 50% 0%,#e9efff 0,#f5f7fb 36%,#f8fafc 100%)}
        .admin-login-shell{width:min(440px,100%)}
        .admin-auth-brand{font-size:16px;letter-spacing:-.04em;transition:opacity .2s ease}
        .admin-auth-brand:hover{opacity:.78}
        .admin-login-card{padding:34px 32px 28px;border-radius:24px;box-shadow:0 24px 70px rgba(15,23,42,.10);animation:admin-card-in .35s ease both}
        .admin-login-heading{text-align:left}
        .admin-eyebrow{display:flex;align-items:center;gap:8px;margin-bottom:14px}
        .admin-eyebrow-dot{width:7px;height:7px;border-radius:50%;background:var(--primary);box-shadow:0 0 0 4px #dbeafe;flex:0 0 auto}
        .admin-login-card h1{font-size:34px;line-height:1.08;margin-bottom:7px}
        .admin-login-card .muted{margin:0;font-size:14px;line-height:1.55}
        .admin-login-form{margin-top:26px!important}
        .admin-login-form label{margin-bottom:18px}
        .admin-login-form input{height:46px;margin-top:8px;border-radius:11px;border-color:#d7dee9;padding:11px 13px;transition:border-color .2s ease,box-shadow .2s ease,background .2s ease}
        .admin-login-form input:hover{border-color:#b8c4d5}
        .admin-login-form input:focus{border-color:#6366f1;box-shadow:0 0 0 4px #eef2ff;background:#fff}
        .admin-password-field{display:block;position:relative;margin-top:8px}
        .admin-password-field input{margin-top:0;padding-right:52px}
        .admin-password-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border:0;background:transparent;color:#64748b;border-radius:8px;padding:0;cursor:pointer}
        .admin-password-toggle:hover{background:#f1f5f9;color:#0f172a}
        .admin-password-toggle:focus-visible{outline:2px solid #93c5fd;outline-offset:2px}
        .admin-password-toggle svg{width:18px;height:18px;pointer-events:none}
        .admin-login-submit{height:47px;border-radius:11px;font-size:14px;gap:9px;margin-top:3px;box-shadow:0 9px 22px rgba(37,99,235,.20)}
        .admin-login-submit:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(37,99,235,.24)}
        .admin-login-submit:disabled{cursor:wait;opacity:.8;transform:none}
        .admin-submit-arrow{font-size:18px;line-height:1;transition:transform .2s ease}
        .admin-login-submit:not(:disabled):hover .admin-submit-arrow{transform:translateX(2px)}
        .admin-login-submit.is-loading .admin-submit-arrow:after{content:'•••';font-size:12px;letter-spacing:1px}
        .admin-auth-error{display:flex;align-items:center;gap:9px;margin:20px 0 0;padding:10px 12px;border-radius:11px}
        .admin-alert-icon{width:19px;height:19px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#fee2e2;font-size:11px;font-weight:900;flex:0 0 auto}
        .admin-login-links{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:20px;font-size:13px}
        .admin-login-links a{color:var(--accent);font-weight:700;transition:color .2s ease}
        .admin-login-links a:hover{color:var(--primary-dark);text-decoration:underline;text-underline-offset:3px}
        .admin-login-links .separator{color:#cbd5e1;font-size:12px}
        .admin-auth-footer{text-align:center;color:#94a3b8;font-size:11px;margin-top:18px}
        @keyframes admin-card-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        @media(max-width:600px){
            .admin-login-page{padding:18px 12px}
            .admin-login-shell{width:100%}
            .admin-auth-brand{font-size:14px;margin-bottom:18px!important}
            .admin-login-card{padding:26px 20px 23px;border-radius:20px}
            .admin-login-card h1{font-size:30px}
            .admin-login-form{margin-top:23px!important}
            .admin-login-form label{margin-bottom:16px}
            .admin-login-form input{height:47px}
            .admin-login-submit{height:48px}
            .admin-login-links{margin-top:19px}
            .admin-auth-footer{margin-top:14px}
        }
        @media(prefers-reduced-motion:reduce){.admin-login-card{animation:none}.admin-login-submit,.admin-submit-arrow{transition:none}}
    </style>
</head>
<body class="auth-page admin-login-page">
    <main class="auth-shell admin-login-shell">
        <a class="brand admin-auth-brand" href="../index.php" aria-label="Student Result Management System home">
            Student Result <span>Management</span>
        </a>

        <section class="auth-card admin-login-card" aria-labelledby="admin-login-title">
            <div class="admin-login-heading">
                <p class="eyebrow admin-eyebrow"><span class="admin-eyebrow-dot" aria-hidden="true"></span>Admin Portal</p>
                <h1 id="admin-login-title">Admin Login</h1>
                <p class="muted">Sign in to manage students and academic results.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert error admin-auth-error" role="alert" aria-live="polite">
                    <span class="admin-alert-icon" aria-hidden="true">!</span>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" class="admin-login-form" id="admin-login-form">
                <label for="admin-username">
                    Username
                    <input id="admin-username" name="username" required autocomplete="username" placeholder="admin" value="<?= e($_POST['username'] ?? '') ?>">
                </label>

                <label for="admin-password">
                    <span>Password</span>
                    <span class="admin-password-field">
                        <input id="admin-password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" class="admin-password-toggle" id="admin-password-toggle" aria-label="Show password" aria-pressed="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.5-5.5 9.5-5.5S21.5 12 21.5 12 18 17.5 12 17.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        </button>
                    </span>
                </label>

                <button class="btn btn-primary full admin-login-submit" id="admin-login-submit" type="submit">
                    <span class="admin-submit-label">Sign In</span>
                    <span class="admin-submit-arrow" aria-hidden="true">→</span>
                </button>
            </form>

            <div class="admin-login-links">
                <a href="forgot-password.php">Forgot password?</a>
                <span class="separator" aria-hidden="true">•</span>
                <a href="../index.php">Back to home</a>
            </div>
        </section>

        <footer class="admin-auth-footer">
            Student Result Management System <span aria-hidden="true">•</span> © 2026
        </footer>
    </main>

    <script>
        (function () {
            const form = document.getElementById('admin-login-form');
            const password = document.getElementById('admin-password');
            const toggle = document.getElementById('admin-password-toggle');
            const submit = document.getElementById('admin-login-submit');

            if (toggle && password) {
                toggle.addEventListener('click', function () {
                    const showing = password.type === 'text';
                    password.type = showing ? 'password' : 'text';
                    toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
                    toggle.setAttribute('aria-pressed', String(!showing));
                    toggle.innerHTML = showing
                        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12s3.5-5.5 9.5-5.5S21.5 12 21.5 12 18 17.5 12 17.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>'
                        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a18 18 0 0 1-3.2 3.8"/><path d="M6.1 6.1C3.8 7.8 2.5 12 2.5 12a17.7 17.7 0 0 0 3.7 4.2A9.9 9.9 0 0 0 12 19c1.1 0 2.2-.2 3.2-.5"/></svg>';
                });
            }

            if (form && submit) {
                form.addEventListener('submit', function () {
                    if (!form.checkValidity()) return;
                    submit.disabled = true;
                    submit.classList.add('is-loading');
                    submit.querySelector('.admin-submit-label').textContent = 'Signing in...';
                    submit.querySelector('.admin-submit-arrow').textContent = '';
                });
            }
        })();
    </script>
</body>
</html>
