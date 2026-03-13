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

$stopNames = $_POST['stop_names'] ?? [];
$stopTimes = $_POST['stop_times'] ?? [];
if (is_array($stopNames) && is_array($stopTimes)) {
    $lines = [];
    foreach ($stopNames as $index => $stopName) {
        $stopName = trim((string) $stopName);
        if ($stopName === '') {
            continue;
        }
        $stopTime = trim((string) ($stopTimes[$index] ?? ''));
        $lines[] = $stopTime !== '' ? $stopName . ' | ' . $stopTime : $stopName;
    }
    if ($lines !== []) {
        $stopsRaw = implode("\n", $lines);
    }
}

$uploadedImage = store_bus_image($_FILES['image_file'] ?? []);
if ($uploadedImage !== null) {
    $imageUrl = $uploadedImage;
}

if ($name === '' || $busNumber === '') {
    header('Location: /admin/buses.php');
    exit;
}

$stopEntries = parse_bus_stop_lines($stopsRaw, $startTime !== '' ? $startTime : null);

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

    if ($stopEntries !== []) {
        $insertStop = $pdo->prepare('INSERT INTO bus_stops (bus_id, stop_name, stop_offset_minutes, stop_time, sort_order) VALUES (:bus_id, :stop_name, :stop_offset_minutes, :stop_time, :sort_order)');
        $index = 0;
        foreach ($stopEntries as $entry) {
            $index++;
            $insertStop->execute([
                'bus_id' => $busId,
                'stop_name' => $entry['name'],
                'stop_offset_minutes' => $entry['offset'],
                'stop_time' => $entry['stop_time'],
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
