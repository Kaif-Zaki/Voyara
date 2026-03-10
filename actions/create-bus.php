<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/buses.php');
    exit;
}

$name = request_value('name');
$busNumber = request_value('bus_number');
$origin = request_value('origin');
$destination = request_value('destination');
$busType = request_value('bus_type');
$description = request_value('description');
$imageUrl = request_value('image_url');
$isActive = request_value('is_active');
$startTime = request_value('start_time');
$endTime = request_value('end_time');
$stopsRaw = request_value('stops');

if ($name === '' || $busNumber === '') {
    header('Location: /admin/buses.php');
    exit;
}

$stops = preg_split('/\r\n|\r|\n/', $stopsRaw) ?: [];
$normalizedStops = [];
foreach ($stops as $stop) {
    $stop = trim($stop);
    if ($stop === '') {
        continue;
    }
    $parts = array_map('trim', explode('|', $stop));
    $name = $parts[0] ?? '';
    if ($name === '') {
        continue;
    }
    $offset = 0;
    if (isset($parts[1]) && $parts[1] !== '') {
        $offset = max(0, (int) $parts[1]);
    }
    if (array_key_exists($name, $normalizedStops)) {
        continue;
    }
    $normalizedStops[$name] = $offset;
}

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('
        INSERT INTO buses (name, bus_number, total_seats, origin, destination, bus_type, description, image_url, start_time, end_time, is_active)
        VALUES (:name, :bus_number, :total_seats, :origin, :destination, :bus_type, :description, :image_url, :start_time, :end_time, :is_active)
    ');
    $stmt->execute([
        'name' => $name,
        'bus_number' => $busNumber,
        'total_seats' => 49,
        'origin' => $origin !== '' ? $origin : null,
        'destination' => $destination !== '' ? $destination : null,
        'bus_type' => $busType !== '' ? $busType : 'Normal',
        'description' => $description !== '' ? $description : null,
        'image_url' => $imageUrl !== '' ? $imageUrl : null,
        'start_time' => $startTime !== '' ? $startTime : null,
        'end_time' => $endTime !== '' ? $endTime : null,
        'is_active' => $isActive === '1' ? 1 : 0,
    ]);

    $busId = (int) $pdo->lastInsertId();

    if ($normalizedStops !== []) {
        $insertStop = $pdo->prepare('INSERT INTO bus_stops (bus_id, stop_name, stop_offset_minutes, sort_order) VALUES (:bus_id, :stop_name, :stop_offset_minutes, :sort_order)');
        $index = 0;
        foreach ($normalizedStops as $stopName => $offset) {
            $index++;
            $insertStop->execute([
                'bus_id' => $busId,
                'stop_name' => $stopName,
                'stop_offset_minutes' => $offset,
                'sort_order' => $index,
            ]);
        }
    }

    $insertSeat = $pdo->prepare('INSERT INTO seats (bus_id, seat_number) VALUES (:bus_id, :seat_number)');
    for ($i = 1; $i <= 49; $i++) {
        $insertSeat->execute([
            'bus_id' => $busId,
            'seat_number' => str_pad((string) $i, 2, '0', STR_PAD_LEFT),
        ]);
    }

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

header('Location: /admin/buses.php');
exit;
