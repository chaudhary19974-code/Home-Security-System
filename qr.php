<?php
require __DIR__ . '/../db.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$url = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
head('Entrance QR code', '../'); ?>
<h1 style="text-align:center"><?= e(SITE_NAME) ?></h1>
<p style="text-align:center">Scan to sign in as a guest or family</p>
<div id="qr" style="display:flex;justify-content:center;margin:16px 0"></div>
<p class="muted" style="text-align:center;word-break:break-all"><?= e($url) ?></p>
<button class="btn dark" onclick="window.print()">Print</button>
<a class="btn ghost" href="index.php">Back</a>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>new QRCode(document.getElementById('qr'), {text: <?= json_encode($url) ?>, width: 260, height: 260});</script>
<?php foot();
