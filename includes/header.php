<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? APP_NAME;
$isAdminArea = $isAdminArea ?? false;
$bodyClass = $bodyClass ?? ($isAdminArea ? 'admin-shell' : 'public-shell');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= h($bodyClass) ?>">
<header class="site-header">
    <div class="container header-row">
        <a class="brand" href="/index.php" aria-label="Voyara home">
            <img src="/assets/images/logo.png" alt="Voyara logo">
        </a>
        <?php if ($isAdminArea): ?>
            <nav class="top-nav admin-nav">
                <a class="<?= is_active_path('/admin/bookings.php') ? 'active' : '' ?>" href="/admin/bookings.php">Bookings</a>
                <a class="<?= is_active_path('/admin/seats.php') ? 'active' : '' ?>" href="/admin/seats.php">Seats</a>
                <a class="<?= is_active_path('/admin/login.php') ? 'active' : '' ?>" href="/admin/login.php">Admin</a>
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
