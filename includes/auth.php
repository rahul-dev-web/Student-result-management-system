<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_student(): void
{
    if (empty($_SESSION['student'])) {
        header('Location: /login.php');
        exit;
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function calculate_result(array $marks): array
{
    $total = array_sum($marks);
    $percentage = $total / 5;

    if ($percentage >= 90) $grade = 'A+';
    elseif ($percentage >= 80) $grade = 'A';
    elseif ($percentage >= 70) $grade = 'B+';
    elseif ($percentage >= 60) $grade = 'B';
    elseif ($percentage >= 50) $grade = 'C';
    elseif ($percentage >= 40) $grade = 'D';
    else $grade = 'F';

    $status = min($marks) >= 40 ? 'PASS' : 'FAIL';

    return [
        'total' => $total,
        'percentage' => round($percentage, 2),
        'grade' => $grade,
        'status' => $status,
    ];
}
