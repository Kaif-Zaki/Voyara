<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_path(): string
{
    return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
}

function is_active_path(string $path): bool
{
    return current_path() === $path;
}

function request_value(string $key, string|int|float|bool $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $_GET[$key] ?? $default));
}

function selected_date_or_today(?string $value = null): string
{
    $date = $value ?: date('Y-m-d');
    $parsed = DateTime::createFromFormat('Y-m-d', $date);

    return $parsed && $parsed->format('Y-m-d') === $date ? $date : date('Y-m-d');
}

function get_buses(): array
{
    return db()->query('SELECT id, name, bus_number, total_seats, origin, destination, bus_type, description, image_url, is_active FROM buses ORDER BY id ASC')->fetchAll();
}

function get_active_buses(): array
{
    $stmt = db()->prepare('SELECT id, name, bus_number, total_seats, origin, destination, bus_type, description, image_url, is_active FROM buses WHERE is_active = 1 ORDER BY id ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_bus_by_id(int $busId): ?array
{
    $stmt = db()->prepare('SELECT id, name, bus_number, total_seats, origin, destination, bus_type, description, image_url, is_active FROM buses WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $busId]);
    $bus = $stmt->fetch();

    return $bus ?: null;
}

function bus_types(): array
{
    return ['Normal', 'Semi-Luxury', 'Luxury', 'Double Decker'];
}

function get_bus_stops(int $busId): array
{
    $stmt = db()->prepare('SELECT stop_name FROM bus_stops WHERE bus_id = :bus_id ORDER BY sort_order ASC, id ASC');
    $stmt->execute(['bus_id' => $busId]);
    return array_map(static fn ($row) => $row['stop_name'], $stmt->fetchAll());
}

function get_bus_route_label(array $bus): string
{
    $origin = trim((string) ($bus['origin'] ?? ''));
    $destination = trim((string) ($bus['destination'] ?? ''));
    if ($origin === '' && $destination === '') {
        return '';
    }
    if ($origin !== '' && $destination !== '') {
        return $origin . ' → ' . $destination;
    }
    return $origin !== '' ? $origin : $destination;
}

function get_bus_seats_with_status(int $busId, string $travelDate): array
{
    $sql = "
        SELECT
            s.id,
            s.seat_number,
            CASE
                WHEN o.status IS NOT NULL THEN o.status
                WHEN MAX(
                    CASE b.status
                        WHEN 'booked' THEN 2
                        WHEN 'pending' THEN 1
                        ELSE 0
                    END
                ) = 2 THEN 'booked'
                WHEN MAX(
                    CASE b.status
                        WHEN 'booked' THEN 2
                        WHEN 'pending' THEN 1
                        ELSE 0
                    END
                ) = 1 THEN 'pending'
                ELSE 'available'
            END AS seat_status
        FROM seats s
        LEFT JOIN seat_status_overrides o
            ON o.seat_id = s.id
            AND o.travel_date = :travel_date
        LEFT JOIN booking_seats bs ON bs.seat_id = s.id
        LEFT JOIN bookings b ON b.id = bs.booking_id
            AND b.travel_date = :travel_date
            AND b.status IN ('pending', 'booked')
        WHERE s.bus_id = :bus_id
        GROUP BY s.id, s.seat_number, o.status
        ORDER BY s.id ASC
    ";

    $stmt = db()->prepare($sql);
    $stmt->execute([
        'bus_id' => $busId,
        'travel_date' => $travelDate,
    ]);

    return $stmt->fetchAll();
}

function seat_number_normalize(string|int $seatNumber): string
{
    $n = (int) $seatNumber;
    if ($n <= 0) {
        return '';
    }

    return str_pad((string) $n, 2, '0', STR_PAD_LEFT);
}

function seat_layout_49(): array
{
    $rows = [];
    $seat = 1;

    // 11 standard rows of 2+2 seats (4 seats per row) = 44 seats
    for ($row = 1; $row <= 11; $row++) {
        $rows[] = [
            'type' => 'standard',
            'slots' => [
                seat_number_normalize($seat++), // left window
                seat_number_normalize($seat++), // left aisle
                null,                            // aisle gap
                seat_number_normalize($seat++), // right aisle
                seat_number_normalize($seat++), // right window
            ],
        ];
    }

    // Back bench row with 5 seats = 49 seats total
    $rows[] = [
        'type' => 'back',
        'slots' => [
            seat_number_normalize($seat++),
            seat_number_normalize($seat++),
            seat_number_normalize($seat++),
            seat_number_normalize($seat++),
            seat_number_normalize($seat++),
        ],
    ];

    return $rows;
}

function seat_attributes_49(string $seatNumber): array
{
    $n = (int) $seatNumber;

    $zone = 'Middle';
    if ($n >= 1 && $n <= 12) {
        $zone = 'Near driver';
    } elseif ($n >= 33) {
        $zone = 'Back';
    }

    if ($n >= 45 && $n <= 49) {
        $side = match ($n - 44) {
            1 => 'Left window',
            2 => 'Left aisle',
            3 => 'Middle',
            4 => 'Right aisle',
            5 => 'Right window',
            default => 'Middle',
        };

        return [
            'zone' => 'Back',
            'side' => $side,
            'row' => 12,
            'is_back_bench' => true,
        ];
    }

    if ($n < 1 || $n > 44) {
        return [
            'zone' => $zone,
            'side' => 'Unknown',
            'row' => 0,
            'is_back_bench' => false,
        ];
    }

    $row = (int) ceil($n / 4);
    $posInRow = (($n - 1) % 4) + 1;
    $side = match ($posInRow) {
        1 => 'Left window',
        2 => 'Left aisle',
        3 => 'Right aisle',
        4 => 'Right window',
        default => 'Unknown',
    };

    return [
        'zone' => $zone,
        'side' => $side,
        'row' => $row,
        'is_back_bench' => false,
    ];
}

function seat_label_49(string $seatNumber): string
{
    $seatNumber = seat_number_normalize($seatNumber);
    if ($seatNumber === '') {
        return 'Unknown';
    }

    $attr = seat_attributes_49($seatNumber);
    $pieces = array_filter([$attr['zone'] ?? null, $attr['side'] ?? null]);

    return implode(' - ', $pieces);
}

function seats_by_number(array $seatsWithStatus): array
{
    $byNumber = [];
    foreach ($seatsWithStatus as $seat) {
        $raw = (string) $seat['seat_number'];
        $byNumber[$raw] = $seat;
        $normalized = seat_number_normalize($raw);
        if ($normalized !== '') {
            $byNumber[$normalized] = $seat;
        }
    }
    return $byNumber;
}

function get_date_bookings(string $travelDate): array
{
    $sql = "
        SELECT
            b.id,
            b.travel_date,
            b.full_name,
            b.phone,
            b.pickup_point,
            b.drop_location,
            b.status,
            b.created_at,
            buses.name AS bus_name,
            buses.origin AS bus_origin,
            buses.destination AS bus_destination,
            GROUP_CONCAT(s.seat_number ORDER BY s.id SEPARATOR ', ') AS seat_numbers
        FROM bookings b
        INNER JOIN buses ON buses.id = b.bus_id
        INNER JOIN booking_seats bs ON bs.booking_id = b.id
        INNER JOIN seats s ON s.id = bs.seat_id
        WHERE b.travel_date = :travel_date
        GROUP BY b.id
        ORDER BY b.created_at DESC
    ";

    $stmt = db()->prepare($sql);
    $stmt->execute(['travel_date' => $travelDate]);

    return $stmt->fetchAll();
}

function get_booking_by_id(int $bookingId): ?array
{
    $stmt = db()->prepare('SELECT id, status FROM bookings WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $bookingId]);
    $booking = $stmt->fetch();

    return $booking ?: null;
}

function status_label(string $status): string
{
    return match ($status) {
        'booked' => 'Booked',
        'pending' => 'Pending',
        'available' => 'Available',
        default => 'Available',
    };
}
