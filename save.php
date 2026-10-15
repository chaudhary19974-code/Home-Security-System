<?php
require __DIR__ . '/db.php';
header('Content-Type: application/json');
function fail($m, $code = 400) { http_response_code($code); echo json_encode(['ok' => false, 'error' => $m]); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('Invalid request.', 405);
$d = json_decode(file_get_contents('php://input'), true);
if (!is_array($d) || !csrf_ok($d['csrf'] ?? '')) fail('Session expired. Reload the page.', 403);
$name = $_SESSION['name'] ?? ''; $phone = $_SESSION['phone'] ?? '';
if ($name === '' || $phone === '') fail('Please start again from the first page.');
if (empty($d['consent'])) fail('Please agree to the photo notice.');

// --- image: validate, resize, re-encode as clean JPEG ---
$img = (string)($d['image'] ?? '');
if (!preg_match('#^data:image/(jpeg|png);base64,#', $img)) fail('Invalid image.');
$bin = base64_decode(substr($img, strpos($img, ',') + 1), true);
if ($bin === false || strlen($bin) > 6 * 1024 * 1024) fail('Image is too large.');
$src = @imagecreatefromstring($bin);
if (!$src) fail('Could not read the image.');
$w = imagesx($src); $h = imagesy($src);
if ($w > 1280) {
    $nh = (int)round($h * 1280 / $w);
    $dst = imagecreatetruecolor(1280, $nh);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, 1280, $nh, $w, $h);
    $src = $dst;
}

// --- abuse limits ---
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$q = db()->prepare('SELECT COUNT(*) FROM visits WHERE ip=? AND created_at>=DATE_SUB(NOW(),INTERVAL 1 HOUR)');
$q->execute([$ip]);
if ($q->fetchColumn() >= MAX_PER_HOUR_IP) fail('Too many uploads. Please try later.', 429);

// --- visitor: find by phone or create ---
$s = db()->prepare('SELECT * FROM visitors WHERE phone=?');
$s->execute([$phone]);
$vis = $s->fetch();
if ($vis) {
    $q = db()->prepare('SELECT COUNT(*) FROM visits WHERE visitor_id=? AND created_at>=DATE_SUB(NOW(),INTERVAL 1 HOUR)');
    $q->execute([$vis['id']]);
    if ($q->fetchColumn() >= MAX_PER_HOUR_PHONE) fail('Too many visits for this number. Please try later.', 429);
    db()->prepare('UPDATE visitors SET name=?, last_seen=NOW() WHERE id=?')->execute([$name, $vis['id']]);
    $vid = (int)$vis['id']; $token = $vis['token'];
} else {
    $token = bin2hex(random_bytes(16));
    db()->prepare('INSERT INTO visitors(name,phone,token) VALUES(?,?,?)')->execute([$name, $phone, $token]);
    $vid = (int)db()->lastInsertId();
}

// --- save file ---
$dir = 'uploads/' . date('Ym');
if (!is_dir(__DIR__ . '/' . $dir) && !mkdir(__DIR__ . '/' . $dir, 0755, true)) fail('Server cannot save photos.', 500);
$rel = $dir . '/' . bin2hex(random_bytes(12)) . '.jpg';
if (!imagejpeg($src, __DIR__ . '/' . $rel, 85)) fail('Could not save the photo.', 500);

$type = ($d['type'] ?? '') === 'family' ? 'family' : 'guest';
$isGroup = !empty($d['is_group']) ? 1 : 0;
$size = $isGroup ? max(2, min(30, (int)($d['group_size'] ?? 2))) : 1;
$fam = $isGroup ? trim(mb_substr((string)($d['family_name'] ?? ''), 0, 60)) : '';
db()->prepare('INSERT INTO visits(visitor_id,visit_type,is_group,group_size,family_name,photo_path,approved,ip) VALUES(?,?,?,?,?,?,?,?)')
    ->execute([$vid, $type, $isGroup, $size, $fam ?: null, $rel, AUTO_APPROVE ? 1 : 0, $ip]);

setcookie('vtoken', $token, ['expires' => time() + 86400 * 365, 'path' => '/', 'httponly' => true,
    'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
echo json_encode(['ok' => true]);
