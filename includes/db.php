<?php
// Database connection settings — edit these to match your server.
define('DB_HOST', 'sql104.infinityfree.com');
define('DB_NAME', 'wasel_taxi');
define('DB_USER', 'if0_42641419');
define('DB_PASS', '5v4L3LCY52');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');
