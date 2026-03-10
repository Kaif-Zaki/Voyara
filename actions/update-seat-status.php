<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/seats.php');
    exit;
}

$seatId = (int) request_value('seat_id');
$busId = (int) request_value('bus_id');
$travelDate = selected_date_or_today(request_value('travel_date'));
$status = request_value('status');

if ($seatId <= 0 || $busId <= 0 || !in_array($status, ['available', 'pending', 'booked'], true)) {
    header('Location: /admin/seats.php?travel_date=' . urlencode($travelDate) . '&bus_id=' . $busId);
    exit;
}

$stmt = db()->prepare('
    INSERT INTO seat_status_overrides (bus_id, seat_id, travel_date, status)
    VALUES (:bus_id, :seat_id, :travel_date, :status)
    ON DUPLICATE KEY UPDATE status = VALUES(status)
');
$stmt->execute([
    'bus_id' => $busId,
    'seat_id' => $seatId,
    'travel_date' => $travelDate,
    'status' => $status,
]);

header('Location: /admin/seats.php?travel_date=' . urlencode($travelDate) . '&bus_id=' . $busId);
exit;
