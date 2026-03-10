<?php

declare(strict_types=1);

$isAdminArea = $isAdminArea ?? false;
?>
</main>
<?php if (!$isAdminArea): ?>
    <footer class="site-footer">
        <div class="footer2">
            <div class="container footer2-inner">
                <div class="footer2-top">
                    <section class="footer2-col">
                        <h3>About Us</h3>
                        <p class="footer2-text">Voyara helps passengers reserve seats by date and send booking requests to admin through WhatsApp. Payments are confirmed manually by the operator.</p>
                        <div class="footer2-contact">
                            <div class="footer2-contact-item">
                                <span class="footer2-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18">
                                        <path fill="currentColor" d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.07 21 3 13.93 3 5a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.24 1.01l-2.2 2.2Z"/>
                                    </svg>
                                </span>
                                <span>+94 77 000 0000</span>
                            </div>
                            <div class="footer2-contact-item">
                                <span class="footer2-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18">
                                        <path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/>
                                    </svg>
                                </span>
                                <span>hello@voyara.lk</span>
                            </div>
                        </div>
                        <form class="footer2-newsletter" action="#" method="post" onsubmit="return false">
                            <label class="sr-only" for="newsletterEmail">Email</label>
                            <input id="newsletterEmail" type="email" placeholder="Enter your e-mail" autocomplete="email">
                            <button type="submit">Send</button>
                        </form>
                    </section>

                    <section class="footer2-col">
                        <h3>Explore</h3>
                        <nav class="footer2-list" aria-label="Explore links">
                            <a href="/index.php">Home</a>
                            <a href="/booking.php">Book Now</a>
                            <a href="/about.php">About Us</a>
                        </nav>
                    </section>

                    <section class="footer2-col">
                        <h3>Support</h3>
                        <nav class="footer2-list" aria-label="Support links">
                            <a href="/faq.php">FAQ</a>
                            <a href="/contact.php">Contact Us</a>
                            <a href="/admin/login.php">Admin Login</a>
                        </nav>
                    </section>
                </div>

                <div class="footer2-bottom">
                    <nav class="footer2-links" aria-label="Footer links">
                        <a href="/index.php">Home</a>
                        <a href="/about.php">About</a>
                        <a href="/booking.php">Book Now</a>
                        <a href="/faq.php">FAQ</a>
                        <a href="/contact.php">Contact</a>
                    </nav>
                    <a class="footer2-brand" href="/index.php" aria-label="Voyara home">
                        <img src="/assets/images/SiteLogo.png" alt="Voyara logo">
                    </a>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>
</body>
</html>
