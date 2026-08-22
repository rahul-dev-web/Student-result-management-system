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
    <meta name="description" content="Student Result Management System for searching and managing academic results.">
    <title>Student Result Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="home-page">
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="index.php" aria-label="Student Result Management System home">Student Result <span>Management</span></a>
        <div class="nav-links">
            <a class="nav-primary-link" href="result.php">Search Result</a>
            <?php if ($student): ?><a href="student/dashboard.php">Dashboard</a><?php endif; ?>
            <?php if ($admin): ?><a href="admin/dashboard.php">Admin Panel</a><?php endif; ?>
        </div>
    </div>
</header>

<main>
    <section class="home-hero container" aria-labelledby="home-title">
        <div class="hero-copy">
            <p class="eyebrow home-eyebrow"><span class="eyebrow-dot"></span> Academic Result Portal</p>
            <h1 id="home-title">Student Result<br><span>Management System</span></h1>
            <p class="hero-text">View academic results instantly using a roll number. A simple, focused portal for students and administrators.</p>
            <div class="hero-actions">
                <a class="btn btn-primary home-search-btn" href="result.php">Search Result <span aria-hidden="true">→</span></a>
                <div class="secondary-actions">
                    <a class="btn btn-secondary" href="login.php">Student Login</a>
                    <a class="btn btn-ghost" href="admin/login.php">Admin Login</a>
                </div>
            </div>
            <div class="hero-meta" aria-label="Project information">
                <span>College Project</span>
                <span class="meta-separator" aria-hidden="true"></span>
                <span>PHP + PostgreSQL</span>
            </div>
        </div>

        <div class="hero-card qr-card">
            <div class="qr-preview">
                <div class="qr-heading">
                    <div>
                        <span class="eyebrow qr-eyebrow">Quick Access</span>
                        <span class="qr-label">Open the live project</span>
                    </div>
                    <span class="status-badge live-badge"><span aria-hidden="true"></span> LIVE</span>
                </div>
                <div class="qr-frame">
                    <img
                        class="home-qr"
                        src="https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=12&data=https%3A%2F%2Fstudent-result-management-system-lime.vercel.app"
                        alt="QR code to open Student Result Management System"
                        width="320"
                        height="320"
                    >
                </div>
                <h3>Scan to access the result system</h3>
                <p>Use your phone camera to open the live website without typing the URL.</p>
            </div>
        </div>
    </section>

    <section class="features home-features container" aria-labelledby="features-title">
        <div class="section-heading">
            <p class="eyebrow">Core Features</p>
            <h2 id="features-title">Everything you need for result management</h2>
            <p>Simple tools for searching results, managing records and handling calculations.</p>
        </div>
        <div class="feature-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true"><span>⌕</span></div>
                <div class="feature-number">01</div>
                <h3>Result Search</h3>
                <p>Find a student's academic result quickly using their roll number.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true"><span>↗</span></div>
                <div class="feature-number">02</div>
                <h3>Result Management</h3>
                <p>Admins can add, update and remove student records and results.</p>
            </article>
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true"><span>✓</span></div>
                <div class="feature-number">03</div>
                <h3>Automatic Evaluation</h3>
                <p>Total, percentage, grade and pass/fail status are calculated automatically.</p>
            </article>
        </div>
    </section>
</main>

<footer class="site-footer home-footer">
    <div class="container footer-inner">
        <div>
            <strong>Student Result Management System</strong>
            <span>Academic Result Management</span>
        </div>
        <div class="footer-tech">PHP &amp; PostgreSQL <span aria-hidden="true">•</span> © 2026</div>
    </div>
</footer>
</body>
</html>
