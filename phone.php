<?php
require __DIR__ . '/db.php';
if (empty($_SESSION['name'])) { header('Location: index.php'); exit; }
$v = current_visitor();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = preg_replace('/[^\d+]/', '', $_POST['phone'] ?? '');
    if (!csrf_ok($_POST['csrf'] ?? '')) $error = 'Session expired. Please try again.';
    elseif (!preg_match('/^\+?\d{7,15}$/', $phone)) $error = 'Enter a valid phone number (7 to 15 digits).';
    else { $_SESSION['phone'] = $phone; header('Location: photo.php'); exit; }
}
$phone = $_SESSION['phone'] ?? ($v['phone'] ?? '');
head(SITE_NAME);
?>
<div class="steps"><i class="on"></i><i class="on"></i><i></i></div>
<h1>Hi <?= e($_SESSION['name']) ?>, your phone number?</h1>
<p class="muted">We use it to recognise you next time.</p>
<form method="post">
  <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
  <label class="f" for="phone">Phone number</label>
  <input type="tel" id="phone" name="phone" value="<?= e($phone) ?>" inputmode="tel" autocomplete="tel" maxlength="20" required autofocus>
  <p class="err"><?= e($error ?? '') ?></p>
  <button class="btn" type="submit">Next</button>
  <a class="btn ghost" href="index.php">Back</a>
</form>
<?php foot();
