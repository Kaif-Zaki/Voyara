<?php

declare(strict_types=1);

$pageTitle = 'About Voyara';
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact">
    <div>
        <p class="eyebrow">About us</p>
        <h1>Voyara is focused on simple, reliable bus seat reservations.</h1>
        <p class="lead">We keep the passenger journey short and clear while giving operators full manual control over confirmation and payment handling.</p>
    </div>
</section>

<section class="section-block">
    <div class="cards-grid two-up">
        <article class="info-card">
            <h2>What we do</h2>
            <p>Voyara helps passengers find seat availability by date, choose seats, and submit their request through WhatsApp in a familiar flow.</p>
        </article>
        <article class="info-card">
            <h2>Why it works</h2>
            <p>Instead of forcing accounts and online payment gateways, the system supports a practical real-world process where the admin confirms bookings manually.</p>
        </article>
    </div>
</section>

<section class="section-block">
    <div class="section-heading">
        <p class="eyebrow">Principles</p>
        <h2>Clean enough for passengers, flexible enough for operators.</h2>
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
