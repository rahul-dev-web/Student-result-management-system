<?php
/**
 * Supabase PostgreSQL connection through PDO.
 *
 * Local XAMPP setup: define these environment variables in Apache/PHP or
 * replace the values locally without committing secrets to GitHub.
 * DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: '5432';
    $name = getenv('DB_NAME') ?: 'postgres';
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');

    if (!$host || !$user || !$password) {
        http_response_code(500);
        exit('Database configuration is missing. Set DB_HOST, DB_PORT, DB_NAME, DB_USER and DB_PASSWORD.');
    }

    $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        exit('Database connection failed. Check the Supabase PostgreSQL settings.');
    }
}
