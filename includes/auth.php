<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Vercel runs PHP requests as separate serverless invocations, so filesystem
 * backed PHP sessions are not reliable between requests. Keep a small,
 * signed identity cookie and rebuild the session from the database.
 */
function auth_secret(): string
{
    $secret = getenv('APP_SECRET');
    if ($secret !== false && $secret !== '') {
        return $secret;
    }

    // College-project fallback: the DB password is already required to access
    // the application. APP_SECRET can be added later without code changes.
    $dbSecret = getenv('DB_PASSWORD');
    if ($dbSecret !== false && $dbSecret !== '') {
        return hash('sha256', $dbSecret);
    }

    return 'student-result-management-system-local-secret';
}

function auth_cookie_name(string $role): string
{
    return $role === 'admin' ? 'srms_admin' : 'srms_student';
}

function auth_cookie_options(): array
{
    return [
        'expires' => time() + 28800,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function make_auth_token(string $role, int $id): string
{
    $payload = base64_encode(json_encode([
        'role' => $role,
        'id' => $id,
        'exp' => time() + 28800,
    ], JSON_UNESCAPED_SLASHES));
    $signature = hash_hmac('sha256', $payload, auth_secret());
    return $payload . '.' . $signature;
}

function read_auth_token(string $role): ?int
{
    $token = $_COOKIE[auth_cookie_name($role)] ?? '';
    if ($token === '' || !str_contains($token, '.')) {
        return null;
    }

    [$payload, $signature] = explode('.', $token, 2);
    $expected = hash_hmac('sha256', $payload, auth_secret());
    if (!hash_equals($expected, $signature)) {
        return null;
    }

    $data = json_decode(base64_decode($payload, true) ?: '', true);
    if (!is_array($data) || ($data['role'] ?? '') !== $role || empty($data['id']) || (int)($data['exp'] ?? 0) < time()) {
        return null;
    }

    return (int)$data['id'];
}

function set_auth_cookie(string $role, int $id): void
{
    setcookie(auth_cookie_name($role), make_auth_token($role, $id), auth_cookie_options());
}

function clear_auth_cookie(string $role): void
{
    $options = auth_cookie_options();
    $options['expires'] = time() - 3600;
    setcookie(auth_cookie_name($role), '', $options);
}

function require_student(): void
{
    $id = read_auth_token('student');
    if ($id === null) {
        unset($_SESSION['student']);
        header('Location: /login.php');
        exit;
    }

    $stmt = db()->prepare('select id, name, roll_number, email, course, semester from students where id = :id limit 1');
    $stmt->execute(['id' => $id]);
    $student = $stmt->fetch();

    if (!$student) {
        clear_auth_cookie('student');
        unset($_SESSION['student']);
        header('Location: /login.php');
        exit;
    }

    $_SESSION['student'] = $student;
}

function require_admin(): void
{
    $id = read_auth_token('admin');
    if ($id === null) {
        unset($_SESSION['admin']);
        header('Location: /admin/login.php');
        exit;
    }

    $stmt = db()->prepare('select id, username from admins where id = :id limit 1');
    $stmt->execute(['id' => $id]);
    $admin = $stmt->fetch();

    if (!$admin) {
        clear_auth_cookie('admin');
        unset($_SESSION['admin']);
        header('Location: /admin/login.php');
        exit;
    }

    $_SESSION['admin'] = $admin;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function calculate_result(array $marks): array
{
    if (count($marks) !== 5) {
        throw new InvalidArgumentException('Exactly five subject marks are required.');
    }

    $marks = array_map('floatval', $marks);
    foreach ($marks as $mark) {
        if ($mark < 0 || $mark > 100) {
            throw new InvalidArgumentException('Each mark must be between 0 and 100.');
        }
    }

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
        'total' => round($total, 2),
        'percentage' => round($percentage, 2),
        'grade' => $grade,
        'status' => $status,
    ];
}
