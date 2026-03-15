<?php

declare(strict_types=1);




// Use WhatsApp number in international format without +, spaces, or leading zero.
// Example for Sri Lanka: 94788385004
const ADMIN_WHATSAPP_NUMBER = '94788385004';
const APP_NAME = 'Voyara';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_PATH')) {
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string) $_SERVER['DOCUMENT_ROOT']) : false;
    $appRoot = realpath(__DIR__ . '/..');
    $basePath = '/';

    if ($docRoot && $appRoot && substr($appRoot, 0, strlen($docRoot)) === $docRoot) {
        $relative = trim(str_replace('\\', '/', substr($appRoot, strlen($docRoot))), '/');
        $basePath = $relative === '' ? '/' : '/' . $relative . '/';
    }

    define('BASE_PATH', $basePath);
}
