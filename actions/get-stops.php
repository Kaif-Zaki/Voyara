<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$busId = (int) request_value('bus_id');
$stops = [];
$route = '';
$bus = null;

if ($busId > 0) {
    $bus = get_bus_by_id($busId);
    if ($bus) {
        $route = get_bus_route_label($bus);
    }
    $stops = get_bus_stops($busId);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'stops' => $stops,
    'route' => $route,
    'bus' => $bus,
], JSON_UNESCAPED_UNICODE);
