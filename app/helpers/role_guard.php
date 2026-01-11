<?php
// ✅ 1. ALWAYS start the session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ 2. Helper to check if user is logged in
function isAuthenticated() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

// ✅ 3. Role must match exactly
function requireRole($role) {
    if (!isAuthenticated() || $_SESSION['user']['role'] !== $role) {
        header("Location: /auth/login"); // ✅ use correct login path
        exit;
    }
}

// ✅ 4. Accept multiple allowed roles
function requireAnyRole($roles = []) {
    if (!isAuthenticated() || !in_array($_SESSION['user']['role'], $roles)) {
        header("Location: /auth/login");
        exit;
    }
}

// ✅ 5. Scorer assignment check
function isScorerForMatch($fixture_id, $user_id) {
    $db = new Database(); // ✅ ensure you're using your actual DB class
    $sql = "SELECT * FROM assignments WHERE fixture_id = :fixture_id AND user_id = :user_id AND role = 'scorer'";
    $db->query($sql);
    $db->bind(':fixture_id', $fixture_id);
    $db->bind(':user_id', $user_id);
    return $db->single() !== false;
}
