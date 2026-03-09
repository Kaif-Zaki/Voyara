<?php

declare(strict_types=1);

$isAdminArea = $isAdminArea ?? false;
?>
</main>
<?php if (!$isAdminArea): ?>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand-block">
                <a class="brand footer-brand" href="/index.php" aria-label="Voyara home">
                    <img src="/assets/images/logo.png" alt="Voyara logo">
                </a>
                <p class="muted">Modern bus booking with a simple WhatsApp confirmation flow and date-wise seat management.</p>
            </div>
            <div>
                <h3>Explore</h3>
                <a href="/index.php">Home</a>
                <a href="/booking.php">Book Now</a>
                <a href="/about.php">About Us</a>
            </div>
            <div>
                <h3>Support</h3>
                <a href="/faq.php">FAQ</a>
                <a href="/contact.php">Contact Us</a>
                <a href="/admin/login.php">Admin Login</a>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>© <?= date('Y') ?> Voyara. All rights reserved.</p>
        </div>
    </footer>
<?php endif; ?>
</body>
</html>
