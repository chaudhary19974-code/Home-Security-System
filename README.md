# House Visitor Check-in System

A QR-based guest and family sign-in for a four-floor house (ground floor = parking). Visitors scan a QR code, enter their name and phone number, take a photo, and the photo appears as a slideshow on the TVs on the other three floors.

Built with **core PHP + MySQL**. TVs can be **LG webOS** or **Android TV**.

## Features
- Static QR code at the parking entrance, printable from the admin page
- 3-step visitor flow: name, phone number, photo
- Returning visitors are recognised (cookie + phone number): name is prefilled and their previous photo is shown
- Guest / Family selection and a **group photo** option (group size, family name)
- Consent tick-box before a photo is shown on the TVs
- TV slideshow page for LG webOS and Android TV, plus an optional Android TV app
- TVs report in to the server, and the admin page shows each one as online or offline
- Owner admin: hide, show or delete photos; optional "approve first" mode
- Abuse limits (per phone number and per IP), image re-encoding, automatic cleanup of old photos

## Documentation
| File | What it covers |
|---|---|
| [SETUP.md](SETUP.md) | Requirements, installation, configuration, cron, troubleshooting |
| [ARCHITECTURE.md](ARCHITECTURE.md) | How it works, database tables, endpoints, security |
| [TV-SETUP.md](TV-SETUP.md) | Setting up LG webOS and Android TV screens |
| [ADMIN-GUIDE.md](ADMIN-GUIDE.md) | Day-to-day use by the house owner |

## Project structure
```
visitor-system/
├── index.php          Step 1: name
├── phone.php          Step 2: phone number
├── photo.php          Step 3: camera, guest/family, group option
├── save.php           Validates and stores the photo
├── done.php           Thank-you page
├── config.php         Settings (database, passwords, limits)
├── db.php             Database connection and helpers
├── database.sql       MySQL schema
├── api/slides.php     JSON feed for the TVs (also records TV check-ins)
├── tv/index.html      Slideshow page (webOS and Android TV)
├── admin/             Owner login, photo management, QR printer
├── cron/cleanup.php   Deletes old visits and photos
├── uploads/           Stored photos (PHP execution blocked)
├── assets/style.css   Styles
├── android-tv/        Optional Android TV wrapper app
└── demo.html          Standalone interactive demo
```

## Quick start
1. Import `database.sql`.
2. Edit `config.php` (database login, `ADMIN_PASS`, `TV_KEY`).
3. Serve over **HTTPS** and make `uploads/` writable.
4. Log in at `/admin/` and print the QR code.
5. Open the TV URL on each TV (see [TV-SETUP.md](TV-SETUP.md)).

Open `demo.html` in a browser to see the whole system working with sample data.
