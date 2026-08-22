<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
$success = '';
$roll = trim($_POST['roll_number'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($roll === '' || $email === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = db()->prepare('select id from students where roll_number = :roll and lower(email) = lower(:email) limit 1');
        $stmt->execute(['roll' => $roll, 'email' => $email]);
        $student = $stmt->fetch();

        if (!$student) {
            $error = 'No student account matches that roll number and registered email.';
        } else {
            $update = db()->prepare('update students set password = :password where id = :id');
            $update->execute([
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
                'id' => $student['id'],
            ]);
            $success = 'Password reset successfully. You can now login with your new password.';
            $roll = $email = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Forgot Password | Student</title><link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<div class="auth-shell">
<a class="brand" href="index.php">Student Result <span>Management</span></a>
<div class="auth-card">
<p class="eyebrow">Student Portal</p><h1>Forgot Password</h1>
<p class="muted">Verify your roll number and registered email, then choose a new password.</p>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
<form method="post" novalidate>
<label>Roll Number<input name="roll_number" value="<?= e($roll) ?>" required placeholder="2024001"></label>
<label>Registered Email<input type="email" name="email" value="<?= e($email) ?>" required placeholder="student@example.com" autocomplete="email"></label>
<label>New Password<input type="password" name="new_password" required minlength="6" autocomplete="new-password" placeholder="Minimum 6 characters"></label>
<label>Confirm New Password<input type="password" name="confirm_password" required minlength="6" autocomplete="new-password" placeholder="Repeat new password"></label>
<button class="btn btn-primary full" type="submit">Reset Password</button>
</form>
<p class="form-footer"><a href="login.php">Back to Student Login</a></p>
</div></div></body></html>
