<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Book Your Seat';
$travelDate = selected_date_or_today(request_value('travel_date'));
$buses = get_buses();
$selectedBusId = (int) request_value('bus_id', $buses[0]['id'] ?? '0');
$selectedBus = $selectedBusId > 0 ? get_bus_by_id($selectedBusId) : null;
$seats = $selectedBus ? get_bus_seats_with_status($selectedBusId, $travelDate) : [];
$flashError = $_SESSION['booking_error'] ?? '';
$old = $_SESSION['booking_old'] ?? [];

unset($_SESSION['booking_error'], $_SESSION['booking_old']);

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact">
    <div>
        <p class="eyebrow">Passenger booking</p>
        <h1>Reserve a Voyara seat in one short flow.</h1>
        <p class="lead">Select your travel date, choose available seats, and send your request to admin through WhatsApp.</p>
    </div>
    <a class="button secondary" href="/faq.php">Need help?</a>
</section>

<?php if ($flashError !== ''): ?>
    <div class="alert error"><?= h($flashError) ?></div>
<?php endif; ?>

<div class="grid-two booking-grid">
    <section class="panel glass-panel">
        <h2>Seat Availability</h2>
        <form method="get" class="stack-md">
            <label>
                <span>Travel Date</span>
                <input type="date" name="travel_date" value="<?= h($travelDate) ?>" min="<?= h(date('Y-m-d')) ?>" required>
            </label>
            <label>
                <span>Select Bus</span>
                <select name="bus_id" required>
                    <?php foreach ($buses as $bus): ?>
                        <option value="<?= (int) $bus['id'] ?>" <?= (int) $bus['id'] === $selectedBusId ? 'selected' : '' ?>>
                            <?= h($bus['name']) ?> (<?= h($bus['bus_number']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="button">Refresh Seats</button>
        </form>

        <div class="legend">
            <span><i class="dot available"></i> Available</span>
            <span><i class="dot pending"></i> Pending</span>
            <span><i class="dot booked"></i> Booked</span>
        </div>

        <div class="seat-layout">
            <?php if (!$selectedBus): ?>
                <p class="muted">No bus found. Add buses and seats in the database first.</p>
            <?php else: ?>
                <?php foreach ($seats as $seat): ?>
                    <button
                        type="button"
                        class="seat <?= h($seat['seat_status']) ?>"
                        data-seat-id="<?= (int) $seat['id'] ?>"
                        data-seat-number="<?= h($seat['seat_number']) ?>"
                        <?= $seat['seat_status'] !== 'available' ? 'disabled' : '' ?>
                    >
                        <?= h($seat['seat_number']) ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="panel booking-form-panel">
        <h2>Passenger Details</h2>
        <form method="post" action="/actions/submit-booking.php" id="bookingForm" class="stack-md">
            <input type="hidden" name="travel_date" value="<?= h($travelDate) ?>">
            <input type="hidden" name="bus_id" value="<?= (int) $selectedBusId ?>">
            <input type="hidden" name="seat_ids" id="seatIds" value="">

            <label>
                <span>Selected Seats</span>
                <input type="text" id="selectedSeatsDisplay" value="" placeholder="Pick from the seat layout" readonly>
            </label>
            <label>
                <span>Full Name</span>
                <input type="text" name="full_name" value="<?= h($old['full_name'] ?? '') ?>" required>
            </label>
            <label>
                <span>Phone Number</span>
                <input type="text" name="phone" value="<?= h($old['phone'] ?? '') ?>" required>
            </label>
            <label>
                <span>Pickup Point</span>
                <input type="text" name="pickup_point" value="<?= h($old['pickup_point'] ?? '') ?>" required>
            </label>
            <label>
                <span>Drop Location</span>
                <input type="text" name="drop_location" value="<?= h($old['drop_location'] ?? '') ?>" required>
            </label>
            <button type="submit" class="button">Send Booking to WhatsApp</button>
        </form>
    </section>
</div>

<script>
const chosenSeatIds = new Set();
const chosenSeatNumbers = new Set();
const seatIdsInput = document.getElementById('seatIds');
const seatDisplayInput = document.getElementById('selectedSeatsDisplay');

function syncSelectedSeats() {
    seatIdsInput.value = [...chosenSeatIds].join(',');
    seatDisplayInput.value = [...chosenSeatNumbers].join(', ');
}

document.querySelectorAll('.seat.available').forEach((seatButton) => {
    seatButton.addEventListener('click', () => {
        const seatId = seatButton.dataset.seatId;
        const seatNumber = seatButton.dataset.seatNumber;

        if (chosenSeatIds.has(seatId)) {
            chosenSeatIds.delete(seatId);
            chosenSeatNumbers.delete(seatNumber);
            seatButton.classList.remove('selected');
        } else {
            chosenSeatIds.add(seatId);
            chosenSeatNumbers.add(seatNumber);
            seatButton.classList.add('selected');
        }

        syncSelectedSeats();
    });
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
