<?php
function isLoggedIn() {
    return isset($_SESSION['user']['id']);
}

function requireLogin() {
    
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    if (!isLoggedIn()) {
        header("Location: /auth/login");
        exit;
    }
}
