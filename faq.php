<?php

declare(strict_types=1);

$pageTitle = 'FAQ';
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact">
    <div>
        <p class="eyebrow">FAQ</p>
        <h1>Common questions before booking with Voyara.</h1>
        <p class="lead">Short answers to the things passengers usually need to know first.</p>
    </div>
</section>

<section class="faq-list">
    <article class="faq-item">
        <h2>Do I need an account to book?</h2>
        <p>No. Voyara lets you select seats and send your request without creating an account.</p>
    </article>
    <article class="faq-item">
        <h2>How do I know whether a seat is free?</h2>
        <p>Each seat is shown with a clear status color: available, pending, or booked.</p>
    </article>
    <article class="faq-item">
        <h2>How is payment handled?</h2>
        <p>Payment is handled manually by the admin after your booking request is submitted.</p>
    </article>
    <article class="faq-item">
        <h2>What happens after I send the booking?</h2>
        <p>Your booking details are sent to the admin through WhatsApp, and the seat remains pending until manually confirmed.</p>
    </article>
    <article class="faq-item">
        <h2>Can I book more than one seat?</h2>
        <p>Yes. You can select multiple available seats before submitting the form.</p>
    </article>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
