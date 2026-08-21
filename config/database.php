<?php
/**
 * Supabase PostgreSQL connection through PDO.
 *
 * Required environment variables:
 * DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '');
    $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '5432');
    $name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'postgres');
    $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? '');
    $password = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? '');

    // Never log the password. These diagnostics are intentionally limited to
    // deployment-safe information so the real failure is visible in Vercel logs.
    error_log(sprintf(
        'SRMS DB diagnostics: host=%s port=%s db=%s user=%s password_set=%s pdo=%s pdo_pgsql=%s',
        $host !== '' ? $host : '(missing)',
        $port,
        $name,
        $user !== '' ? $user : '(missing)',
        $password !== '' ? 'yes' : 'no',
        extension_loaded('pdo') ? 'yes' : 'no',
        extension_loaded('pdo_pgsql') ? 'yes' : 'no'
    ));

    if (!$host || !$user || !$password) {
        error_log('SRMS DB error: one or more required database environment variables are missing.');
        http_response_code(500);
        exit('Database configuration is missing. Check the Vercel Production environment variables.');
    }

    if (!extension_loaded('pdo_pgsql')) {
        error_log('SRMS DB error: PDO PostgreSQL driver (pdo_pgsql) is not loaded in the PHP runtime.');
        http_response_code(500);
        exit('Database driver is unavailable in the PHP runtime.');
    }

    $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 10,
        ]);
        error_log('SRMS DB connection: SUCCESS');
        return $pdo;
    } catch (PDOException $e) {
        // Log the real PDO message server-side, but never expose it to visitors.
        error_log('SRMS DB connection FAILED: ' . $e->getMessage());
        http_response_code(500);
        exit('Database connection failed. Check the Supabase PostgreSQL settings.');
    }
}
