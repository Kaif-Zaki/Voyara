<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
// const DB_HOST = '82.25.121.136';

const DB_PORT = '3306';

const DB_NAME = 'bus_room_schedule';
// const DB_NAME = 'u41473066O_zaky';

const DB_USER = 'root';
// const DB_USER = 'u41473066O_zaky';

const DB_PASS = 'kaifzakey@IJSE';
// const DB_PASS = 'Zaky2026';


// Use WhatsApp number in international format without +, spaces, or leading zero.
// Example for Sri Lanka: 94788385004
const ADMIN_WHATSAPP_NUMBER = '94788385004';
const APP_NAME = 'Voyara';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
