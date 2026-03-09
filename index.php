<?php

declare(strict_types=1);

$pageTitle = 'Voyara';
require_once __DIR__ . '/includes/header.php';
?>
<section class="home-hero">
    <div class="hero-copy">
        <p class="eyebrow">Voyara</p>
        <h1>Book bus seats with a cleaner flow.</h1>
        <p class="lead">Check availability by date, choose your seats, and send the request to admin through WhatsApp.</p>
        <div class="hero-actions">
            <a class="button" href="/booking.php">Book Now</a>
            <a class="button secondary" href="/about.php">About Us</a>
        </div>
        <div class="mini-stats">
            <div>
                <strong>2</strong>
                <span>Buses</span>
            </div>
            <div>
                <strong>40</strong>
                <span>Seats each</span>
            </div>
            <div>
                <strong>0</strong>
                <span>Login needed</span>
            </div>
        </div>
    </div>

    <div class="hero-card feature-panel">
        <div class="route-pill">3-step booking</div>
        <div class="hero-steps">
            <article>
                <span>01</span>
                <p>Select date</p>
            </article>
            <article>
                <span>02</span>
                <p>Pick seats</p>
            </article>
            <article>
                <span>03</span>
                <p>Send on WhatsApp</p>
            </article>
        </div>
        <div class="hero-highlight">
            <div>
                <small>Status</small>
                <strong>Live seat view</strong>
            </div>
            <div>
                <small>Admin</small>
                <strong>Manual confirmation</strong>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
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
            <h3>Flexible admin flow</h3>
            <p>Payments and confirmation stay under manual control.</p>
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
