<?php
require_once __DIR__ . '/config.php';
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
function db() {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    }
    return $pdo;
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_ok($t) { return !empty($_SESSION['csrf']) && is_string($t) && hash_equals($_SESSION['csrf'], $t); }
function current_visitor() {
    if (empty($_COOKIE['vtoken'])) return null;
    $s = db()->prepare('SELECT * FROM visitors WHERE token=?');
    $s->execute([$_COOKIE['vtoken']]);
    return $s->fetch() ?: null;
}
function last_photo($visitorId) {
    $s = db()->prepare('SELECT photo_path FROM visits WHERE visitor_id=? ORDER BY id DESC LIMIT 1');
    $s->execute([$visitorId]);
    $r = $s->fetch();
    return $r ? $r['photo_path'] : null;
}
function head($title, $base = '') {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>' . e($title) . '</title><link rel="stylesheet" href="' . $base . 'assets/style.css"></head><body><main class="card">';
}
function foot() { echo '</main></body></html>'; }
