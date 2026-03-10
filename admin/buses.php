<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$buses = get_buses();
$pageTitle = 'Manage Buses';
$isAdminArea = true;

require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel stack-lg">
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
                <form method="post" action="/actions/create-bus.php" class="stack-md">
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
                            <span>Cover Image (optional)</span>
                            <input type="text" name="image_url" placeholder="Paste image URL">
                        </label>
                    </div>
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
                            <span>Bus Details (optional)</span>
                            <textarea name="description" rows="3" placeholder="e.g., AC, WiFi, Recliner seats"></textarea>
                        </label>
                        <label>
                            <span>Bus Stops (one per line)</span>
                            <textarea name="stops" rows="3" placeholder="Enter stop names"></textarea>
                        </label>
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
            $stops = get_bus_stops((int) $bus['id']);
            $stopsText = implode("\n", $stops);
            $routeLabel = get_bus_route_label($bus);
            $busType = $bus['bus_type'] ?? 'Normal';
            $isActive = (int) ($bus['is_active'] ?? 1) === 1;
            $stopCount = count($stops);
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
                    <form method="post" action="/actions/update-bus.php" class="stack-md">
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
                                <span>Cover Image (optional)</span>
                                <input type="text" name="image_url" value="<?= h($bus['image_url'] ?? '') ?>" placeholder="Paste image URL">
                            </label>
                        </div>
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
                        <label>
                            <span>Bus Details (optional)</span>
                            <textarea name="description" rows="3" placeholder="e.g., AC, WiFi, Recliner seats"><?= h($bus['description'] ?? '') ?></textarea>
                        </label>
                        <label>
                            <span>Bus Stops (one per line)</span>
                            <textarea name="stops" rows="6" placeholder="Enter stop names"><?= h($stopsText) ?></textarea>
                        </label>
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
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
