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
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Search Result</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body><header class="site-header"><div class="container nav"><a class="brand" href="index.php">Student Result <span>Management</span></a><div class="nav-links"><a href="index.php">Home</a><a href="login.php">Student Login</a><a href="admin/login.php">Admin Login</a></div></div></header>
<main class="container page"><div class="page-heading"><p class="eyebrow">Result Search</p><h1>Check Your Result</h1><p class="muted">Enter a student's roll number to view the published result.</p></div>
<form class="search-bar" method="get"><input name="roll_number" value="<?= e($roll) ?>" placeholder="Enter roll number" required><button class="btn btn-primary" type="submit">Search Result</button></form>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($result && !$error): ?><section class="result-sheet"><div class="result-header"><div><p class="eyebrow">Student Result</p><h2><?= e($result['name']) ?></h2><p><?= e($result['roll_number']) ?> • <?= e($result['course']) ?> • Semester <?= e((string)$result['semester']) ?></p></div><span class="status-badge <?= $result['status'] === 'PASS' ? 'pass' : 'fail' ?>"><?= e($result['status']) ?></span></div><table><thead><tr><th>Subject</th><th>Marks</th></tr></thead><tbody><tr><td>PHP</td><td><?= e((string)$result['php']) ?></td></tr><tr><td>DBMS</td><td><?= e((string)$result['dbms']) ?></td></tr><tr><td>Computer Network</td><td><?= e((string)$result['computer_network']) ?></td></tr><tr><td>Operating System</td><td><?= e((string)$result['operating_system']) ?></td></tr><tr><td>Computer Graphics</td><td><?= e((string)$result['computer_graphics']) ?></td></tr></tbody></table><div class="summary-grid"><div><small>Total</small><strong><?= e((string)$result['total']) ?> / 500</strong></div><div><small>Percentage</small><strong><?= e((string)$result['percentage']) ?>%</strong></div><div><small>Grade</small><strong><?= e($result['grade']) ?></strong></div></div></section><?php endif; ?></main></body></html>
