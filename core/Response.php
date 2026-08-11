<?php
/**
 * Response.php — Standardized JSON API Responses
 * 
 * Provides consistent response format for all API endpoints.
 * Usage: json_response('success', 'Message', ['data' => $data]);
 */

/**
 * Send a standardized JSON response
 * 
 * @param string $status 'success' or 'error'
 * @param string $message Human-readable message
 * @param array $data Additional data to include
 * @param int $httpCode HTTP status code (200 for success, 400/403/500 for errors)
 */
function json_response(string $status, string $message, array $data = [], int $httpCode = 200): void {
    http_response_code($httpCode);
    header('Content-Type: application/json');
    
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send a success response
 */
function json_success(string $message, array $data = []): void {
    json_response('success', $message, $data, 200);
}

/**
 * Send an error response
 */
function json_error(string $message, int $httpCode = 400, array $data = []): void {
    json_response('error', $message, $data, $httpCode);
}

/**
 * Send a validation error response
 */
function json_validation_error(string $message, array $errors = []): void {
    json_response('error', $message, ['errors' => $errors], 422);
}