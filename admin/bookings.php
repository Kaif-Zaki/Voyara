<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$travelDate = selected_date_or_today(request_value('travel_date'));
$bookings = get_date_bookings($travelDate);
$pageTitle = 'Booking Requests';
$isAdminArea = true;

require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel stack-lg">
    <div class="split-row">
        <div>
            <p class="eyebrow">Admin panel</p>
            <h1>Booking Requests</h1>
        </div>
        <div class="inline-links">
            <a class="button secondary" href="/admin/seats.php?travel_date=<?= h($travelDate) ?>">Manage Seats</a>
            <a class="button ghost" href="/admin/logout.php">Logout</a>
        </div>
    </div>

    <form method="get" class="inline-form">
        <label>
            <span>Travel Date</span>
            <input type="date" name="travel_date" value="<?= h($travelDate) ?>" required>
        </label>
        <button type="submit" class="button">Load</button>
    </form>

    <?php if ($bookings === []): ?>
        <p class="muted">No booking requests found for <?= h($travelDate) ?>.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Passenger</th>
                    <th>Bus</th>
                    <th>Seats</th>
                    <th>Route</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>
                            <strong><?= h($booking['full_name']) ?></strong><br>
                            <?= h($booking['phone']) ?>
                        </td>
                        <td><?= h($booking['bus_name']) ?></td>
                        <td><?= h($booking['seat_numbers']) ?></td>
                        <td><?= h($booking['pickup_point']) ?> to <?= h($booking['drop_location']) ?></td>
                        <td><span class="status-chip <?= h($booking['status']) ?>"><?= h(status_label($booking['status'])) ?></span></td>
                        <td>
                            <form method="post" action="/actions/update-booking-status.php" class="inline-form">
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <input type="hidden" name="travel_date" value="<?= h($travelDate) ?>">
                                <select name="status">
                                    <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="booked" <?= $booking['status'] === 'booked' ? 'selected' : '' ?>>Booked</option>
                                </select>
                                <button type="submit" class="button small">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
