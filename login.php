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
