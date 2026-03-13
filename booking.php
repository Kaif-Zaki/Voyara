<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Book Your Seat';
$travelDate = selected_date_or_today(request_value('travel_date'));
$buses = get_active_buses();
$selectedBusId = (int) request_value('bus_id', $buses[0]['id'] ?? '0');
$selectedBus = $selectedBusId > 0 ? get_bus_by_id($selectedBusId) : null;
$busInactive = $selectedBus ? (int) ($selectedBus['is_active'] ?? 1) !== 1 : false;
if ($busInactive && $buses !== []) {
    $selectedBusId = (int) $buses[0]['id'];
    $selectedBus = get_bus_by_id($selectedBusId);
    $busInactive = false;
}
$seats = ($selectedBus && !$busInactive) ? get_bus_seats_with_status($selectedBusId, $travelDate) : [];
$seatsByNumber = seats_by_number($seats);
$seatLayout = seat_layout_49();
$routeLabel = $selectedBus ? get_bus_route_label($selectedBus) : '';
$busStops = $selectedBus ? get_bus_stops($selectedBusId) : [];
$busType = $selectedBus ? ($selectedBus['bus_type'] ?? 'Normal') : 'Normal';
$busDescription = $selectedBus ? ($selectedBus['description'] ?? '') : '';
$busImage = $selectedBus ? ($selectedBus['image_url'] ?? '') : '';
$busImage = $busImage !== '' ? $busImage : 'https://images.unsplash.com/photo-tjbo7Vr04Xs?auto=format&fit=crop&w=1200&q=60';
$busStartTime = $selectedBus['start_time'] ?? '';
$busEndTime = $selectedBus['end_time'] ?? '';
$busDuration = '';
if ($busStartTime !== '' && $busEndTime !== '') {
    $startMinutes = time_to_minutes($busStartTime);
    $endMinutes = time_to_minutes($busEndTime);
    if ($startMinutes !== null && $endMinutes !== null) {
        $minutes = $endMinutes >= $startMinutes ? $endMinutes - $startMinutes : (24 * 60 - $startMinutes + $endMinutes);
        $busDuration = format_duration_minutes($minutes);
    }
}
$flashError = $_SESSION['booking_error'] ?? '';
$old = $_SESSION['booking_old'] ?? [];

unset($_SESSION['booking_error'], $_SESSION['booking_old']);

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact">
    <div>
        <p class="eyebrow">Passenger booking</p>
        <h1>Reserve a seat in minutes.</h1>
        <p class="lead">Pick a date, choose seats, and send a WhatsApp request to admin.</p>
    </div>
    <a class="button secondary" href="/faq.php">Need help?</a>
</section>

<?php if ($flashError !== ''): ?>
    <div class="alert error"><?= h($flashError) ?></div>
<?php endif; ?>
<?php if ($buses === []): ?>
    <div class="alert error">No active buses are available right now. Please check back later.</div>
<?php endif; ?>

<div class="grid-two booking-grid">
    <section class="panel glass-panel <?= $buses === [] ? 'is-disabled' : '' ?>">
        <h2>Seat Availability</h2>
        <p class="muted" id="routeLabel" <?= $routeLabel === '' ? 'style="display:none;"' : '' ?>>
            Route: <?= h($routeLabel) ?>
        </p>
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
            <span><i class="dot missing"></i> Not configured</span>
        </div>

        <div class="bus-structure" data-seat-map="49" aria-label="Bus seat map">
            <?php if ($buses === []): ?>
                <p class="muted">No active buses are available. Ask admin to activate a bus first.</p>
            <?php elseif (!$selectedBus): ?>
                <p class="muted">No bus found. Add buses and seats in the database first.</p>
            <?php elseif ($seats === []): ?>
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
                                        $status = $seat ? ($seat['seat_status'] ?? 'available') : 'missing';
                                        $seatId = (int) ($seat['id'] ?? 0);
                                        $label = $seat ? seat_label_49($slot) : 'Not configured';
                                        ?>
                                        <button
                                            type="button"
                                            class="seat <?= h($status) ?> <?= $seat ? '' : 'missing' ?> back-bench"
                                            data-seat-id="<?= $seatId ?: '' ?>"
                                            data-seat-number="<?= h($slot) ?>"
                                            data-seat-label="<?= h($label) ?>"
                                            title="<?= h($slot . ' - ' . $label) ?>"
                                            <?= ($status !== 'available' || $seatId === 0) ? 'disabled' : '' ?>
                                        >
                                            <span class="seat-num"><?= h($slot) ?></span>
                                        </button>
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
                                            $status = $seat ? ($seat['seat_status'] ?? 'available') : 'missing';
                                            $seatId = (int) ($seat['id'] ?? 0);
                                            $label = $seat ? seat_label_49($slot) : 'Not configured';
                                            ?>
                                            <button
                                                type="button"
                                                class="seat <?= h($status) ?> <?= $seat ? '' : 'missing' ?>"
                                                data-seat-id="<?= $seatId ?: '' ?>"
                                                data-seat-number="<?= h($slot) ?>"
                                                data-seat-label="<?= h($label) ?>"
                                                title="<?= h($slot . ' - ' . $label) ?>"
                                                <?= ($status !== 'available' || $seatId === 0) ? 'disabled' : '' ?>
                                            >
                                                <span class="seat-num"><?= h($slot) ?></span>
                                            </button>
                                        <?php endif;
                                    endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="panel booking-form-panel <?= $buses === [] ? 'is-disabled' : '' ?>">
        <div class="bus-detail-card" id="busDetailCard">
            <div class="bus-detail-cover" id="busDetailCover" style="background-image: url('<?= h($busImage) ?>')"></div>
            <div class="bus-detail-body">
                <div class="bus-detail-row">
                    <span class="bus-detail-label">Bus</span>
                    <strong id="busDetailName"><?= h($selectedBus ? $selectedBus['name'] : 'Bus') ?> (<?= h($selectedBus ? $selectedBus['bus_number'] : '-') ?>)</strong>
                </div>
                <div class="bus-detail-row">
                    <span class="bus-detail-label">Type</span>
                    <strong id="busDetailType"><?= h($busType ?: 'Normal') ?></strong>
                </div>
                <?php if ($routeLabel !== ''): ?>
                    <div class="bus-detail-row">
                        <span class="bus-detail-label">Route</span>
                        <strong id="busDetailRoute"><?= h($routeLabel) ?></strong>
                    </div>
                <?php endif; ?>
                <div class="bus-detail-row">
                    <span class="bus-detail-label">Seats</span>
                    <strong id="busDetailSeats"><?= (int) ($selectedBus ? $selectedBus['total_seats'] : 49) ?></strong>
                </div>
                <div class="bus-detail-row" id="busTimeRow" <?= $busStartTime || $busEndTime ? '' : 'style="display:none;"' ?>>
                    <span class="bus-detail-label">Schedule</span>
                    <strong id="busDetailTime">
                        <?= $busStartTime ? h($busStartTime) : '-' ?> → <?= $busEndTime ? h($busEndTime) : '-' ?>
                    </strong>
                </div>
                <div class="bus-detail-row" id="busDurationRow" style="<?= $busStartTime && $busEndTime ? '' : 'display:none;' ?>">
                    <span class="bus-detail-label">Duration</span>
                    <strong id="busDetailDuration"><?= h($busDuration) ?></strong>
                </div>
                <?php if ($busDescription !== ''): ?>
                    <p class="bus-detail-text" id="busDetailDesc"><?= h($busDescription) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="alert error" id="busInactiveAlert" style="display:none;">
            This bus is currently unavailable. Please choose another bus.
        </div>

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
                <input type="text" name="pickup_point" value="<?= h($old['pickup_point'] ?? '') ?>" list="pickupStops" autocomplete="off" required>
            </label>
            <small class="input-hint" id="pickupHint"></small>
            <label>
                <span>Drop Location</span>
                <input type="text" name="drop_location" value="<?= h($old['drop_location'] ?? '') ?>" list="dropStops" autocomplete="off" required>
            </label>
            <small class="input-hint" id="dropHint"></small>
            <datalist id="pickupStops">
                <?php foreach ($busStops as $stop): ?>
                    <option value="<?= h($stop) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <datalist id="dropStops">
                <?php foreach ($busStops as $stop): ?>
                    <option value="<?= h($stop) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <button type="submit" class="button">Send Booking to WhatsApp</button>
        </form>
    </section>
</div>

<script>
const chosenSeats = new Map(); // seatId -> { number, label }
const seatIdsInput = document.getElementById('seatIds');
const seatDisplayInput = document.getElementById('selectedSeatsDisplay');

function syncSelectedSeats() {
    const seatIds = [...chosenSeats.keys()];
    const seatText = [...chosenSeats.values()].map((s) => `${s.number} (${s.label})`);
    seatIdsInput.value = seatIds.join(',');
    seatDisplayInput.value = seatText.join(', ');
}

document.querySelectorAll('.seat.available').forEach((seatButton) => {
    seatButton.addEventListener('click', () => {
        const seatId = seatButton.dataset.seatId;
        const seatNumber = seatButton.dataset.seatNumber;
        const seatLabel = seatButton.dataset.seatLabel;

        if (chosenSeats.has(seatId)) {
            chosenSeats.delete(seatId);
            seatButton.classList.remove('selected');
        } else {
            chosenSeats.set(seatId, { number: seatNumber, label: seatLabel });
            seatButton.classList.add('selected');
        }

        syncSelectedSeats();
    });
});

const busSelect = document.querySelector('select[name="bus_id"]');
const pickupList = document.getElementById('pickupStops');
const dropList = document.getElementById('dropStops');
const routeLabelEl = document.getElementById('routeLabel');
const detailName = document.getElementById('busDetailName');
const detailType = document.getElementById('busDetailType');
const detailRoute = document.getElementById('busDetailRoute');
const detailSeats = document.getElementById('busDetailSeats');
const detailDesc = document.getElementById('busDetailDesc');
const detailCover = document.getElementById('busDetailCover');
const detailTime = document.getElementById('busDetailTime');
const detailTimeRow = document.getElementById('busTimeRow');
const detailDurationRow = document.getElementById('busDurationRow');
const detailDuration = document.getElementById('busDetailDuration');
const bookingForm = document.getElementById('bookingForm');
const busInactiveAlert = document.getElementById('busInactiveAlert');
const pickupInput = document.querySelector('input[name="pickup_point"]');
const dropInput = document.querySelector('input[name="drop_location"]');
const pickupHint = document.getElementById('pickupHint');
const dropHint = document.getElementById('dropHint');

let stopOffsets = {};
let stopTimes = {};
let busStartTime = '';
let busEndTime = '';

function timeToMinutes(time) {
    if (!time) return null;
    const parts = time.split(':');
    const h = Number(parts[0] || 0);
    const m = Number(parts[1] || 0);
    return h * 60 + m;
}

function minutesToTime(minutes) {
    const mins = ((minutes % (24 * 60)) + (24 * 60)) % (24 * 60);
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
}

function formatDuration(minutes) {
    if (minutes == null || Number.isNaN(minutes)) return '';
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    if (h > 0 && m > 0) return `${h}h ${m}m`;
    if (h > 0) return `${h}h`;
    return `${m}m`;
}

function setBusActiveState(isActive) {
    if (busInactiveAlert) {
        busInactiveAlert.style.display = isActive ? 'none' : 'block';
    }
    if (bookingForm) {
        bookingForm.querySelectorAll('input, select, textarea, button').forEach((el) => {
            if (el.type === 'hidden') {
                return;
            }
            el.disabled = !isActive;
        });
    }
    document.querySelectorAll('.bus-structure .seat').forEach((seat) => {
        if (seat.tagName.toLowerCase() === 'button') {
            seat.disabled = seat.disabled || !isActive;
        }
        seat.classList.toggle('disabled', !isActive);
    });
}

async function loadStops(busId) {
    pickupList.innerHTML = '';
    dropList.innerHTML = '';
    if (!busId) {
        return;
    }
    try {
        const response = await fetch(`/actions/get-stops.php?bus_id=${busId}`);
        if (!response.ok) {
            return;
        }
        const data = await response.json();
        const stops = Array.isArray(data.stops) ? data.stops : [];
        const route = typeof data.route === 'string' ? data.route : '';
        const bus = data.bus || null;
        stopOffsets = data.stop_offsets || {};
        stopTimes = data.stop_times || {};
        busStartTime = bus && bus.start_time ? bus.start_time : '';
        busEndTime = bus && bus.end_time ? bus.end_time : '';
        const options = stops.map((stop) => `<option value="${stop}"></option>`).join('');
        pickupList.innerHTML = options;
        dropList.innerHTML = options;
        if (routeLabelEl) {
            if (route) {
                routeLabelEl.textContent = `Route: ${route}`;
                routeLabelEl.style.display = 'block';
            } else {
                routeLabelEl.textContent = '';
                routeLabelEl.style.display = 'none';
            }
        }
        if (bus && detailName && detailType && detailSeats) {
            detailName.textContent = `${bus.name} (${bus.bus_number})`;
            detailType.textContent = bus.bus_type || 'Normal';
            detailSeats.textContent = bus.total_seats || 49;
            if (detailTime && detailTimeRow) {
                if (bus.start_time || bus.end_time) {
                    detailTime.textContent = `${bus.start_time || '-'} → ${bus.end_time || '-'}`;
                    detailTimeRow.style.display = 'flex';
                } else {
                    detailTimeRow.style.display = 'none';
                }
            }
            if (detailDuration && detailDurationRow) {
                const start = timeToMinutes(bus.start_time || '');
                const end = timeToMinutes(bus.end_time || '');
                if (start != null && end != null) {
                    const duration = end >= start ? end - start : (24 * 60 - start + end);
                    detailDuration.textContent = formatDuration(duration);
                    detailDurationRow.style.display = 'flex';
                } else {
                    detailDurationRow.style.display = 'none';
                }
            }
            if (detailRoute) {
                detailRoute.textContent = route || '';
                detailRoute.parentElement.style.display = route ? 'flex' : 'none';
            }
            if (detailDesc) {
                detailDesc.textContent = bus.description || '';
                detailDesc.style.display = bus.description ? 'block' : 'none';
            }
            if (detailCover) {
                detailCover.style.backgroundImage = `url('${bus.image_url || 'https://images.unsplash.com/photo-tjbo7Vr04Xs?auto=format&fit=crop&w=1200&q=60'}')`;
            }
        }
        setBusActiveState(!!bus && Number(bus.is_active) === 1);
        updatePickupDropHints();
    } catch (e) {
        // ignore fetch errors
    }
}

function updatePickupDropHints() {
    if (!pickupHint || !dropHint) {
        return;
    }
    const start = timeToMinutes(busStartTime);
    const pickup = pickupInput ? pickupInput.value.trim() : '';
    const drop = dropInput ? dropInput.value.trim() : '';
    if (pickup && Object.prototype.hasOwnProperty.call(stopTimes, pickup)) {
        pickupHint.textContent = `Estimated pickup time: ${stopTimes[pickup]}`;
    } else if (start != null && pickup && Object.prototype.hasOwnProperty.call(stopOffsets, pickup)) {
        const minutes = start + Number(stopOffsets[pickup] || 0);
        pickupHint.textContent = `Estimated pickup time: ${minutesToTime(minutes)}`;
    } else {
        pickupHint.textContent = '';
    }
    if (drop && Object.prototype.hasOwnProperty.call(stopTimes, drop)) {
        dropHint.textContent = `Estimated drop time: ${stopTimes[drop]}`;
    } else if (start != null && drop && Object.prototype.hasOwnProperty.call(stopOffsets, drop)) {
        const minutes = start + Number(stopOffsets[drop] || 0);
        dropHint.textContent = `Estimated drop time: ${minutesToTime(minutes)}`;
    } else {
        dropHint.textContent = '';
    }
}

if (busSelect) {
    const busForm = busSelect.closest('form');
    const dateInput = busForm ? busForm.querySelector('input[name="travel_date"]') : null;
    busSelect.addEventListener('change', (event) => {
        if (busForm) {
            busForm.submit();
            return;
        }
        loadStops(event.target.value);
    });
    if (dateInput) {
        dateInput.addEventListener('change', () => {
            busForm.submit();
        });
    }
    loadStops(busSelect.value);
}

if (pickupInput) {
    pickupInput.addEventListener('input', updatePickupDropHints);
}
if (dropInput) {
    dropInput.addEventListener('input', updatePickupDropHints);
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
