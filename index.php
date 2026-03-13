<?php

declare(strict_types=1);

$pageTitle = 'Voyara';

require_once __DIR__ . '/includes/header.php';
?>
<section class="home-hero hero-image hero-split">
    <div class="hero-copy">
        <p class="eyebrow">Voyara bus booking</p>
        <h1>Seat booking that stays simple.</h1>
        <p class="lead">See live seat availability, pick seats by date, and send your request directly to admin through WhatsApp.</p>
        <div class="hero-badges">
            <span class="pill">10+ bookings</span>
            <span class="pill">Fast and smooth</span>
            <span class="pill">No login needed</span>
        </div>
        <div class="hero-actions">
            <a class="button" href="/booking.php">Book Now</a>
            <a class="button secondary" href="/about.php">About Us</a>
        </div>
    </div>
    <div class="hero-media" aria-hidden="true">
        <div class="hero-media-card">
            <small>Live seats</small>
            <strong>Available</strong>
        </div>
        <div class="hero-media-card secondary">
            <small>WhatsApp</small>
            <strong>Instant request</strong>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="hero-card feature-panel">
        <div class="hero-visual">
            <div class="hero-visual-header">
                <span class="route-pill">Next departure</span>
                <span class="pill">Colombo → Kandy</span>
            </div>
            <div class="hero-visual-body">
                <div>
                    <small>Seats available</small>
                    <strong>38 / 49</strong>
                </div>
                <div>
                    <small>Bus type</small>
                    <strong>Luxury</strong>
                </div>
                <div>
                    <small>Pickup</small>
                    <strong>8:00 AM</strong>
                </div>
            </div>
        </div>
        <div class="hero-steps">
            <article>
                <span>01</span>
                <p>Select travel date</p>
            </article>
            <article>
                <span>02</span>
                <p>Choose seats</p>
            </article>
            <article>
                <span>03</span>
                <p>Send booking</p>
            </article>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="section-heading">
        <p class="eyebrow">Why Voyara</p>
        <h2>Designed for fast, low-friction bookings.</h2>
        <p class="lead">Passengers book without accounts while the operator keeps manual control of payments.</p>
    </div>
    <div class="cards-grid three-up">
        <article class="info-card">
            <h3>Live availability</h3>
            <p>Seats update by travel date with clear status colors.</p>
        </article>
        <article class="info-card">
            <h3>Quick booking</h3>
            <p>No passenger account required to reserve seats.</p>
        </article>
        <article class="info-card">
            <h3>Operator control</h3>
            <p>Admins decide when to mark seats as pending or booked.</p>
        </article>
    </div>
</section>

<section class="section-block cta-strip">
    <div>
        <p class="eyebrow">Start now</p>
        <h2>Open the booking page and reserve your seat.</h2>
    </div>
    <a class="button" href="/booking.php">Open Booking</a>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
