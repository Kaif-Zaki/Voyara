<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$busId = (int) request_value('bus_id');
$stops = [];
$route = '';
$bus = null;
$stopOffsets = [];

if ($busId > 0) {
    $bus = get_bus_by_id($busId);
    if ($bus) {
        $route = get_bus_route_label($bus);
    }
    $stopRows = get_bus_stops_with_offsets($busId);
    foreach ($stopRows as $row) {
        $stops[] = $row['stop_name'];
        $stopOffsets[$row['stop_name']] = (int) ($row['stop_offset_minutes'] ?? 0);
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'stops' => $stops,
    'route' => $route,
    'stop_offsets' => $stopOffsets,
    'bus' => $bus,
], JSON_UNESCAPED_UNICODE);
