<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'undericeberg';
    $user = getenv('DB_USER') ?: 'ice';
    $pass = getenv('DB_PASS') ?: 'ice';

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Controlla se la migrazione con 'post' e 'utenti' è già stata eseguita
    static $checked = false;
    if (!$checked) {
        $checked = true;
        try {
            $pdo->query("SELECT 1 FROM post LIMIT 1");
        } catch (Throwable $e) {
            require_once __DIR__ . '/db_setup.php';
            init_database();
        }
    }

    return $pdo;
}

function h(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function tempo_fa(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return "proprio ora";
    if ($diff < 3600) return floor($diff / 60) . " min fa";
    if ($diff < 86400) return floor($diff / 3600) . " h fa";
    if ($diff < 604800) return floor($diff / 86400) . " gg fa";
    return date('d/m/Y', $time);
}

function format_plays(int $n): string {
    if ($n === 113) return "113 (GOAT STATUS)";
    if ($n >= 1000000) return round($n / 1000000, 1) . 'M';
    if ($n >= 1000) return round($n / 1000, 1) . 'k';
    return (string)$n;
}
