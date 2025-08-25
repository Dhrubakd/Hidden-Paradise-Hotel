# Hidden Paradise Hotel

Simple PHP/MySQL hotel management demo intended for local use (XAMPP).

## Features
- Check-in (create reservations, select services)
- Check-out (payments, mark rooms available)
- Room management (add/edit/delete with FK safety checks)
- Simple role-based pages (manager vs staff) via demo credentials
- Tailwind CSS used via CDN for styling

## Quick start (Windows + XAMPP)
1. Install XAMPP and start Apache + MySQL.
2. Put this project in `C:\xampp\htdocs\hotel-system` (already placed).
3. Create the database and schema:


4. Open in browser: http://localhost/hotel-system

## Demo credentials
- Manager: `admin` / `password`
- Staff: `staff` / `staffpass`

(These are hardcoded for the demo — do NOT use in production.)

## Reset / Clear data
Backup first (recommended):



Truncate all tables safely (child tables first):

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -e "USE hotel_db;
TRUNCATE TABLE reservation_services;
# Hidden Paradise Hotel

Simple PHP + MySQL hotel management demo intended for local development (XAMPP).

## Features
- Check-in (create reservations, select services)
- Check-out (payments, mark rooms available)
- Room management (add/edit/delete with foreign-key safety checks)
- Simple role-based pages (manager vs staff) via demo credentials
- Tailwind CSS used via CDN for styling

## Quick start (Windows + XAMPP)
1. Install XAMPP and start Apache + MySQL.
2. Place the project at `C:\xampp\htdocs\hotel-system`.
3. Create the database and schema (run from PowerShell):

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root < 'C:\xampp\htdocs\hotel-system\setup.sql'
```

4. Open in your browser: http://localhost/hotel-system

## Demo credentials
- Manager: `admin` / `password`
- Staff: `staff` / `staffpass`

These credentials are hardcoded for the demo — do not use them in production.

## Reset / Clear data
Make a backup first (recommended):

```powershell
& 'C:\xampp\mysql\bin\mysqldump.exe' -u root hotel_db > 'C:\temp\hotel_db_backup.sql'
```

Truncate all tables safely (truncate child tables first to respect foreign keys):

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -e "USE hotel_db;
TRUNCATE TABLE reservation_services;
TRUNCATE TABLE payments;
TRUNCATE TABLE reservations;
TRUNCATE TABLE guests;
TRUNCATE TABLE services;
TRUNCATE TABLE rooms;"
```

If you prefer a single-shot approach, you can temporarily disable foreign key checks (use with caution):

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -u root -e "SET FOREIGN_KEY_CHECKS=0; USE hotel_db;
TRUNCATE TABLE reservation_services;
TRUNCATE TABLE payments;
TRUNCATE TABLE reservations;
TRUNCATE TABLE guests;
TRUNCATE TABLE services;
TRUNCATE TABLE rooms;
SET FOREIGN_KEY_CHECKS=1;"
```

## Notes and recommendations
- This is a demo. For production consider:
  - Using a `users` table with hashed passwords (password_hash / password_verify).
  - Adding CSRF protection for forms.
  - Enforcing role-based authorization backed by the database.
  - Securing database credentials and removing hardcoded secrets.

## Troubleshooting
- If labels or inputs appear invisible, hard-refresh (Ctrl+F5) and check the DevTools Network tab to confirm the Tailwind CDN loads.
- If you get foreign-key errors when truncating, truncate dependent (child) tables first or use the FK-disable option above.

## Development notes
Files of interest:
- `db.php` – PDO connection and session helpers
- `checkin.php`, `checkout.php` – reservation and payment flows
- `available_rooms.php` – AJAX endpoint for room availability
- `edit_room.php` – manager edit/delete UI

## License
This demo is provided as-is for learning and development.






