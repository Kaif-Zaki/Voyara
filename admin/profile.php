<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$isAdminArea = true;
$pageTitle = 'Admin Profile';

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
$admin = $adminId > 0 ? get_admin_by_id($adminId) : null;

if (!$admin) {
    header('Location: /admin/logout.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = request_value('username');
    $currentPassword = request_value('current_password');
    $newPassword = request_value('new_password');
    $confirmPassword = request_value('confirm_password');

    if ($username === '') {
        $error = 'Username is required.';
    } elseif ($currentPassword === '') {
        $error = 'Current password is required.';
    } elseif (!password_verify($currentPassword, (string) $admin['password_hash'])) {
        $error = 'Current password is incorrect.';
    } elseif ($newPassword !== '' && $newPassword !== $confirmPassword) {
        $error = 'New password and confirmation do not match.';
    } elseif (is_username_taken($username, $adminId)) {
        $error = 'That username is already in use.';
    } else {
        $updated = update_admin_profile($adminId, $username, $newPassword !== '' ? $newPassword : null);
        if ($updated) {
            $_SESSION['admin_username'] = $username;
            $admin = get_admin_by_id($adminId);
            $success = 'Profile updated successfully.';
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel stack-lg">
    <div class="split-row">
        <div>
            <p class="eyebrow">Admin account</p>
            <h1>Admin Profile</h1>
            <p class="lead">Update your username or password.</p>
        </div>
        <div class="inline-links">
            <a class="button secondary" href="/admin/dashboard.php">Back to Dashboard</a>
            <a class="button ghost" href="/admin/logout.php">Logout</a>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert error"><?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success !== ''): ?>
        <div class="alert" style="background: rgba(34, 197, 94, 0.12); color: #15803d; border: 1px solid rgba(34, 197, 94, 0.2);">
            <?= h($success) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="stack-md">
        <div class="grid-two">
            <label>
                <span>Username</span>
                <input type="text" name="username" value="<?= h($admin['username'] ?? '') ?>" required>
            </label>
            <label>
                <span>Current Password</span>
                <input type="password" name="current_password" required>
            </label>
        </div>
        <div class="grid-two">
            <label>
                <span>New Password (optional)</span>
                <input type="password" name="new_password">
            </label>
            <label>
                <span>Confirm New Password</span>
                <input type="password" name="confirm_password">
            </label>
        </div>
        <button type="submit" class="button">Save Changes</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
