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
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Student Login</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="auth-page"><div class="auth-shell"><a class="brand" href="index.php">Student Result <span>Management</span></a><div class="auth-card"><p class="eyebrow">Student Portal</p><h1>Student Login</h1><p class="muted">Sign in to view your result.</p><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><label>Roll Number<input name="roll_number" required autocomplete="username" placeholder="2024001"></label><label>Password<input type="password" name="password" required autocomplete="current-password" placeholder="••••••••"></label><button class="btn btn-primary full" type="submit">Login</button></form><p class="form-footer"><a href="forgot-password.php">Forgot password?</a><br><a href="result.php">Search result by roll number</a></p></div></div></body></html>
