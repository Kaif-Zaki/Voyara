<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$travelDate = selected_date_or_today(request_value('travel_date'));
$buses = get_buses();
$selectedBusId = (int) request_value('bus_id', $buses[0]['id'] ?? '0');
$selectedBus = $selectedBusId > 0 ? get_bus_by_id($selectedBusId) : null;
$seats = $selectedBus ? get_bus_seats_with_status($selectedBusId, $travelDate) : [];
$pageTitle = 'Manage Seats';
$isAdminArea = true;

require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel stack-lg">
    <div class="split-row">
        <div>
            <p class="eyebrow">Admin panel</p>
            <h1>Date-wise Seat Status</h1>
        </div>
        <div class="inline-links">
            <a class="button secondary" href="/admin/bookings.php?travel_date=<?= h($travelDate) ?>">View Bookings</a>
            <a class="button ghost" href="/admin/logout.php">Logout</a>
        </div>
    </div>

    <form method="get" class="inline-form">
        <label>
            <span>Travel Date</span>
            <input type="date" name="travel_date" value="<?= h($travelDate) ?>" required>
        </label>
        <label>
            <span>Bus</span>
            <select name="bus_id">
                <?php foreach ($buses as $bus): ?>
                    <option value="<?= (int) $bus['id'] ?>" <?= (int) $bus['id'] === $selectedBusId ? 'selected' : '' ?>>
                        <?= h($bus['name']) ?> (<?= h($bus['bus_number']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="button">Load</button>
    </form>

    <div class="legend">
        <span><i class="dot available"></i> Available</span>
        <span><i class="dot pending"></i> Pending</span>
        <span><i class="dot booked"></i> Booked</span>
    </div>

    <div class="seat-layout admin-layout">
        <?php foreach ($seats as $seat): ?>
            <div class="seat <?= h($seat['seat_status']) ?> static-seat"><?= h($seat['seat_number']) ?></div>
        <?php endforeach; ?>
    </div>

    <p class="muted">Seat status here is derived from booking records for the selected date. Update statuses from the booking list.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
