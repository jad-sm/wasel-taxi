<?php
session_start();
require_once __DIR__ . '/db.php';

function current_user() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    global $mysqli;
    $stmt = $mysqli->prepare('SELECT id, full_name, email, phone FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function generate_ride_code() {
    return 'WSL-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}
