# Setup Guide

## Requirements
| Item | Notes |
|---|---|
| PHP 7.4 or newer | Extensions: `pdo_mysql`, `gd`, `mbstring` |
| MySQL 5.7+ or MariaDB 10.3+ | |
| Web server | Apache (uses `uploads/.htaccess`) or Nginx (see below) |
| **HTTPS** | Required. Browsers block camera access on plain HTTP |
| Wi-Fi | The TVs and visitors' phones must reach the server |

For testing on one computer, XAMPP works, but the camera only works on `https://` or `http://localhost`. To test from a phone you need a real HTTPS address.

## Installation
1. **Copy** the folder to your web root, e.g. `/var/www/html/visit`.
2. **Create the database**
   ```bash
   mysql -u root -p < database.sql
   ```
   (or import `database.sql` in phpMyAdmin).
3. **Edit `config.php`** (see the table below).
4. **Make uploads writable**
   ```bash
   chown -R www-data:www-data uploads
   chmod 755 uploads
   ```
5. **Log in** at `https://your-domain/visit/admin/`, open **Print QR code**, print and place it at the parking entrance.
6. **Set up the TVs** following [TV-SETUP.md](TV-SETUP.md).
7. **Schedule cleanup** (optional but recommended):
   ```
   0 3 * * * php /var/www/html/visit/cron/cleanup.php
   ```

## Configuration (`config.php`)
| Setting | Default | Meaning |
|---|---|---|
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | localhost / visitor_system / root / empty | Database connection. Use a dedicated MySQL user in production |
| `SITE_NAME` | Welcome Home | Title text |
| `ADMIN_USER`, `ADMIN_PASS` | admin / ChangeMe@123 | Owner login. **Change the password** |
| `TV_KEY` | tv-secret-change-me | Secret the TVs send to read the photo feed. **Change it** |
| `AUTO_APPROVE` | 1 | `1` = photos show on TVs immediately. `0` = owner must approve each photo |
| `SLIDE_HOURS` | 72 | TVs show photos from the last N hours |
| `KEEP_DAYS` | 7 | `cron/cleanup.php` deletes visits older than N days |
| `MAX_PER_HOUR_PHONE` | 5 | Uploads allowed per phone number per hour |
| `MAX_PER_HOUR_IP` | 20 | Uploads allowed per IP address per hour |
| timezone | Asia/Kolkata | Change the `date_default_timezone_set` line if needed |

## Nginx
Nginx ignores `.htaccess`, so block PHP in the uploads folder yourself:
```nginx
location ~* ^/visit/uploads/.*\.(php|phtml|phar)$ { deny all; }
```

## Upgrading an existing install
If you imported an earlier `database.sql`, add the TV status table:
```sql
CREATE TABLE IF NOT EXISTS tvs (
  name VARCHAR(40) PRIMARY KEY,
  platform VARCHAR(20) NULL,
  last_seen DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

## Before going live checklist
- [ ] `ADMIN_PASS` and `TV_KEY` changed
- [ ] Dedicated MySQL user (not `root`)
- [ ] HTTPS working, camera opens on a phone
- [ ] `uploads/` writable, and `https://your-domain/visit/uploads/anything.php` is not executable
- [ ] Test visit from a phone, photo appears on a TV
- [ ] Cron cleanup scheduled
- [ ] Consent wording agreed with the owner

## Troubleshooting
| Problem | Likely cause and fix |
|---|---|
| Camera does not open | Page is not on HTTPS, or camera permission was denied. Use the file-picker fallback or allow camera access |
| "Server cannot save photos" | `uploads/` is not writable by the web server |
| Blank database error | Check `config.php` credentials; make sure `database.sql` was imported |
| TV shows "Waiting for the first visitor" | No approved photos in the last `SLIDE_HOURS`, or the `key` in the TV URL does not match `TV_KEY` |
| TV is "offline" in admin | TV page is not open, Wi-Fi is down, or the `tvs` table is missing |
| Photo not appearing | `AUTO_APPROVE` is 0 and the photo is awaiting approval, or it was hidden in admin |
| "Too many uploads" | Rate limit reached (see `MAX_PER_HOUR_*`) |
| Large photos fail | Raise `upload_max_filesize` and `post_max_size` in `php.ini` (photos are resized in the browser first, so 8M is plenty) |
