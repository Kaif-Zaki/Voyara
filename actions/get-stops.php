<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$busId = (int) request_value('bus_id');
$stops = [];
$route = '';
$bus = null;
$stopOffsets = [];
$stopTimes = [];

if ($busId > 0) {
    $bus = get_bus_by_id($busId);
    if ($bus) {
        $route = get_bus_route_label($bus);
    }
    $stopRows = get_bus_stops_with_offsets($busId);
    $startTime = $bus['start_time'] ?? '';
    foreach ($stopRows as $row) {
        $stops[] = $row['stop_name'];
        $stopTime = $row['stop_time'] ?? '';
        if ($stopTime !== '') {
            $stopTimes[$row['stop_name']] = $stopTime;
        }
        $offset = (int) ($row['stop_offset_minutes'] ?? 0);
        if ($stopTime !== '') {
            $computed = compute_offset_minutes($startTime, $stopTime);
            if ($computed !== null) {
                $offset = $computed;
            }
        }
        $stopOffsets[$row['stop_name']] = $offset;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'stops' => $stops,
    'route' => $route,
    'stop_offsets' => $stopOffsets,
    'stop_times' => $stopTimes,
    'bus' => $bus,
], JSON_UNESCAPED_UNICODE);
