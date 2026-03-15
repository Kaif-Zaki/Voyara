<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/buses.php');
    exit;
}

$errors = [];
$busId = (int) request_value('bus_id');
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

$name = validate_required($name, 'Bus name', $errors, 120);
$busNumber = validate_required($busNumber, 'Bus number', $errors, 60);
$origin = validate_optional_text($origin, 'Origin', $errors, 120);
$destination = validate_optional_text($destination, 'Destination', $errors, 120);
$busType = validate_bus_type($busType, $errors);
$description = validate_optional_text($description, 'Description', $errors, 600);
$imageUrl = validate_optional_text($imageUrl, 'Image URL', $errors, 500);
$startTime = validate_optional_time($startTime, 'Start time', $errors);
$endTime = validate_optional_time($endTime, 'End time', $errors);

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
} elseif (upload_attempted($_FILES['image_file'] ?? [])) {
    $errors[] = 'Uploaded image must be a valid JPG, PNG, WEBP, GIF, or AVIF file.';
}

if ($busId <= 0) {
    $errors[] = 'Invalid bus selected.';
}

if ($errors !== []) {
    redirect_with_flash('/admin/buses.php', implode(' ', $errors));
}

$stopEntries = parse_bus_stop_lines($stopsRaw, $startTime !== null ? $startTime : null);

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
            start_time = :start_time,
            end_time = :end_time,
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
        'start_time' => $startTime !== null ? $startTime : null,
        'end_time' => $endTime !== null ? $endTime : null,
        'is_active' => $isActive === '1' ? 1 : 0,
        'id' => $busId,
    ]);

    $pdo->prepare('DELETE FROM bus_stops WHERE bus_id = :bus_id')
        ->execute(['bus_id' => $busId]);

    if ($stopEntries !== []) {
        $insert = $pdo->prepare('INSERT INTO bus_stops (bus_id, stop_name, stop_offset_minutes, stop_time, sort_order) VALUES (:bus_id, :stop_name, :stop_offset_minutes, :stop_time, :sort_order)');
        $index = 0;
        foreach ($stopEntries as $entry) {
            $index++;
            try {
                $insert->execute([
                    'bus_id' => $busId,
                    'stop_name' => $entry['name'],
                    'stop_offset_minutes' => $entry['offset'],
                    'stop_time' => $entry['stop_time'],
                    'sort_order' => $index,
                ]);
            } catch (PDOException $exception) {
                if ($exception->getCode() !== '42S22') {
                    throw $exception;
                }
                $fallback = $pdo->prepare('INSERT INTO bus_stops (bus_id, stop_name, stop_offset_minutes, sort_order) VALUES (:bus_id, :stop_name, :stop_offset_minutes, :sort_order)');
                $fallback->execute([
                    'bus_id' => $busId,
                    'stop_name' => $entry['name'],
                    'stop_offset_minutes' => $entry['offset'],
                    'sort_order' => $index,
                ]);
            }
        }
    }

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    redirect_with_flash('/admin/buses.php', 'Failed to update the bus. Please try again.');
}

flash_set('success', 'Bus updated successfully.');
header('Location: /admin/buses.php');
exit;
