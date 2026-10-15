<?php
require __DIR__ . '/db.php';
$v = current_visitor();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(mb_substr(preg_replace('/\s+/', ' ', $_POST['name'] ?? ''), 0, 80));
    if (csrf_ok($_POST['csrf'] ?? '') && $name !== '') {
        $_SESSION['name'] = $name;
        header('Location: phone.php'); exit;
    }
    $error = 'Please enter your name.';
}
$name = $_SESSION['name'] ?? ($v['name'] ?? '');
head(SITE_NAME);
?>
<div class="steps"><i class="on"></i><i></i><i></i></div>
<h1><?= $v ? 'Good to see you again' : 'Welcome' ?></h1>
<?php if ($v): ?><div class="hello">We found your earlier visit. Check your name and continue.</div><?php endif; ?>
<form method="post" autocomplete="name">
  <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
  <label class="f" for="name">Your name</label>
  <input type="text" id="name" name="name" value="<?= e($name) ?>" maxlength="80" required autofocus>
  <p class="err"><?= e($error ?? '') ?></p>
  <button class="btn" type="submit">Next</button>
</form>
<?php foot();
