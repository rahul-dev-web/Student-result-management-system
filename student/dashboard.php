<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_student();
$student = $_SESSION['student'];
$stmt = db()->prepare('select php, dbms, computer_network, operating_system, computer_graphics, total, percentage, grade, status from results where student_id = :id limit 1');
$stmt->execute(['id' => $student['id']]);
$result = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="student-dashboard">
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="../index.php">Student Result <span>Management</span></a>
      <div class="nav-links">
        <a href="../result.php?roll_number=<?= urlencode($student['roll_number']) ?>">My Result</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="container page student-dashboard-main">
    <section class="page-heading student-welcome">
      <p class="eyebrow"><span class="eyebrow-dot"></span> Student Portal</p>
      <h1>Welcome back, <?= e($student['name']) ?></h1>
      <p class="muted student-meta">
        <span><?= e($student['course']) ?></span>
        <span class="meta-separator">•</span>
        <span>Semester <?= e((string)$student['semester']) ?></span>
        <span class="meta-separator">•</span>
        <span>Roll No. <?= e($student['roll_number']) ?></span>
      </p>
    </section>

    <?php if ($result): ?>
      <section class="stats-grid student-stats" aria-label="Result summary">
        <div class="stat-card student-stat-primary">
          <small>Percentage</small>
          <strong><?= e((string)$result['percentage']) ?>%</strong>
        </div>
        <div class="stat-card">
          <small>Grade</small>
          <strong><?= e($result['grade']) ?></strong>
        </div>
        <div class="stat-card student-status-card">
          <small>Result Status</small>
          <strong class="<?= strtoupper((string)$result['status']) === 'PASS' ? 'status-pass' : 'status-fail' ?>"><?= e($result['status']) ?></strong>
        </div>
      </section>

      <section class="panel student-result-panel">
        <div class="panel-head">
          <div>
            <h2>Your Result</h2>
            <p class="muted">Your latest academic performance.</p>
          </div>
          <a class="btn btn-primary student-result-cta" href="../result.php?roll_number=<?= urlencode($student['roll_number']) ?>">View Full Result <span aria-hidden="true">→</span></a>
        </div>

        <div class="mini-marks student-mini-marks">
          <span><label>PHP</label><b><?= e((string)$result['php']) ?> <small>/ 100</small></b></span>
          <span><label>DBMS</label><b><?= e((string)$result['dbms']) ?> <small>/ 100</small></b></span>
          <span><label><span class="subject-desktop">Computer Network</span><span class="subject-mobile">CN</span></label><b><?= e((string)$result['computer_network']) ?> <small>/ 100</small></b></span>
          <span><label><span class="subject-desktop">Operating System</span><span class="subject-mobile">OS</span></label><b><?= e((string)$result['operating_system']) ?> <small>/ 100</small></b></span>
          <span><label><span class="subject-desktop">Computer Graphics</span><span class="subject-mobile">CG</span></label><b><?= e((string)$result['computer_graphics']) ?> <small>/ 100</small></b></span>
        </div>
      </section>
    <?php else: ?>
      <section class="empty-state student-empty-state">
        <p class="eyebrow">Academic Record</p>
        <h2>Result not available yet</h2>
        <p>Your academic result hasn't been published. Please check again later.</p>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
