<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll = trim($_POST['roll_number'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($roll === '' || $password === '') {
        $error = 'Please enter roll number and password.';
    } else {
        $stmt = db()->prepare('select id, name, roll_number, email, course, semester, password from students where roll_number = :roll limit 1');
        $stmt->execute(['roll' => $roll]);
        $student = $stmt->fetch();

        if ($student && password_verify($password, $student['password'])) {
            unset($student['password']);
            $_SESSION['student'] = $student;
            set_auth_cookie('student', (int)$student['id']);
            header('Location: /student/dashboard.php');
            exit;
        }
        $error = 'Invalid roll number or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="description" content="Student portal login for the Student Result Management System.">
    <title>Student Login | Student Result Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .student-login-page{padding:28px 16px;background:radial-gradient(circle at 50% 0%,#e9efff 0,#f5f7fb 36%,#f8fafc 100%)}
        .student-login-shell{width:min(440px,100%)}
        .auth-brand{font-size:16px;letter-spacing:-.04em;transition:opacity .2s ease}
        .auth-brand:hover{opacity:.78}
        .student-login-card{padding:34px 32px 28px;border-radius:24px;box-shadow:0 24px 70px rgba(15,23,42,.10);animation:login-card-in .35s ease both}
        .student-login-heading{text-align:left}
        .auth-eyebrow{display:flex;align-items:center;gap:8px;margin-bottom:14px}
        .auth-eyebrow-dot{width:7px;height:7px;border-radius:50%;background:var(--primary);box-shadow:0 0 0 4px #dbeafe;flex:0 0 auto}
        .student-login-card h1{font-size:34px;line-height:1.08;margin-bottom:7px}
        .student-login-card .muted{margin:0;font-size:14px;line-height:1.55}
        .student-login-form{margin-top:26px!important}
        .student-login-form label{margin-bottom:18px}
        .student-login-form input{height:46px;margin-top:8px;border-radius:11px;border-color:#d7dee9;padding:11px 13px;transition:border-color .2s ease,box-shadow .2s ease,background .2s ease}
        .student-login-form input:hover{border-color:#b8c4d5}
        .student-login-form input:focus{border-color:#6366f1;box-shadow:0 0 0 4px #eef2ff;background:#fff}
        .password-field{display:block;position:relative;margin-top:8px}
        .password-field input{margin-top:0;padding-right:66px}
        .password-toggle{position:absolute;right:8px;top:50%;transform:translateY(-50%);border:0;background:#f1f5f9;color:#475569;border-radius:8px;padding:6px 9px;font-size:11px;font-weight:800;cursor:pointer;min-height:30px}
        .password-toggle:hover{background:#e2e8f0;color:#0f172a}
        .password-toggle:focus-visible{outline:2px solid #93c5fd;outline-offset:2px}
        .student-login-submit{height:47px;border-radius:11px;font-size:14px;gap:9px;margin-top:3px;box-shadow:0 9px 22px rgba(37,99,235,.20)}
        .student-login-submit:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(37,99,235,.24)}
        .student-login-submit:disabled{cursor:wait;opacity:.8;transform:none}
        .submit-arrow{font-size:18px;line-height:1;transition:transform .2s ease}
        .student-login-submit:not(:disabled):hover .submit-arrow{transform:translateX(2px)}
        .student-login-submit.is-loading .submit-arrow:after{content:'•••';font-size:12px;letter-spacing:1px}
        .auth-error{display:flex;align-items:center;gap:9px;margin:20px 0 0;padding:10px 12px;border-radius:11px}
        .alert-icon{width:19px;height:19px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#fee2e2;font-size:11px;font-weight:900;flex:0 0 auto}
        .student-login-links{display:flex;flex-direction:column;align-items:center;gap:9px;margin-top:21px;font-size:13px}
        .student-login-links a{color:var(--accent);font-weight:700;transition:color .2s ease}
        .student-login-links a:hover{color:var(--primary-dark);text-decoration:underline;text-underline-offset:3px}
        .login-divider{width:32px;height:1px;background:var(--line);margin:1px 0}
        .student-login-links a:last-child{font-size:12px;color:#64748b;font-weight:600}
        .student-login-links a:last-child:hover{color:var(--primary)}
        .auth-footer{text-align:center;color:#94a3b8;font-size:11px;margin-top:18px}
        .auth-footer span{padding:0 5px;color:#cbd5e1}
        @keyframes login-card-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        @media(max-width:600px){
            .student-login-page{padding:18px 12px}
            .student-login-shell{width:100%}
            .auth-brand{font-size:14px;margin-bottom:18px!important}
            .student-login-card{padding:26px 20px 23px;border-radius:20px}
            .student-login-card h1{font-size:30px}
            .student-login-form{margin-top:23px!important}
            .student-login-form label{margin-bottom:16px}
            .student-login-form input{height:47px}
            .student-login-submit{height:48px}
            .student-login-links{margin-top:19px}
            .auth-footer{margin-top:14px}
        }
        @media(prefers-reduced-motion:reduce){.student-login-card{animation:none}.student-login-submit,.submit-arrow{transition:none}}
    </style>
</head>
<body class="auth-page student-login-page">
    <main class="auth-shell student-login-shell">
        <a class="brand auth-brand" href="index.php" aria-label="Student Result Management System home">
            Student Result <span>Management</span>
        </a>

        <section class="auth-card student-login-card" aria-labelledby="student-login-title">
            <div class="student-login-heading">
                <p class="eyebrow auth-eyebrow"><span class="auth-eyebrow-dot" aria-hidden="true"></span>Student Portal</p>
                <h1 id="student-login-title">Student Login</h1>
                <p class="muted">Sign in to view your academic result.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert error auth-error" role="alert" aria-live="polite">
                    <span class="alert-icon" aria-hidden="true">!</span>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" class="student-login-form" id="student-login-form">
                <label for="roll-number">
                    Roll Number
                    <input id="roll-number" name="roll_number" required autocomplete="username" inputmode="numeric" placeholder="2024001" value="<?= e($_POST['roll_number'] ?? '') ?>">
                </label>

                <label for="student-password">
                    <span>Password</span>
                    <span class="password-field">
                        <input id="student-password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        <button type="button" class="password-toggle" id="password-toggle" aria-label="Show password" aria-pressed="false">Show</button>
                    </span>
                </label>

                <button class="btn btn-primary full student-login-submit" id="student-login-submit" type="submit">
                    <span class="submit-label">Sign In</span>
                    <span class="submit-arrow" aria-hidden="true">→</span>
                </button>
            </form>

            <div class="student-login-links">
                <a href="forgot-password.php">Forgot password?</a>
                <span class="login-divider" aria-hidden="true"></span>
                <a href="result.php">View result without login <span aria-hidden="true">→</span></a>
            </div>
        </section>

        <footer class="auth-footer">
            Student Result Management System <span aria-hidden="true">•</span> © 2026
        </footer>
    </main>

    <script>
        (function () {
            const form = document.getElementById('student-login-form');
            const password = document.getElementById('student-password');
            const toggle = document.getElementById('password-toggle');
            const submit = document.getElementById('student-login-submit');

            if (toggle && password) {
                toggle.addEventListener('click', function () {
                    const showing = password.type === 'text';
                    password.type = showing ? 'password' : 'text';
                    toggle.textContent = showing ? 'Show' : 'Hide';
                    toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
                    toggle.setAttribute('aria-pressed', String(!showing));
                });
            }

            if (form && submit) {
                form.addEventListener('submit', function () {
                    if (!form.checkValidity()) return;
                    submit.disabled = true;
                    submit.classList.add('is-loading');
                    submit.querySelector('.submit-label').textContent = 'Signing in...';
                    submit.querySelector('.submit-arrow').textContent = '';
                });
            }
        })();
    </script>
</body>
</html>
