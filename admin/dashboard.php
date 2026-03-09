<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_admin();

header('Location: /admin/bookings.php');
exit;
