<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/buses.php');
    exit;
}

$busId = (int) request_value('bus_id');
$name = request_value('name');
$busNumber = request_value('bus_number');
$origin = request_value('origin');
$destination = request_value('destination');
$busType = request_value('bus_type');
$description = request_value('description');
$imageUrl = request_value('image_url');
$isActive = request_value('is_active');
$stopsRaw = request_value('stops');

if ($busId <= 0 || $name === '' || $busNumber === '') {
    header('Location: /admin/buses.php');
    exit;
}

$stops = preg_split('/\r\n|\r|\n/', $stopsRaw) ?: [];
$normalizedStops = [];
foreach ($stops as $stop) {
    $stop = trim($stop);
    if ($stop === '' || in_array($stop, $normalizedStops, true)) {
        continue;
    }
    $normalizedStops[] = $stop;
}

$pdo = db();
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare('
        UPDATE buses
        SET name = :name,
            bus_number = :bus_number,
            origin = :origin,
            destination = :destination,
            bus_type = :bus_type,
            description = :description,
            image_url = :image_url,
            is_active = :is_active
        WHERE id = :id
    ');
    $stmt->execute([
        'name' => $name,
        'bus_number' => $busNumber,
        'origin' => $origin !== '' ? $origin : null,
        'destination' => $destination !== '' ? $destination : null,
        'bus_type' => $busType !== '' ? $busType : 'Normal',
        'description' => $description !== '' ? $description : null,
        'image_url' => $imageUrl !== '' ? $imageUrl : null,
        'is_active' => $isActive === '1' ? 1 : 0,
        'id' => $busId,
    ]);

    $pdo->prepare('DELETE FROM bus_stops WHERE bus_id = :bus_id')
        ->execute(['bus_id' => $busId]);

    if ($normalizedStops !== []) {
        $insert = $pdo->prepare('INSERT INTO bus_stops (bus_id, stop_name, sort_order) VALUES (:bus_id, :stop_name, :sort_order)');
        foreach ($normalizedStops as $index => $stop) {
            $insert->execute([
                'bus_id' => $busId,
                'stop_name' => $stop,
                'sort_order' => $index + 1,
            ]);
        }
    }

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

header('Location: /admin/buses.php');
exit;
