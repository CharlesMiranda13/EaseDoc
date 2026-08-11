<?php
/**
 * config.php — Centralized environment loader
 * 
 * Reads database credentials from the .env file at the project root.
 * All db.php files across the project should include this file
 * instead of hardcoding credentials directly.
 * 
 * Usage: require_once __DIR__ . '/config.php';
 */

function loadEnv(string $envPath): void
{
    if (!file_exists($envPath)) {
        // Fallback: try the parent directory
        $envPath = dirname($envPath) . '/.env';
        if (!file_exists($envPath)) {
            return; // No .env found; rely on system environment variables
        }
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Only set if not already defined (system env takes priority)
            if (!array_key_exists($key, $_ENV) && !getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Walk up from this file's location to find the project root .env
$projectRoot = dirname(__DIR__); // config/ is one level below root
loadEnv($projectRoot . '/.env');

// Expose as constants for backward compatibility with existing db.php files
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: '');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');
