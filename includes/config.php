<?php

declare(strict_types=1);

// const DB_HOST = '127.0.0.1';
const DB_HOST = '82.25.121.136';

const DB_PORT = '3306';

// const DB_NAME = 'bus_room_schedule';
const DB_NAME = 'u414730660_zaky';

// const DB_USER = 'root';
const DB_USER = 'u414730660_zaky';

// const DB_PASS = 'kaifzakey@IJSE';
const DB_PASS = 'Zaky2026';


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
