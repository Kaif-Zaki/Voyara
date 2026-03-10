<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$travelDate = selected_date_or_today(request_value('travel_date'));
$pageTitle = 'Admin Panel';
$isAdminArea = true;

$pdo = db();

$statsStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total_bookings,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_bookings,
        SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END) AS booked_bookings,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_bookings
    FROM bookings
    WHERE travel_date = :travel_date
");
$statsStmt->execute(['travel_date' => $travelDate]);
$stats = $statsStmt->fetch() ?: [];

$busesStmt = $pdo->query("
    SELECT
        COUNT(*) AS total_buses,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_buses
    FROM buses
");
$busStats = $busesStmt->fetch() ?: [];

$stopsStmt = $pdo->query("SELECT COUNT(*) AS total_stops FROM bus_stops");
$stopStats = $stopsStmt->fetch() ?: [];

$today = new DateTimeImmutable('today');
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $dates[] = $today->modify("-{$i} days")->format('Y-m-d');
}

$placeholders = implode(',', array_fill(0, count($dates), '?'));
$trendStmt = $pdo->prepare("
    SELECT travel_date, COUNT(*) AS total
    FROM bookings
    WHERE travel_date IN ($placeholders)
    GROUP BY travel_date
");
$trendStmt->execute($dates);
$trendRaw = $trendStmt->fetchAll();

$trendMap = [];
foreach ($trendRaw as $row) {
    $trendMap[$row['travel_date']] = (int) $row['total'];
}

$trendData = [];
foreach ($dates as $date) {
    $trendData[] = [
        'date' => $date,
        'total' => $trendMap[$date] ?? 0,
    ];
}

$maxTrend = max(array_map(static fn ($row) => $row['total'], $trendData)) ?: 1;

require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel stack-lg">
    <div class="split-row">
        <div>
            <p class="eyebrow">Admin panel</p>
            <h1>Overview Dashboard</h1>
            <p class="lead">Track today’s booking activity and manage your fleet in one view.</p>
        </div>
        <div class="inline-links">
            <a class="button secondary" href="/admin/bookings.php?travel_date=<?= h($travelDate) ?>">Booking Requests</a>
            <a class="button ghost" href="/admin/logout.php">Logout</a>
        </div>
    </div>

    <form method="get" class="inline-form">
        <label>
            <span>Travel Date</span>
            <input type="date" name="travel_date" value="<?= h($travelDate) ?>" required>
        </label>
        <button type="submit" class="button">Refresh</button>
    </form>

    <div class="cards-grid three-up admin-metrics">
        <div class="info-card">
            <h3>Today’s Requests</h3>
            <div class="metric-value"><?= (int) ($stats['total_bookings'] ?? 0) ?></div>
            <p class="muted">Total booking requests for <?= h($travelDate) ?>.</p>
        </div>
        <div class="info-card">
            <h3>Pending Approval</h3>
            <div class="metric-value"><?= (int) ($stats['pending_bookings'] ?? 0) ?></div>
            <p class="muted">Waiting for payment confirmation.</p>
        </div>
        <div class="info-card">
            <h3>Confirmed Bookings</h3>
            <div class="metric-value"><?= (int) ($stats['booked_bookings'] ?? 0) ?></div>
            <p class="muted">Marked as paid and confirmed.</p>
        </div>
    </div>

    <div class="cards-grid two-up admin-metrics">
        <div class="info-card">
            <h3>Fleet Status</h3>
            <div class="metric-split">
                <div>
                    <div class="metric-value"><?= (int) ($busStats['active_buses'] ?? 0) ?></div>
                    <p class="muted">Active buses</p>
                </div>
                <div>
                    <div class="metric-value"><?= (int) ($busStats['total_buses'] ?? 0) ?></div>
                    <p class="muted">Total buses</p>
                </div>
            </div>
        </div>
        <div class="info-card">
            <h3>Stops Configured</h3>
            <div class="metric-value"><?= (int) ($stopStats['total_stops'] ?? 0) ?></div>
            <p class="muted">Pickup and drop locations set by admin.</p>
        </div>
    </div>

    <div class="panel admin-chart">
        <div class="split-row">
            <div>
                <h2>7-Day Booking Trend</h2>
                <p class="muted">Requests by travel date.</p>
            </div>
            <a class="button ghost" href="/admin/bookings.php">View all</a>
        </div>
        <div class="chart-grid">
            <?php foreach ($trendData as $row): ?>
                <?php
                $height = (int) round(($row['total'] / $maxTrend) * 120);
                ?>
                <div class="chart-bar">
                    <div class="chart-bar-fill" style="height: <?= $height ?>px"></div>
                    <span><?= h(date('M j', strtotime($row['date']))) ?></span>
                    <strong><?= (int) $row['total'] ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
