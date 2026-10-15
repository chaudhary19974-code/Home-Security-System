# Owner Guide

For the person who runs the house screens day to day.

## Logging in
Go to `https://your-domain/visit/admin/` and sign in with the admin username and password from `config.php`. Log out with the **Log out** button.

## The visits list
The page lists the latest 100 visits with a thumbnail, name, phone number, type (Guest, Family or group), time and whether the photo is **Showing** or **Hidden** on the TVs.

| Button | What it does |
|---|---|
| **Hide** | Removes the photo from all TVs but keeps it in the list. **Show** brings it back |
| **Delete** | Deletes the photo file and the visit permanently |

Click a thumbnail to open the full photo.

## Approving photos first
In `config.php`, set `AUTO_APPROVE` to `0`. New photos then start as **Hidden**; press **Show** to put them on the TVs. With `1` (default) photos appear immediately and you hide anything unwanted afterwards.

## TV status
At the top of the page each TV is listed as **online** or **offline**.
- **online**: the TV contacted the server in the last 90 seconds.
- **offline**: the TV is off, the page was closed, or Wi-Fi is down. See TV-SETUP.md.

## Printing the QR code
Click **Print QR code**, then **Print**. Place it at the parking entrance, at a height visitors can scan easily. The QR code never changes, so one printout is enough.

## What visitors see
1. Scan the QR code.
2. Enter their name (prefilled if they have visited before).
3. Enter their phone number.
4. Take a photo (their previous photo is shown if they have one). They can pick Guest or Family, tick "group photo" with the number of people and family name, and must tick the consent box.
5. A thank-you page. The photo appears on the TVs within about 30 seconds.

## Routine tasks
| When | Task |
|---|---|
| Daily | Glance at the TV status and new photos |
| Weekly | Delete anything inappropriate; check cleanup is running |
| Monthly | Change passwords if staff changed; check disk space in `uploads/` |
| On request | Delete a guest's photos from the admin page if they ask |

## Privacy
Photos and phone numbers are personal data. They are deleted automatically after `KEEP_DAYS` (default 7 days) when the cleanup job is scheduled. TVs show first names only. Keep the consent text on the photo page, and do not share the admin password.

## Frequently asked questions
**A guest says their photo is not on the TV.** It can take up to 30 seconds. Check it is not marked Hidden, and that the TV is online.

**A guest lost their phone or cleared their browser.** No problem. Entering the same phone number recognises them and shows their previous photo.

**Can I show only family photos?** Not yet. This can be added as a setting on request.

**I forgot the admin password.** Open `config.php` on the server and read or change `ADMIN_PASS`.
