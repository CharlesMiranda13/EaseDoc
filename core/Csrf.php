<?php
/**
 * Csrf.php — CSRF Token Generation & Validation
 * 
 * Provides CSRF protection for all form submissions.
 * Include this file in forms and validate in controllers.
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

/**
 * Generate or retrieve CSRF token
 */
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF hidden field for forms
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate CSRF token
 * 
 * @param string $token The token to validate
 * @return bool True if valid, false otherwise
 */
function validate_csrf(string $token): bool {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require valid CSRF token or exit with error
 * 
 * @param string $token The token from $_POST
 * @return void
 */
function require_csrf(string $token): void {
    if (!validate_csrf($token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or expired CSRF token'
        ]);
        exit;
    }
}