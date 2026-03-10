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
            <label>
                <span>Drop Location</span>
                <input type="text" name="drop_location" value="<?= h($old['drop_location'] ?? '') ?>" list="dropStops" autocomplete="off" required>
            </label>
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
const bookingForm = document.getElementById('bookingForm');
const busInactiveAlert = document.getElementById('busInactiveAlert');

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
    } catch (e) {
        // ignore fetch errors
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
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
