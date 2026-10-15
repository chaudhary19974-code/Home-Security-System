<?php
// ---- Edit these before going live ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'visitor_system');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Welcome Home');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'ChangeMe@123');   // change this
define('TV_KEY', 'tv-secret-change-me'); // TVs open /tv/index.html?key=THIS

define('AUTO_APPROVE', 1);        // 1 = photo shows on TVs immediately, 0 = owner approves first
define('SLIDE_HOURS', 72);        // TVs show photos from the last N hours
define('KEEP_DAYS', 7);           // cron/cleanup.php deletes visits older than N days
define('MAX_PER_HOUR_PHONE', 5);  // abuse limits
define('MAX_PER_HOUR_IP', 20);

date_default_timezone_set('Asia/Kolkata');
