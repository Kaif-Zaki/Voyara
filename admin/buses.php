<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$buses = get_buses();
$pageTitle = 'Manage Buses';
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
            <h1>Bus Routes & Stops</h1>
        </div>
        <div class="inline-links">
            <a class="button secondary" href="/admin/bookings.php">View Bookings</a>
            <a class="button ghost" href="/admin/logout.php">Logout</a>
        </div>
    </div>

    <div class="bus-cards">
        <details class="bus-accordion add-bus">
            <summary>
                <div class="summary-main">
                    <div class="summary-title">Add New Bus</div>
                    <div class="summary-meta">Create a new bus and seed 49 seats automatically.</div>
                </div>
                <div class="summary-right">
                    <span class="status-chip status-active">New</span>
                </div>
            </summary>
            <div class="bus-accordion-body">
                <form method="post" action="/actions/create-bus.php" class="stack-md" enctype="multipart/form-data">
                    <div class="grid-two">
                        <label>
                            <span>Bus Name</span>
                            <input type="text" name="name" placeholder="e.g., Voyara Express" required>
                        </label>
                        <label>
                            <span>Bus Number</span>
                            <input type="text" name="bus_number" placeholder="e.g., NB-1003" required>
                        </label>
                    </div>
                    <div class="grid-two">
                        <label>
                            <span>Bus Type</span>
                            <select name="bus_type">
                                <?php foreach (bus_types() as $type): ?>
                                    <option value="<?= h($type) ?>"><?= h($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Cover Image URL (optional)</span>
                            <input type="text" name="image_url" placeholder="Paste image URL">
                        </label>
                    </div>
                    <label>
                        <span>Upload Image (optional)</span>
                        <input type="file" name="image_file" accept="image/*">
                    </label>
                    <div class="grid-two">
                        <label>
                            <span>Route From (Origin)</span>
                            <input type="text" name="origin" placeholder="e.g., Colombo">
                        </label>
                        <label>
                            <span>Route To (Destination)</span>
                            <input type="text" name="destination" placeholder="e.g., Kandy">
                        </label>
                    </div>
                    <div class="grid-two">
                        <label>
                            <span>Start Time</span>
                            <input type="time" name="start_time">
                        </label>
                        <label>
                            <span>End Time</span>
                            <input type="time" name="end_time">
                        </label>
                    </div>
                    <div class="grid-two">
                        <label>
                            <span>Bus Details (optional)</span>
                            <textarea name="description" rows="3" placeholder="e.g., AC, WiFi, Recliner seats"></textarea>
                        </label>
                        <div>
                            <span class="input-label">Stops & Arrival Time</span>
                            <div class="stops-builder" data-stops-builder>
                                <div class="stops-row" data-stop-row>
                                    <input type="text" name="stop_names[]" placeholder="Stop name">
                                    <input type="time" name="stop_times[]">
                                    <button type="button" class="button ghost small" data-remove-stop>Remove</button>
                                </div>
                            </div>
                            <div class="inline-form">
                                <button type="button" class="button secondary small" data-add-stop>Add stop</button>
                            </div>
                            <small class="input-hint">Use exact arrival time for each stop (e.g., 08:30).</small>
                        </div>
                    </div>
                    <div class="inline-form">
                        <input type="hidden" name="is_active" value="0">
                        <label class="inline-switch">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>Active and visible to passengers</span>
                        </label>
                    </div>
                    <button type="submit" class="button">Add Bus</button>
                </form>
            </div>
        </details>

        <?php foreach ($buses as $bus): ?>
            <?php
        $stopRows = get_bus_stops_with_offsets((int) $bus['id']);
        $routeLabel = get_bus_route_label($bus);
        $busType = $bus['bus_type'] ?? 'Normal';
        $isActive = (int) ($bus['is_active'] ?? 1) === 1;
        $stopCount = count($stopRows);
        $startTime = $bus['start_time'] ?? '';
        $endTime = $bus['end_time'] ?? '';
        ?>
            <details class="bus-accordion">
                <summary>
                    <div class="summary-main">
                        <div class="summary-title"><?= h($bus['name']) ?> (<?= h($bus['bus_number']) ?>)</div>
                        <div class="summary-meta">
                            <span class="pill"><?= h($busType) ?></span>
                            <?php if ($routeLabel !== ''): ?>
                                <span class="pill"><?= h($routeLabel) ?></span>
                            <?php endif; ?>
                            <span class="pill"><?= $stopCount ?> stops</span>
                        </div>
                    </div>
                    <div class="summary-right">
                        <span class="status-chip <?= $isActive ? 'status-active' : 'status-inactive' ?>">
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                        </span>
                        <span class="pill">Manage</span>
                    </div>
                </summary>
                <div class="bus-accordion-body">
                    <form method="post" action="/actions/update-bus.php" class="stack-md" enctype="multipart/form-data">
                        <input type="hidden" name="bus_id" value="<?= (int) $bus['id'] ?>">
                        <div class="grid-two">
                            <label>
                                <span>Bus Name</span>
                                <input type="text" name="name" value="<?= h($bus['name']) ?>" required>
                            </label>
                            <label>
                                <span>Bus Number</span>
                                <input type="text" name="bus_number" value="<?= h($bus['bus_number']) ?>" required>
                            </label>
                        </div>
                        <div class="grid-two">
                            <label>
                                <span>Bus Type</span>
                                <select name="bus_type">
                                    <?php foreach (bus_types() as $type): ?>
                                        <option value="<?= h($type) ?>" <?= $type === $busType ? 'selected' : '' ?>><?= h($type) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>
                                <span>Cover Image URL (optional)</span>
                                <input type="text" name="image_url" value="<?= h($bus['image_url'] ?? '') ?>" placeholder="Paste image URL">
                            </label>
                        </div>
                        <label>
                            <span>Upload Image (optional)</span>
                            <input type="file" name="image_file" accept="image/*">
                        </label>
                        <div class="grid-two">
                            <label>
                                <span>Route From (Origin)</span>
                                <input type="text" name="origin" value="<?= h($bus['origin'] ?? '') ?>" placeholder="e.g., Colombo">
                            </label>
                            <label>
                                <span>Route To (Destination)</span>
                                <input type="text" name="destination" value="<?= h($bus['destination'] ?? '') ?>" placeholder="e.g., Kandy">
                            </label>
                        </div>
                        <div class="grid-two">
                            <label>
                                <span>Start Time</span>
                                <input type="time" name="start_time" value="<?= h($startTime) ?>">
                            </label>
                            <label>
                                <span>End Time</span>
                                <input type="time" name="end_time" value="<?= h($endTime) ?>">
                            </label>
                        </div>
                        <label>
                            <span>Bus Details (optional)</span>
                            <textarea name="description" rows="3" placeholder="e.g., AC, WiFi, Recliner seats"><?= h($bus['description'] ?? '') ?></textarea>
                        </label>
                        <div>
                            <span class="input-label">Stops & Arrival Time</span>
                            <div class="stops-builder" data-stops-builder>
                                <?php if ($stopRows === []): ?>
                                    <div class="stops-row" data-stop-row>
                                        <input type="text" name="stop_names[]" placeholder="Stop name">
                                        <input type="time" name="stop_times[]">
                                        <button type="button" class="button ghost small" data-remove-stop>Remove</button>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($stopRows as $row): ?>
                                        <?php
                                        $stopTime = $row['stop_time'] ?? '';
                                        if ($stopTime === '' && $startTime !== '') {
                                            $startMinutes = time_to_minutes($startTime);
                                            $offsetMinutes = (int) ($row['stop_offset_minutes'] ?? 0);
                                            if ($startMinutes !== null) {
                                                $stopTime = minutes_to_time($startMinutes + $offsetMinutes);
                                            }
                                        }
                                        ?>
                                        <div class="stops-row" data-stop-row>
                                            <input type="text" name="stop_names[]" value="<?= h($row['stop_name']) ?>" placeholder="Stop name">
                                            <input type="time" name="stop_times[]" value="<?= h($stopTime) ?>">
                                            <button type="button" class="button ghost small" data-remove-stop>Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="inline-form">
                                <button type="button" class="button secondary small" data-add-stop>Add stop</button>
                            </div>
                            <small class="input-hint">Use exact arrival time for each stop (e.g., 08:30).</small>
                        </div>
                        <div class="inline-form">
                            <input type="hidden" name="is_active" value="0">
                            <label class="inline-switch">
                                <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
                                <span>Active and visible to passengers</span>
                            </label>
                        </div>
                        <button type="submit" class="button">Save Bus</button>
                    </form>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
<script>
    document.querySelectorAll('[data-stops-builder]').forEach((builder) => {
        builder.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-stop]');
            if (!button) {
                return;
            }
            const row = button.closest('[data-stop-row]');
            if (!row) {
                return;
            }
            const rows = builder.querySelectorAll('[data-stop-row]');
            if (rows.length <= 1) {
                row.querySelectorAll('input').forEach((input) => {
                    input.value = '';
                });
                return;
            }
            row.remove();
        });
    });

    document.querySelectorAll('[data-add-stop]').forEach((button) => {
        button.addEventListener('click', () => {
            const form = button.closest('form');
            if (!form) {
                return;
            }
            const builder = form.querySelector('[data-stops-builder]');
            if (!builder) {
                return;
            }
            const row = document.createElement('div');
            row.className = 'stops-row';
            row.setAttribute('data-stop-row', '');
            row.innerHTML = `
                <input type="text" name="stop_names[]" placeholder="Stop name">
                <input type="time" name="stop_times[]">
                <button type="button" class="button ghost small" data-remove-stop>Remove</button>
            `;
            builder.appendChild(row);
        });
    });
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
