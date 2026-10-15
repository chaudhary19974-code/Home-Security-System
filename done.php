<?php
require __DIR__ . '/db.php';
unset($_SESSION['name'], $_SESSION['phone']);
head(SITE_NAME);
?>
<h1>You're checked in</h1>
<p><?= AUTO_APPROVE ? 'Your photo will appear on the house screens shortly.' : 'Your photo will appear on the house screens once the owner approves it.' ?></p>
<a class="btn ghost" href="index.php">Check in another person</a>
<?php foot();
