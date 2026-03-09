# Voyara

Simple PHP + MySQL starter for:

- passenger seat booking without login
- seat availability by travel date
- booking request redirect to admin WhatsApp
- admin login and manual booking confirmation

## Run locally

1. Create a MySQL database by importing [database/schema.sql](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/database/schema.sql).
2. Update database credentials and WhatsApp number in [includes/config.php](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/includes/config.php).
3. Serve the project with PHP, for example:

```bash
php -S localhost:8000
```

4. Open `http://localhost:8000/index.php`.

## Default admin

- Username: `admin`
- Password: `admin123`

Change this immediately after setup.

## Main files

- Passenger booking page: [index.php](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/index.php)
- Booking submit action: [actions/submit-booking.php](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/actions/submit-booking.php)
- Admin login: [admin/login.php](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/admin/login.php)
- Admin bookings: [admin/bookings.php](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/admin/bookings.php)
- Admin seat view: [admin/seats.php](/Users/kaifzaki/Developer/Oncode/Bus_Room_Schedule/admin/seats.php)

## Notes

- Booking requests are stored first with `pending` status.
- Admin manually changes `pending` to `booked` after payment confirmation.
- Seat colors are derived from bookings for the selected date.
- The environment used to generate this starter did not have PHP installed, so runtime verification still needs to be done on a machine with PHP 8+.
