<?php
/**
 * Auth.php — Centralized Session & Authentication Helpers
 * 
 * Provides session management and role-based authentication guards.
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

/**
 * Start a session if not already active
 */
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
}

/**
 * Require admin authentication
 * Redirects to admin login if not authenticated
 */
function require_admin(): void {
    init_session();
    
    if (empty($_SESSION['user_id']) || empty($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: ../admin.html');
        exit;
    }
}

/**
 * Require resident authentication
 * Redirects to resident login if not authenticated
 */
function require_resident(): void {
    init_session();
    
    if (empty($_SESSION['r_id'])) {
        header('Location: ../login.html');
        exit;
    }
}

/**
 * Get the current admin user ID
 */
function get_admin_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get the current resident user ID
 */
function get_resident_id(): ?int {
    return isset($_SESSION['r_id']) ? (int)$_SESSION['r_id'] : null;
}

/**
 * Get the current admin user type
 */
function get_admin_type(): ?string {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Get the current resident name
 */
function get_resident_name(): ?string {
    return $_SESSION['r_fname'] ?? null;
}

/**
 * Check if user is logged in as admin
 */
function is_admin(): bool {
    return !empty($_SESSION['user_id']) && ($_SESSION['user_type'] ?? '') === 'admin';
}

/**
 * Check if user is logged in as resident
 */
function is_resident(): bool {
    return !empty($_SESSION['r_id']);
}

/**
 * Logout the current user
 */
function logout(string $redirect = '../login.html'): void {
    init_session();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
    header("Location: $redirect");
    exit;
}