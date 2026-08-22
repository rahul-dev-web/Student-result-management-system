<?php
session_start();
$student = $_SESSION['student'] ?? null;
$admin = $_SESSION['admin'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="index.php">Student Result <span>Management</span></a>
        <div class="nav-links">
            <a href="result.php">Search Result</a>
            <?php if ($student): ?><a href="student/dashboard.php">Dashboard</a><?php endif; ?>
            <?php if ($admin): ?><a href="admin/dashboard.php">Admin Panel</a><?php endif; ?>
        </div>
    </div>
</header>

<main>
    <section class="hero container">
        <div class="hero-copy">
            <p class="eyebrow">College Project • PHP + Supabase</p>
            <h1>Student Result<br><span>Management System</span></h1>
            <p class="hero-text">A simple platform to search, view and manage student academic results.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="result.php">Search Result</a>
                <a class="btn btn-secondary" href="login.php">Student Login</a>
                <a class="btn btn-ghost" href="admin/login.php">Admin Login</a>
            </div>
        </div>
        <div class="hero-card qr-card">
            <div class="qr-preview">
                <div class="qr-heading">
                    <span class="eyebrow">Quick Access</span>
                    <span class="status-badge">LIVE</span>
                </div>
                <img
                    class="home-qr"
                    src="https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=12&data=https%3A%2F%2Fstudent-result-management-system-lime.vercel.app"
                    alt="QR code to open Student Result Management System"
                    width="320"
                    height="320"
                >
                <h3>Scan to Open</h3>
                <p>Scan this QR code with your phone to open the project directly.</p>
            </div>
        </div>
    </section>

    <section class="features container">
        <article class="feature-card"><div class="icon">01</div><h3>Student Results</h3><p>Search a result quickly using the student's roll number.</p></article>
        <article class="feature-card"><div class="icon">02</div><h3>Easy Management</h3><p>Admin can add, update and remove students and results.</p></article>
        <article class="feature-card"><div class="icon">03</div><h3>Automatic Calculation</h3><p>Total, percentage, grade and pass/fail status are calculated automatically.</p></article>
    </section>
</main>

<footer class="site-footer"><div class="container">Student Result Management System • PHP &amp; Supabase PostgreSQL</div></footer>
</body>
</html>
