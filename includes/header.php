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
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_PATH ?>assets/images/PageLogo.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_PATH ?>assets/images/SiteLogo.png">
    <link rel="stylesheet" href="<?= BASE_PATH ?>assets/css/style.css">
</head>
<body class="<?= h($bodyClass) ?>">
<header class="site-header">
    <div class="container header-row">
        <a class="brand" href="/index.php" aria-label="Voyara home">
            <img src="<?= BASE_PATH ?>assets/images/SiteLogo.png" alt="Voyara logo">
        </a>
        <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
        </button>
        <?php if ($isAdminArea): ?>
            <nav class="top-nav admin-nav" id="mobileNav">
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
            </nav>
        <?php else: ?>
            <nav class="top-nav" id="mobileNav">
                <a class="<?= is_active_path('/index.php') ? 'active' : '' ?>" href="/index.php">Home</a>
                <a class="<?= is_active_path('/booking.php') ? 'active' : '' ?>" href="/booking.php">Book Now</a>
                <a class="<?= is_active_path('/contact.php') ? 'active' : '' ?>" href="/contact.php">Contact</a>
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                    <span class="theme-toggle-icon" data-theme-icon="sun" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm0-16a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1Zm0 18a1 1 0 0 1 1 1v0a1 1 0 1 1-2 0 1 1 0 0 1 1-1Zm10-8a1 1 0 0 1-1 1h-2a1 1 0 1 1 0-2h2a1 1 0 0 1 1 1ZM5 12a1 1 0 0 1-1 1H2a1 1 0 1 1 0-2h2a1 1 0 0 1 1 1Zm13.66 6.66a1 1 0 0 1-1.42 1.42l-1.42-1.42a1 1 0 0 1 1.42-1.42l1.42 1.42Zm-12.48-12.5a1 1 0 0 1-1.42 0L3.34 4.74a1 1 0 1 1 1.42-1.42l1.42 1.42a1 1 0 0 1 0 1.42Zm12.48-1.42a1 1 0 0 1 0 1.42l-1.42 1.42a1 1 0 1 1-1.42-1.42l1.42-1.42a1 1 0 0 1 1.42 0ZM6.76 19.66a1 1 0 0 1-1.42 0l-1.42-1.42a1 1 0 1 1 1.42-1.42l1.42 1.42a1 1 0 0 1 0 1.42Z"/>
                        </svg>
                    </span>
                    <span class="theme-toggle-icon" data-theme-icon="moon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M21 14.5A8.5 8.5 0 0 1 9.5 3a1 1 0 0 0-1.26 1.26A6.5 6.5 0 1 0 19.74 15.76 1 1 0 0 0 21 14.5Z"/>
                        </svg>
                    </span>
                </button>
                <a class="nav-cta" href="/admin/login.php">Admin</a>
            </nav>
        <?php endif; ?>
    </div>
    <div class="nav-backdrop" data-nav-backdrop aria-hidden="true"></div>
</header>
<main class="container page-content">
