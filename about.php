<?php

declare(strict_types=1);

$pageTitle = 'About Voyara';
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact">
    <div>
        <p class="eyebrow">About us</p>
        <h1>Simple, reliable seat booking for Sri Lankan routes.</h1>
        <p class="lead">Voyara keeps the passenger journey quick and clear while giving operators full control over confirmation and payment handling.</p>
    </div>
</section>

<section class="section-block">
    <div class="section-heading">
        <p class="eyebrow">Our approach</p>
        <h2>Built for real-world operations.</h2>
        <p class="lead">No accounts, no payment gateways, no confusion. Just a clear flow passengers understand.</p>
    </div>
    <div class="cards-grid two-up">
        <article class="info-card">
            <h2>Passenger-first flow</h2>
            <p>Check seats by date, pick a seat, and send a request through WhatsApp in minutes.</p>
        </article>
        <article class="info-card">
            <h2>Operator control</h2>
            <p>Admins decide when a booking is pending or confirmed after manual payment collection.</p>
        </article>
    </div>
</section>

<section class="section-block">
    <div class="section-heading">
        <p class="eyebrow">Principles</p>
        <h2>Clean for passengers, flexible for operators.</h2>
    </div>
    <div class="cards-grid three-up">
        <article class="info-card">
            <h3>Minimal steps</h3>
            <p>No login required for passengers. Just choose, fill, and send.</p>
        </article>
        <article class="info-card">
            <h3>Human confirmation</h3>
            <p>Payments and final seat confirmation stay under admin control.</p>
        </article>
        <article class="info-card">
            <h3>Clear availability</h3>
            <p>Status colors make it obvious which seats can still be selected.</p>
        </article>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
