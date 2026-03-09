<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/bookings.php');
    exit;
}

$bookingId = (int) request_value('booking_id');
$travelDate = selected_date_or_today(request_value('travel_date'));
$status = request_value('status');

if ($bookingId <= 0 || !in_array($status, ['pending', 'booked'], true)) {
    header('Location: /admin/bookings.php?travel_date=' . urlencode($travelDate));
    exit;
}

$booking = get_booking_by_id($bookingId);

if ($booking) {
    $stmt = db()->prepare('UPDATE bookings SET status = :status WHERE id = :id');
    $stmt->execute([
        'status' => $status,
        'id' => $bookingId,
    ]);
}

header('Location: /admin/bookings.php?travel_date=' . urlencode($travelDate));
exit;
