<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_admin_logged_in()) {
    header('Location: /admin/bookings.php');
    exit;
}

$error = '';
$isAdminArea = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = request_value('username');
    $password = request_value('password');

    if (!attempt_admin_login($username, $password)) {
        $error = 'Invalid username or password.';
    } else {
        header('Location: /admin/bookings.php');
        exit;
    }
}

$pageTitle = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-card">
    <h1>Admin Login</h1>
    <p class="lead">Only the admin panel requires authentication.</p>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= h($error) ?></div>
    <?php endif; ?>
    <form method="post" class="stack-md">
        <label>
            <span>Username</span>
            <input type="text" name="username" required>
        </label>
        <label>
            <span>Password</span>
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="button">Login</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
