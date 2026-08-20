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
        <div class="hero-card">
            <div class="result-preview">
                <div class="preview-top"><span>RESULT</span><span class="status-badge">PASS</span></div>
                <h3>Rahul Jha</h3>
                <p>Roll No. 2024001 • BCA • Semester 4</p>
                <div class="preview-score"><strong>80%</strong><small>Overall Percentage</small></div>
                <div class="preview-grid"><div><small>Grade</small><b>A</b></div><div><small>Total</small><b>400 / 500</b></div></div>
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
