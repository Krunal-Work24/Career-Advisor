<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function requireLogin($redirect = '../login.php') {
    if (!isLoggedIn()) { header("Location: $redirect"); exit; }
}
function requireAdmin($redirect = '../login.php') {
    if (!isAdmin()) { header("Location: $redirect"); exit; }
}
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
function redirect($url) {
    header("Location: $url"); exit;
}
function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
