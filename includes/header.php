<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? APP_NAME;
$isAdminArea = $isAdminArea ?? false;
$bodyClass = $bodyClass ?? ($isAdminArea ? 'admin-shell' : 'public-shell');
$pendingRequests = $isAdminArea ? get_pending_requests_count() : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/PageLogo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/images/SiteLogo.png">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= h($bodyClass) ?>">
<header class="site-header">
    <div class="container header-row">
        <a class="brand" href="/index.php" aria-label="Voyara home">
            <img src="/assets/images/SiteLogo.png" alt="Voyara logo">
        </a>
        <?php if ($isAdminArea): ?>
            <nav class="top-nav admin-nav">
                <a class="<?= is_active_path('/admin/dashboard.php') ? 'active' : '' ?>" href="/admin/dashboard.php">Admin Panel</a>
                <a class="<?= is_active_path('/admin/seats.php') ? 'active' : '' ?>" href="/admin/seats.php">Seats</a>
                <a class="<?= is_active_path('/admin/buses.php') ? 'active' : '' ?>" href="/admin/buses.php">Buses</a>
                <a class="admin-notify <?= is_active_path('/admin/bookings.php') ? 'active' : '' ?>" href="/admin/bookings.php" aria-label="Booking requests">
                    <span class="admin-notify-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18">
                            <path fill="currentColor" d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 1 0-14 0v5l-2 2v1h18v-1l-2-2Z"/>
                        </svg>
                    </span>
                    <?php if ($pendingRequests > 0): ?>
                        <span class="notify-badge"><?= (int) $pendingRequests ?></span>
                    <?php endif; ?>
                </a>
                <a class="admin-profile <?= is_active_path('/admin/profile.php') ? 'active' : '' ?>" href="/admin/profile.php" aria-label="Admin Profile">
                    <span class="sr-only">Admin Profile</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                        <path fill="currentColor" d="M12 12a4 4 0 1 0-4-4a4 4 0 0 0 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z"/>
                    </svg>
                </a>
            </nav>
        <?php else: ?>
            <nav class="top-nav">
                <a class="<?= is_active_path('/index.php') ? 'active' : '' ?>" href="/index.php">Home</a>
                <a class="<?= is_active_path('/booking.php') ? 'active' : '' ?>" href="/booking.php">Book Now</a>
                <a class="<?= is_active_path('/about.php') ? 'active' : '' ?>" href="/about.php">About</a>
                <a class="<?= is_active_path('/faq.php') ? 'active' : '' ?>" href="/faq.php">FAQ</a>
                <a class="<?= is_active_path('/contact.php') ? 'active' : '' ?>" href="/contact.php">Contact</a>
                <a class="nav-cta" href="/admin/login.php">Admin</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container page-content">
