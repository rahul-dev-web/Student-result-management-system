<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$result = null;
$error = '';
$roll = trim($_GET['roll_number'] ?? '');

if ($roll !== '') {
    $stmt = db()->prepare('select s.name, s.roll_number, s.course, s.semester, r.php, r.dbms, r.computer_network, r.operating_system, r.computer_graphics, r.total, r.percentage, r.grade, r.status from students s left join results r on r.student_id = s.id where s.roll_number = :roll limit 1');
    $stmt->execute(['roll' => $roll]);
    $result = $stmt->fetch();
    if (!$result) $error = 'No student found with this roll number.';
    elseif ($result['total'] === null) $error = 'Result has not been published for this student yet.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Search Result</title><link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="assets/css/result-page.css"></head>
<body class="result-search-page"><header class="site-header"><div class="container nav"><a class="brand" href="index.php">Student Result <span>Management</span></a><div class="nav-links"><a href="index.php">Home</a><a href="login.php">Student Login</a><a href="admin/login.php">Admin Login</a></div></div></header>
<main class="container page result-page">
<section class="result-intro"><div class="page-heading"><p class="eyebrow result-eyebrow"><span class="eyebrow-dot" aria-hidden="true"></span> Result Search</p><h1>Check Your Result</h1><p class="muted">Enter your roll number to view the published academic result.</p></div>
<form class="result-search-card" method="get" role="search"><label for="roll-number">Roll Number</label><div class="result-search-row"><input id="roll-number" name="roll_number" value="<?= e($roll) ?>" placeholder="e.g. 2024001" autocomplete="off" inputmode="numeric" required><button class="btn btn-primary result-search-button" type="submit">Search Result <span aria-hidden="true">→</span></button></div></form></section>
<?php if ($error): ?><section class="result-state <?= str_contains($error, 'not been published') ? 'info' : 'error' ?>" role="status"><div class="result-state-mark" aria-hidden="true"><?= str_contains($error, 'not been published') ? 'i' : '!' ?></div><div><h2><?= str_contains($error, 'not been published') ? 'Result not published yet' : 'No result found' ?></h2><p><?= e($error) ?> <?= !str_contains($error, 'not been published') ? 'Please check the roll number and try again.' : '' ?></p></div></section><?php endif; ?>
<?php if ($result && !$error): ?><section class="result-sheet result-sheet-premium" aria-labelledby="student-result-title"><div class="result-header result-header-premium"><div><p class="eyebrow">Student Result</p><h2 id="student-result-title"><?= e($result['name']) ?></h2><div class="result-meta"><span>Roll No. <?= e($result['roll_number']) ?></span><span aria-hidden="true">•</span><span><?= e($result['course']) ?></span><span aria-hidden="true">•</span><span>Semester <?= e((string)$result['semester']) ?></span></div></div><span class="status-badge result-status <?= $result['status'] === 'PASS' ? 'pass' : 'fail' ?>"><span aria-hidden="true">●</span><?= e($result['status']) ?></span></div>
<div class="result-table-wrap"><table class="result-table"><thead><tr><th scope="col">Subject</th><th scope="col">Marks</th></tr></thead><tbody><tr><td>PHP</td><td><?= e((string)$result['php']) ?> / 100</td></tr><tr><td>DBMS</td><td><?= e((string)$result['dbms']) ?> / 100</td></tr><tr><td>Computer Network</td><td><?= e((string)$result['computer_network']) ?> / 100</td></tr><tr><td>Operating System</td><td><?= e((string)$result['operating_system']) ?> / 100</td></tr><tr><td>Computer Graphics</td><td><?= e((string)$result['computer_graphics']) ?> / 100</td></tr></tbody></table></div>
<div class="summary-grid result-summary"><div><small>Total</small><strong><?= e((string)$result['total']) ?> / 500</strong></div><div class="summary-primary"><small>Percentage</small><strong><?= e((string)$result['percentage']) ?>%</strong></div><div><small>Grade</small><strong><?= e($result['grade']) ?></strong></div></div></section><?php endif; ?>
</main></body></html>
