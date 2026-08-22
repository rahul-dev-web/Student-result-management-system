<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$pdo = db();
$students = (int)$pdo->query('select count(*) from students')->fetchColumn();
$results = (int)$pdo->query('select count(*) from results')->fetchColumn();
$passed = (int)$pdo->query("select count(*) from results where status = 'PASS'")->fetchColumn();
$failed = (int)$pdo->query("select count(*) from results where status = 'FAIL'")->fetchColumn();
$recent = $pdo->query('select s.roll_number, s.name, r.percentage, r.grade, r.status from results r join students s on s.id = r.student_id order by r.updated_at desc limit 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Dashboard | Student Result Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-dashboard-page">
<div class="admin-layout">
    <aside class="sidebar admin-sidebar">
        <a class="brand" href="../index.php">Result <span>System</span></a>
        <nav aria-label="Admin navigation">
            <p class="admin-nav-label">Main</p>
            <a class="active" href="dashboard.php">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="students.php">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20M10 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM16 4.2a3.5 3.5 0 0 1 0 6.6M17 15h1a3 3 0 0 1 3 3v2"/></svg>
                <span>Students</span>
            </a>
            <a href="results.php">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M7 3h8l4 4v14H7zM15 3v5h5M10 12h6M10 16h6"/></svg>
                <span>Results</span>
            </a>
            <p class="admin-nav-label account-label">Account</p>
            <a href="logout.php" class="logout-link">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M10 5H5v14h5M14 8l4 4-4 4M9 12h9"/></svg>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <main class="admin-main admin-dashboard-main">
        <header class="admin-page-heading">
            <p class="eyebrow">Administration</p>
            <h1>Dashboard</h1>
            <p class="muted">Monitor students, results and academic performance from one place.</p>
        </header>

        <section class="admin-stats-grid" aria-label="System overview">
            <article class="admin-stat-card">
                <div class="admin-stat-top">
                    <span class="admin-stat-icon blue" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20M10 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM16 4.2a3.5 3.5 0 0 1 0 6.6M17 15h1a3 3 0 0 1 3 3v2"/></svg></span>
                    <span class="admin-stat-label">Students</span>
                </div>
                <strong><?= $students ?></strong>
                <small>Registered students</small>
            </article>

            <article class="admin-stat-card">
                <div class="admin-stat-top">
                    <span class="admin-stat-icon indigo" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h8l4 4v14H7zM15 3v5h5M10 12h6M10 16h6"/></svg></span>
                    <span class="admin-stat-label">Results</span>
                </div>
                <strong><?= $results ?></strong>
                <small>Published results</small>
            </article>

            <article class="admin-stat-card semantic-success">
                <div class="admin-stat-top">
                    <span class="admin-stat-icon green" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg></span>
                    <span class="admin-stat-label">Passed</span>
                </div>
                <strong><?= $passed ?></strong>
                <small>Successful results</small>
            </article>

            <article class="admin-stat-card semantic-danger">
                <div class="admin-stat-top">
                    <span class="admin-stat-icon red" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 8v5M12 16.5v.5M10.3 4h3.4L21 18.5a1 1 0 0 1-.9 1.5H3.9a1 1 0 0 1-.9-1.5L10.3 4Z"/></svg></span>
                    <span class="admin-stat-label">Failed</span>
                </div>
                <strong><?= $failed ?></strong>
                <small>Unsuccessful results</small>
            </article>
        </section>

        <section class="admin-quick-actions" aria-label="Quick actions">
            <div>
                <p class="admin-section-kicker">Quick actions</p>
                <h2>Manage records</h2>
            </div>
            <div class="admin-action-links">
                <a class="btn btn-secondary" href="students.php"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add Student</a>
                <a class="btn btn-primary" href="results.php"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Manage Results</a>
            </div>
        </section>

        <section class="panel admin-results-panel">
            <div class="panel-head admin-results-head">
                <div>
                    <p class="admin-section-kicker">Overview</p>
                    <h2>Recent Results</h2>
                    <p class="muted">Latest published or updated results.</p>
                </div>
                <a class="btn btn-secondary" href="results.php">View all <span aria-hidden="true">→</span></a>
            </div>

            <div class="table-wrap admin-results-table-wrap">
                <table>
                    <thead>
                        <tr><th>Roll No.</th><th>Name</th><th>Percentage</th><th>Grade</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recent as $row): ?>
                        <tr>
                            <td class="roll-cell"><?= e($row['roll_number']) ?></td>
                            <td class="name-cell"><?= e($row['name']) ?></td>
                            <td class="percentage-cell"><?= e((string)$row['percentage']) ?>%</td>
                            <td class="grade-cell"><?= e($row['grade']) ?></td>
                            <td><span class="status-badge <?= $row['status'] === 'PASS' ? 'pass' : 'fail' ?>"><?= e($row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?><tr><td colspan="5" class="empty-cell">No results yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($recent): ?>
            <div class="admin-mobile-results" aria-label="Recent results on mobile">
                <?php foreach ($recent as $row): ?>
                    <article class="admin-mobile-result-row">
                        <div class="admin-mobile-result-main">
                            <strong><?= e($row['name']) ?></strong>
                            <span><?= e($row['roll_number']) ?> · <?= e((string)$row['percentage']) ?>% · Grade <?= e($row['grade']) ?></span>
                        </div>
                        <span class="status-badge <?= $row['status'] === 'PASS' ? 'pass' : 'fail' ?>"><?= e($row['status']) ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
