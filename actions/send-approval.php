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

if ($bookingId <= 0) {
    header('Location: /admin/bookings.php?travel_date=' . urlencode($travelDate));
    exit;
}

$booking = get_booking_details($bookingId);

if (!$booking) {
    header('Location: /admin/bookings.php?travel_date=' . urlencode($travelDate));
    exit;
}

$stmt = db()->prepare('UPDATE bookings SET status = :status WHERE id = :id');
$stmt->execute([
    'status' => 'booked',
    'id' => $bookingId,
]);

$routeLabel = get_bus_route_label([
    'origin' => $booking['bus_origin'] ?? '',
    'destination' => $booking['bus_destination'] ?? '',
]);

$offsets = get_stop_offsets_map((int) $booking['bus_id']);
$startTime = $booking['bus_start_time'] ?? '';
$endTime = $booking['bus_end_time'] ?? '';
$pickupTime = '';
$dropTime = '';
$duration = '';

$startMinutes = time_to_minutes($startTime);
if ($startMinutes !== null && $booking['pickup_point'] !== '' && array_key_exists($booking['pickup_point'], $offsets)) {
    $pickupTime = minutes_to_time($startMinutes + $offsets[$booking['pickup_point']]);
}
if ($startMinutes !== null && $booking['drop_location'] !== '' && array_key_exists($booking['drop_location'], $offsets)) {
    $dropTime = minutes_to_time($startMinutes + $offsets[$booking['drop_location']]);
}
if ($startTime !== '' && $endTime !== '') {
    $start = time_to_minutes($startTime);
    $end = time_to_minutes($endTime);
    if ($start !== null && $end !== null) {
        $minutes = $end >= $start ? $end - $start : (24 * 60 - $start + $end);
        $duration = format_duration_minutes($minutes);
    }
}

$messageLines = [
    'Booking Approved',
    '',
    'Name: ' . $booking['full_name'],
    'Phone: ' . $booking['phone'],
    'Date: ' . $booking['travel_date'],
    'Bus: ' . $booking['bus_name'] . ' (' . $booking['bus_number'] . ')',
];

if ($routeLabel !== '') {
    $messageLines[] = 'Route: ' . $routeLabel;
}

if ($startTime || $endTime) {
    $messageLines[] = 'Schedule: ' . ($startTime ?: '-') . ' → ' . ($endTime ?: '-');
}
if ($duration !== '') {
    $messageLines[] = 'Duration: ' . $duration;
}

$messageLines[] = 'Seats: ' . $booking['seat_numbers'];
$messageLines[] = 'Pickup: ' . $booking['pickup_point'] . ($pickupTime !== '' ? ' at ' . $pickupTime : '');
$messageLines[] = 'Drop: ' . $booking['drop_location'] . ($dropTime !== '' ? ' at ' . $dropTime : '');
$messageLines[] = 'Status: Booked';

$message = implode("\n", $messageLines);

$whatsAppNumber = normalize_phone_number((string) $booking['phone']);
if ($whatsAppNumber === '') {
    header('Location: /admin/bookings.php?travel_date=' . urlencode($travelDate));
    exit;
}
$whatsAppUrl = 'https://wa.me/' . $whatsAppNumber . '?text=' . rawurlencode($message);
header('Location: ' . $whatsAppUrl);
exit;
