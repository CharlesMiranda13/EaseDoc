<?php
// Disable caching and start output buffering
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
ob_start();

// Use realpath to ensure correct path resolution
$corePath = realpath(__DIR__ . '/../../core');
$backendPath = realpath(__DIR__ . '/../');

require_once $corePath . '/Response.php';
require_once $corePath . '/Csrf.php';
require_once $backendPath . '/class.php';

$db = new global_class();

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestType = $_POST['requestType'] ?? $_GET['requestType'] ?? '';

if ($requestMethod === 'POST' || $requestMethod === 'GET') {
    if (!empty($requestType)) {
        switch ($requestType) {
            case 'LoginResident': {
                if ($requestMethod !== 'POST') {
                    json_error('Method not allowed.', 405);
                }
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    json_error('Please enter both email address and password.', 422);
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    json_error('Please enter a valid email address.', 422);
                }
                
                $rawResponse = $db->LoginResident($email, $password);
                $decoded = json_decode($rawResponse, true);
                if (isset($decoded['code']) && $decoded['code'] === 429) {
                    http_response_code(429);
                }
                echo $rawResponse;
                break;
            }
            
            case 'LoginAdmin': {
                if ($requestMethod !== 'POST') {
                    json_error('Method not allowed.', 405);
                }
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    json_error('Please enter both email address and password.', 422);
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    json_error('Please enter a valid email address.', 422);
                }
                
                $rawResponse = $db->LoginAdmin($email, $password);
                $decoded = json_decode($rawResponse, true);
                if (isset($decoded['code']) && $decoded['code'] === 429) {
                    http_response_code(429);
                }
                echo $rawResponse;
                break;
            }

            case 'TrackDocument': {
                $trackingCode = trim($_POST['tracking_code'] ?? $_GET['tracking_code'] ?? '');
                if (empty($trackingCode)) {
                    json_error('Please enter a document tracking code.', 422);
                }
                $response = $db->TrackDocument($trackingCode);
                echo $response;
                break;
            }

            case 'GetPublicAnnouncements': {
                $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : (isset($_GET['limit']) ? (int)$_GET['limit'] : 6);
                $response = $db->GetPublicAnnouncements($limit);
                echo $response;
                break;
            }
            
            default: {
                // If it requires CSRF token verification
                $csrfToken = $_POST['csrf_token'] ?? '';
                if (!validate_csrf($csrfToken)) {
                    json_error('Invalid or expired CSRF token.', 403);
                }
                
                json_error('Invalid request type.', 400);
            }
        }
    } else {
        json_error('Access Denied! No Request Type specified.', 400);
    }
} else {
    json_error('Method not allowed.', 405);
}

// Clean any output buffer and send only the JSON response
ob_end_flush();
