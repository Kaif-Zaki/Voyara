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
$seatsByNumber = seats_by_number($seats);
$seatLayout = seat_layout_49();
$pageTitle = 'Manage Seats';
$isAdminArea = true;

require_once __DIR__ . '/../includes/header.php';
$flashError = flash_get('error');
$flashSuccess = flash_get('success');
?>
<section class="panel stack-lg">
    <?php if ($flashError !== ''): ?>
        <div class="alert error"><?= h($flashError) ?></div>
    <?php endif; ?>
    <?php if ($flashSuccess !== ''): ?>
        <div class="alert success"><?= h($flashSuccess) ?></div>
    <?php endif; ?>
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
                    <?php $isActive = (int) ($bus['is_active'] ?? 1) === 1; ?>
                    <option value="<?= (int) $bus['id'] ?>" <?= (int) $bus['id'] === $selectedBusId ? 'selected' : '' ?>>
                        <?= h($bus['name']) ?> (<?= h($bus['bus_number']) ?>)<?= $isActive ? '' : ' - Inactive' ?>
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

    <div class="bus-structure admin-layout" data-seat-map="49" aria-label="Bus seat map">
        <?php if ($seats === []): ?>
            <p class="muted">No seats configured for this bus. Import the schema to seed seats.</p>
        <?php else: ?>
        <div class="bus-outline" role="group" aria-label="Bus layout">
            <div class="bus-top">
                <div class="bus-crew" aria-hidden="true">
                    <div class="bus-staff">KRU</div>
                    <div class="bus-staff">KRU</div>
                </div>
                <div class="bus-pilot" aria-hidden="true">PILOT</div>
            </div>

            <div class="bus-seat-rows" role="group" aria-label="Passenger seats">
                <?php foreach ($seatLayout as $row): ?>
                    <?php
                    $slots = $row['slots'];
                    $isBack = $row['type'] === 'back';
                    ?>
                    <div class="bus-seat-row <?= $isBack ? 'is-back' : '' ?>" role="group" aria-label="<?= $isBack ? 'Back row' : 'Row' ?>">
                        <?php if ($isBack): ?>
                            <?php foreach ($slots as $slot): ?>
                                <?php
                                $seat = $seatsByNumber[$slot] ?? null;
                                $status = $seat ? ($seat['seat_status'] ?? 'available') : 'disabled';
                                $missingClass = $seat ? '' : 'disabled';
                                $label = $seat ? seat_label_49($slot) : '';
                                ?>
                                <div class="seat <?= h($status) ?> static-seat back-bench <?= h($missingClass) ?>" data-seat-id="<?= $seat ? (int) $seat['id'] : '' ?>" data-seat-label="<?= $label !== '' ? h($slot . ' - ' . $label) : '' ?>" title="<?= $label !== '' ? h($slot . ' - ' . $label) : '' ?>">
                                    <span class="seat-num"><?= h($slot) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php
                            $leftWindow = $slots[0];
                            $leftAisle = $slots[1];
                            $rightAisle = $slots[3];
                            $rightWindow = $slots[4];
                            $rowSlots = [$leftWindow, $leftAisle, null, $rightAisle, $rightWindow];
                            foreach ($rowSlots as $slot):
                                if ($slot === null): ?>
                                    <div class="bus-aisle" aria-hidden="true"></div>
                                <?php else:
                                    $seat = $seatsByNumber[$slot] ?? null;
                                    $status = $seat ? ($seat['seat_status'] ?? 'available') : 'disabled';
                                    $missingClass = $seat ? '' : 'disabled';
                                    $label = $seat ? seat_label_49($slot) : '';
                                    ?>
                                    <div class="seat <?= h($status) ?> static-seat <?= h($missingClass) ?>" data-seat-id="<?= $seat ? (int) $seat['id'] : '' ?>" data-seat-label="<?= $label !== '' ? h($slot . ' - ' . $label) : '' ?>" title="<?= $label !== '' ? h($slot . ' - ' . $label) : '' ?>">
                                        <span class="seat-num"><?= h($slot) ?></span>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="seat-admin-controls">
        <div class="muted" id="seatStatusSelected">Select a seat to update status.</div>
        <form method="post" action="/actions/update-seat-status.php" id="seatStatusForm" class="inline-form">
            <input type="hidden" name="seat_id" id="seatStatusSeatId" value="">
            <input type="hidden" name="bus_id" value="<?= (int) $selectedBusId ?>">
            <input type="hidden" name="travel_date" value="<?= h($travelDate) ?>">
            <input type="hidden" name="status" id="seatStatusValue" value="">
            <button type="button" class="button secondary" data-status="available">Available</button>
            <button type="button" class="button" data-status="pending">Pending</button>
            <button type="button" class="button ghost" data-status="booked">Booked</button>
        </form>
    </div>

    <p class="muted">Seat status here is derived from booking records for the selected date. Update statuses from the booking list.</p>
</section>
<script>
const seatButtons = document.querySelectorAll('.bus-structure .seat.static-seat');
const seatIdInput = document.getElementById('seatStatusSeatId');
const seatStatusValue = document.getElementById('seatStatusValue');
const seatStatusForm = document.getElementById('seatStatusForm');
const seatStatusSelected = document.getElementById('seatStatusSelected');

seatButtons.forEach((seat) => {
    seat.addEventListener('click', () => {
        if (!seat.dataset.seatId) {
            return;
        }
        seatButtons.forEach((s) => s.classList.remove('selected'));
        seat.classList.add('selected');
        seatIdInput.value = seat.dataset.seatId || '';
        seatStatusSelected.textContent = seat.dataset.seatLabel
            ? `Selected: ${seat.dataset.seatLabel}`
            : `Selected seat`;
    });
});

document.querySelectorAll('#seatStatusForm button[data-status]').forEach((btn) => {
    btn.addEventListener('click', () => {
        if (!seatIdInput.value) {
            return;
        }
        seatStatusValue.value = btn.dataset.status;
        seatStatusForm.submit();
    });
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
