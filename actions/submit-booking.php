<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /booking.php');
    exit;
}

$travelDate = selected_date_or_today(request_value('travel_date'));
$busId = (int) request_value('bus_id');
$fullName = request_value('full_name');
$phone = request_value('phone');
$pickupPoint = request_value('pickup_point');
$dropLocation = request_value('drop_location');
$seatIds = array_values(array_filter(array_map('intval', explode(',', request_value('seat_ids')))));

$_SESSION['booking_old'] = [
    'full_name' => $fullName,
    'phone' => $phone,
    'pickup_point' => $pickupPoint,
    'drop_location' => $dropLocation,
];

if ($busId <= 0 || $fullName === '' || $phone === '' || $pickupPoint === '' || $dropLocation === '' || $seatIds === []) {
    $_SESSION['booking_error'] = 'Complete all fields and select at least one available seat.';
    header('Location: /booking.php?travel_date=' . urlencode($travelDate) . '&bus_id=' . $busId);
    exit;
}

$bus = get_bus_by_id($busId);

if (!$bus) {
    $_SESSION['booking_error'] = 'Selected bus was not found.';
    header('Location: /booking.php');
    exit;
}

if ((int) ($bus['is_active'] ?? 1) !== 1) {
    $_SESSION['booking_error'] = 'Selected bus is currently unavailable. Please choose another bus.';
    header('Location: /booking.php?travel_date=' . urlencode($travelDate));
    exit;
}

$seatPlaceholders = implode(',', array_fill(0, count($seatIds), '?'));

try {
    $pdo = db();
    $pdo->beginTransaction();

    $seatCheckSql = "
        SELECT s.id, s.seat_number,
            CASE
                WHEN o.status IS NOT NULL THEN o.status
                WHEN MAX(CASE WHEN b.status = 'booked' THEN 2 WHEN b.status = 'pending' THEN 1 ELSE 0 END) = 2 THEN 'booked'
                WHEN MAX(CASE WHEN b.status = 'booked' THEN 2 WHEN b.status = 'pending' THEN 1 ELSE 0 END) = 1 THEN 'pending'
                ELSE 'available'
            END AS effective_status
        FROM seats s
        LEFT JOIN seat_status_overrides o
            ON o.seat_id = s.id
            AND o.travel_date = ?
        LEFT JOIN booking_seats bs ON bs.seat_id = s.id
        LEFT JOIN bookings b ON b.id = bs.booking_id
            AND b.travel_date = ?
            AND b.status IN ('pending', 'booked')
        WHERE s.bus_id = ?
            AND s.id IN ($seatPlaceholders)
        GROUP BY s.id, s.seat_number, o.status
        HAVING effective_status = 'available'
    ";

    $params = array_merge([$travelDate, $travelDate, $busId], $seatIds);
    $seatStmt = $pdo->prepare($seatCheckSql);
    $seatStmt->execute($params);
    $availableSeats = $seatStmt->fetchAll();

    if (count($availableSeats) !== count($seatIds)) {
        $pdo->rollBack();
        $_SESSION['booking_error'] = 'One or more selected seats are no longer available. Please reload the seat map.';
        header('Location: /booking.php?travel_date=' . urlencode($travelDate) . '&bus_id=' . $busId);
        exit;
    }

    $bookingStmt = $pdo->prepare("
        INSERT INTO bookings (bus_id, travel_date, full_name, phone, pickup_point, drop_location, status, created_at)
        VALUES (:bus_id, :travel_date, :full_name, :phone, :pickup_point, :drop_location, 'pending', NOW())
    ");
    $bookingStmt->execute([
        'bus_id' => $busId,
        'travel_date' => $travelDate,
        'full_name' => $fullName,
        'phone' => $phone,
        'pickup_point' => $pickupPoint,
        'drop_location' => $dropLocation,
    ]);

    $bookingId = (int) $pdo->lastInsertId();
    $linkStmt = $pdo->prepare('INSERT INTO booking_seats (booking_id, seat_id) VALUES (:booking_id, :seat_id)');

    foreach ($seatIds as $seatId) {
        $linkStmt->execute([
            'booking_id' => $bookingId,
            'seat_id' => $seatId,
        ]);
    }

    $pdo->commit();
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['booking_error'] = 'Booking could not be saved. Check the database configuration and try again.';
    header('Location: /booking.php?travel_date=' . urlencode($travelDate) . '&bus_id=' . $busId);
    exit;
}

$seatNumbers = array_column($availableSeats, 'seat_number');
$seatDetails = [];
foreach ($seatNumbers as $seatNumber) {
    $sn = seat_number_normalize((string) $seatNumber);
    $seatDetails[] = $sn . ' (' . seat_label_49($sn) . ')';
}

$messageLines = [
    'New Booking Request',
    '',
    'Name: ' . $fullName,
    'Phone: ' . $phone,
    'Date: ' . $travelDate,
    'Bus: ' . $bus['name'] . ' (' . $bus['bus_number'] . ')',
];

$routeLabel = get_bus_route_label($bus);
if ($routeLabel !== '') {
    $messageLines[] = 'Route: ' . $routeLabel;
}

$messageLines = array_merge($messageLines, [
    'Seats:',
]);

foreach ($seatDetails as $detail) {
    $messageLines[] = '- ' . $detail;
}

$messageLines = array_merge($messageLines, [
    'Pickup: ' . $pickupPoint,
    'Drop: ' . $dropLocation,
    'Status: Pending',
]);

$message = implode("\n", $messageLines);

unset($_SESSION['booking_old']);

$whatsAppNumber = preg_replace('/\D+/', '', ADMIN_WHATSAPP_NUMBER) ?? '';
$whatsAppUrl = 'https://api.whatsapp.com/send?phone=' . $whatsAppNumber . '&text=' . rawurlencode($message);
header('Location: ' . $whatsAppUrl);
exit;
