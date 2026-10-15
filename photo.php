<?php
require __DIR__ . '/db.php';
if (empty($_SESSION['name']) || empty($_SESSION['phone'])) { header('Location: index.php'); exit; }
$s = db()->prepare('SELECT * FROM visitors WHERE phone=?');
$s->execute([$_SESSION['phone']]);
$v = $s->fetch();
$prev = $v ? last_photo($v['id']) : null;
head(SITE_NAME);
?>
<div class="steps"><i class="on"></i><i class="on"></i><i class="on"></i></div>
<h1><?= e($_SESSION['name']) ?>, take your photo</h1>
<?php if ($prev): ?>
  <div class="row" style="align-items:center"><img class="prev" src="<?= e($prev) ?>" alt="Your last visit photo"><span class="muted">Your photo from last time</span></div>
<?php endif; ?>

<div class="cam"><video id="v" autoplay playsinline muted></video><img id="shot" alt="Your new photo" hidden></div>
<canvas id="c" hidden></canvas>

<div class="row">
  <label><input type="radio" name="t" value="guest" checked> Guest</label>
  <label><input type="radio" name="t" value="family"> Family</label>
</div>
<label class="chk"><input type="checkbox" id="g"> This is a group photo</label>
<div id="gbox" hidden>
  <input type="number" id="gs" min="2" max="30" value="2" aria-label="Number of people">
  <input type="text" id="fn" maxlength="60" placeholder="Family name (optional)">
</div>
<label class="chk"><input type="checkbox" id="cons"> I agree my photo can be shown on the screens in this house.</label>
<p class="err" id="msg"></p>

<button class="btn" id="snap" type="button">Take photo</button>
<button class="btn ghost" id="again" type="button" hidden>Retake</button>
<button class="btn dark" id="send" type="button" hidden>Submit photo</button>
<p class="muted" style="margin-top:12px">Camera not working? <input type="file" id="f" accept="image/*" capture="user"></p>

<script>
var csrf = <?= json_encode(csrf()) ?>;
var $ = function (i) { return document.getElementById(i); };
var v = $('v'), c = $('c'), shot = $('shot'), msg = $('msg'), data = null;

function mode(taken) {
  v.hidden = taken; shot.hidden = !taken;
  $('snap').hidden = taken; $('again').hidden = !taken; $('send').hidden = !taken;
}
function draw(src, w, h) {
  var r = Math.min(1, 1024 / w);
  c.width = Math.round(w * r); c.height = Math.round(h * r);
  c.getContext('2d').drawImage(src, 0, 0, c.width, c.height);
  data = c.toDataURL('image/jpeg', 0.85);
  shot.src = data; mode(true);
}
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
  navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
    .then(function (s) { v.srcObject = s; })
    .catch(function () { msg.textContent = 'Camera is blocked. Allow camera access, or use the file option below.'; });
} else { msg.textContent = 'This browser cannot open the camera. Use the file option below.'; }

$('snap').onclick = function () {
  if (!v.videoWidth) { msg.textContent = 'Camera is not ready yet.'; return; }
  msg.textContent = ''; draw(v, v.videoWidth, v.videoHeight);
};
$('again').onclick = function () { data = null; mode(false); };
$('g').onchange = function () { $('gbox').hidden = !this.checked; };
$('f').onchange = function () {
  var file = this.files[0]; if (!file) return;
  var r = new FileReader();
  r.onload = function () { var im = new Image(); im.onload = function () { draw(im, im.width, im.height); }; im.src = r.result; };
  r.readAsDataURL(file);
};
$('send').onclick = function () {
  if (!data) return;
  if (!$('cons').checked) { msg.textContent = 'Please tick the agreement to continue.'; return; }
  var btn = this; btn.disabled = true; msg.textContent = 'Uploading...';
  fetch('save.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      csrf: csrf, image: data, consent: true,
      type: document.querySelector('input[name=t]:checked').value,
      is_group: $('g').checked, group_size: $('gs').value, family_name: $('fn').value
    })
  }).then(function (r) { return r.json(); }).then(function (j) {
    if (j.ok) { location.href = 'done.php'; } else { msg.textContent = j.error || 'Upload failed.'; btn.disabled = false; }
  }).catch(function () { msg.textContent = 'Network error. Try again.'; btn.disabled = false; });
};
</script>
<?php foot();
