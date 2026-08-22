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
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Admin Login</title><link rel="stylesheet" href="../assets/css/style.css"></head><body class="auth-page"><div class="auth-shell"><a class="brand" href="../index.php">Student Result <span>Management</span></a><div class="auth-card"><p class="eyebrow">Administration</p><h1>Admin Login</h1><p class="muted">Manage students and results.</p><?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><label>Username<input name="username" required autocomplete="username" placeholder="admin"></label><label>Password<input type="password" name="password" required autocomplete="current-password" placeholder="••••••••"></label><button class="btn btn-primary full" type="submit">Login to Admin Panel</button></form><p class="form-footer"><a href="forgot-password.php">Forgot password?</a></p></div></div></body></html>
