# TV Setup (LG webOS and Android TV)

All TVs show the same page:

```
https://your-domain/visit/tv/index.html?key=YOUR_TV_KEY&tv=Floor2
```
- `key` must equal `TV_KEY` in `config.php`.
- `tv` is a name for this TV (use a different one on each TV, e.g. `Floor1`, `Floor2`, `Floor3`). It is shown in the admin page.

The page checks for new photos every 30 seconds, shows a newly arrived visitor next, crossfades between photos (group photos stay longer) and reloads itself every 6 hours.

## Option A: LG webOS (browser)
1. Connect the TV to the same network/Wi-Fi with internet access to your server.
2. Open the **Web Browser** app.
3. Enter the TV URL above and bookmark it.
4. Press the full-screen option in the browser menu if available.
5. Check the admin page: the TV should show as **online (webOS)**.

Tips: turn off the TV's screen saver and auto power-off if you want it always on; a TV that restarts must have the browser reopened (webOS browsers do not reopen pages automatically).

## Option B: Android TV (browser)
1. Install a browser from the Play Store (for example, a TV-friendly browser).
2. Open the TV URL and bookmark it, or set it as the home page.
3. The admin page should show the TV as **online (AndroidTV)**.

## Option C: Android TV app (recommended for Android TV)
A small app in `android-tv/` opens the page full screen, keeps the screen awake and retries every 10 seconds if the network or server is down.

1. Open the `android-tv` folder in **Android Studio** (it creates the Gradle wrapper).
2. Edit `app/src/main/res/values/strings.xml`:
   ```xml
   <string name="tv_url">https://your-domain/visit/tv/index.html?key=YOUR_TV_KEY</string>
   <string name="tv_name">Floor 2 TV</string>
   ```
   Change `tv_name` for each TV (the app adds `&tv=` automatically).
3. **Build > Build APK(s)**.
4. On the TV: **Settings > Device Preferences > About**, tap *Build* seven times to enable developer options, then enable **network debugging**.
5. Install from your computer:
   ```bash
   adb connect TV_IP_ADDRESS
   adb install app-debug.apk
   ```
   (or copy the APK to a USB stick and open it with a file manager).
6. Open **House Screen** from the app row.

`android:usesCleartextTraffic` is enabled so you can test over plain `http://` on your home network. Remove it from `AndroidManifest.xml` once the site is on HTTPS.

## Checking the connection
Open **Admin**. Each TV is listed as **online** if it contacted the server in the last 90 seconds, otherwise **offline**.

## Troubleshooting
| Symptom | Fix |
|---|---|
| "Waiting for the first visitor" | No approved photo in the last 72 hours (see `SLIDE_HOURS`), or wrong `key` |
| Black screen, nothing | URL typed wrong, TV has no internet, or the SSL certificate is invalid/expired |
| Shows offline in admin | Page closed, TV asleep, or `tvs` table missing (see SETUP.md) |
| Photos slow to appear | Normal delay is up to 30 seconds |
| Page looks broken on an old TV | The page uses plain JavaScript on purpose; update the TV firmware or test in the TV's latest browser |
