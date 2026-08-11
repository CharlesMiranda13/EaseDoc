<?php
require_once __DIR__ . '/db.php';
date_default_timezone_set('Asia/Manila');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Helper: Get Client IP Address securely
     */
    private function getClientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $firstIp = trim($ipList[0]);
            if (filter_var($firstIp, FILTER_VALIDATE_IP)) {
                $ip = $firstIp;
            }
        }
        return preg_replace('/[^0-9a-fA-F:\.]/', '', $ip);
    }

    /**
     * Check if client IP or session is rate-limited for an action
     *
     * @param string $action Action name (e.g., 'login')
     * @param int $maxAttempts Maximum allowed attempts
     * @param int $decaySeconds Lockout duration in seconds (default: 300s / 5 mins)
     * @return array ['allowed' => bool, 'retry_after' => int, 'remaining' => int]
     */
    public function checkRateLimit(string $action = 'login', int $maxAttempts = 5, int $decaySeconds = 300): array
    {
        $ip = $this->getClientIp();
        $cacheDir = sys_get_temp_dir() . '/easedoc_ratelimit';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $key = md5($ip . '_' . $action);
        $file = $cacheDir . '/' . $key . '.json';

        $now = time();
        $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];

        if (file_exists($file)) {
            $content = @file_get_contents($file);
            $parsed = $content ? json_decode($content, true) : null;
            if (is_array($parsed)) {
                $data = $parsed;
            }
        }

        // Check if currently locked
        if (!empty($data['locked_until']) && $data['locked_until'] > $now) {
            $retryAfter = $data['locked_until'] - $now;
            return [
                'allowed' => false,
                'retry_after' => $retryAfter,
                'remaining' => 0
            ];
        }

        // Reset if window has elapsed
        if (($now - $data['first_attempt']) > $decaySeconds) {
            $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];
            @file_put_contents($file, json_encode($data));
        }

        $remaining = max(0, $maxAttempts - $data['attempts']);
        return [
            'allowed' => ($data['attempts'] < $maxAttempts),
            'retry_after' => 0,
            'remaining' => $remaining
        ];
    }

    /**
     * Record a failed attempt for rate limiting
     */
    public function recordFailedAttempt(string $action = 'login', int $maxAttempts = 5, int $decaySeconds = 300): void
    {
        $ip = $this->getClientIp();
        $cacheDir = sys_get_temp_dir() . '/easedoc_ratelimit';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $key = md5($ip . '_' . $action);
        $file = $cacheDir . '/' . $key . '.json';

        $now = time();
        $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];

        if (file_exists($file)) {
            $content = @file_get_contents($file);
            $parsed = $content ? json_decode($content, true) : null;
            if (is_array($parsed)) {
                $data = $parsed;
            }
        }

        if (($now - $data['first_attempt']) > $decaySeconds) {
            $data = ['attempts' => 0, 'first_attempt' => $now, 'locked_until' => 0];
        }

        $data['attempts']++;

        if ($data['attempts'] >= $maxAttempts) {
            $data['locked_until'] = $now + $decaySeconds;
        }

        @file_put_contents($file, json_encode($data));
    }

    /**
     * Reset rate limit upon successful action
     */
    public function resetRateLimit(string $action = 'login'): void
    {
        $ip = $this->getClientIp();
        $cacheDir = sys_get_temp_dir() . '/easedoc_ratelimit';
        $key = md5($ip . '_' . $action);
        $file = $cacheDir . '/' . $key . '.json';
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Resident Login with Rate Limiting & Verification
     */
    public function LoginResident($email, $password)
    {
        $rateLimit = $this->checkRateLimit('login_resident', 5, 300);
        if (!$rateLimit['allowed']) {
            return json_encode([
                "status" => "error",
                "code" => 429,
                "message" => "Too many failed login attempts. Please wait " . ceil($rateLimit['retry_after'] / 60) . " minute(s) before trying again."
            ]);
        }

        $email = trim($email);
        $query = $this->conn->prepare("SELECT * FROM `resident` WHERE `r_email` = ? AND r_status = '1' LIMIT 1");
        if (!$query) {
            return json_encode([
                "status" => "error",
                "message" => "Database error occurred."
            ]);
        }

        $query->bind_param("s", $email);
        
        if ($query->execute()) {
            $result = $query->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify password
                if (!password_verify($password, $user['r_password'])) {
                    $this->recordFailedAttempt('login_resident', 5, 300);
                    return json_encode([
                        "status" => "error",
                        "message" => "Invalid email or password."
                    ]);
                }

                // Reset rate limit on success
                $this->resetRateLimit('login_resident');

                // Regenerate session ID to prevent session fixation attacks
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }

                $_SESSION['r_id'] = (int)$user['r_id'];
                $_SESSION['r_fname'] = htmlspecialchars($user['r_fname']);

                return json_encode([
                    "status" => "success",
                    "message" => "Login successful.",
                    "user" => [
                        "r_id" => (int)$user['r_id'],
                        "r_fname" => $user['r_fname']
                    ]
                ]);
            } else {
                $this->recordFailedAttempt('login_resident', 5, 300);
                return json_encode([
                    "status" => "error",
                    "message" => "Invalid email or password."
                ]);
            }
        } else {
            return json_encode([
                "status" => "error",
                "message" => "Database query failed."
            ]);
        }
    }

    /**
     * Admin / Staff Login with Rate Limiting & Verification
     */
    public function LoginAdmin($email, $password)
    {
        $rateLimit = $this->checkRateLimit('login_admin', 5, 300);
        if (!$rateLimit['allowed']) {
            return json_encode([
                "status" => "error",
                "code" => 429,
                "message" => "Too many failed login attempts. Please wait " . ceil($rateLimit['retry_after'] / 60) . " minute(s) before trying again."
            ]);
        }

        $email = trim($email);
        $query = $this->conn->prepare("SELECT * FROM `user` WHERE `user_email` = ? AND user_status = 'Active' LIMIT 1");
        if (!$query) {
            return json_encode([
                "status" => "error",
                "message" => "Database error occurred."
            ]);
        }

        $query->bind_param("s", $email);

        if ($query->execute()) {
            $result = $query->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify password
                if (!password_verify($password, $user['user_password'])) {
                    $this->recordFailedAttempt('login_admin', 5, 300);
                    return json_encode([
                        "status" => "error",
                        "message" => "Invalid email or password."
                    ]);
                }

                // Reset rate limit on success
                $this->resetRateLimit('login_admin');

                // Regenerate session ID to prevent session fixation attacks
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }

                $_SESSION['user_id'] = (int)$user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];

                return json_encode([
                    "status" => "success",
                    "message" => "Login successful.",
                    "user" => [
                        "user_id" => (int)$user['user_id'],
                        "user_type" => $user['user_type'],
                        "user_email" => $user['user_email']
                    ]
                ]);
            } else {
                $this->recordFailedAttempt('login_admin', 5, 300);
                return json_encode([
                    "status" => "error",
                    "message" => "Invalid email or password."
                ]);
            }
        } else {
            return json_encode([
                "status" => "error",
                "message" => "Database query failed."
            ]);
        }
    }

    /**
     * Public Document Tracking by Tracking Code (cr_code)
     * Secure query with PII masking for public tracking on the landing page
     */
    public function TrackDocument(string $trackingCode): string
    {
        $trackingCode = trim($trackingCode);
        if (empty($trackingCode)) {
            return json_encode([
                "status" => "error",
                "message" => "Please enter a valid tracking code."
            ]);
        }

        // Limit code length to avoid excessive query payloads
        if (strlen($trackingCode) > 64) {
            return json_encode([
                "status" => "error",
                "message" => "Invalid tracking code format."
            ]);
        }

        $stmt = $this->conn->prepare("
            SELECT 
                cr.cr_id,
                cr.cr_code,
                cr.cr_formtype,
                cr.cr_purpose,
                cr.cr_price,
                cr.cr_shipping_fee,
                cr.cr_total,
                cr.cr_payment,
                cr.cr_request_date,
                cr.cr_status,
                r.r_fname,
                r.r_lname,
                r.r_barangay,
                r.r_municipality
            FROM `centralize_request` cr
            LEFT JOIN `resident` r ON cr.cr_r_id = r.r_id
            WHERE cr.cr_code = ?
            LIMIT 1
        ");

        if (!$stmt) {
            return json_encode([
                "status" => "error",
                "message" => "Database query failed."
            ]);
        }

        $stmt->bind_param("s", $trackingCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();

            // PII Masking: John Doe -> J*** D**
            $maskedName = "Resident";
            if (!empty($row['r_fname'])) {
                $f = trim($row['r_fname']);
                $l = trim($row['r_lname'] ?? '');
                $fMasked = mb_substr($f, 0, 1) . str_repeat('*', max(2, mb_strlen($f) - 1));
                $lMasked = !empty($l) ? (mb_substr($l, 0, 1) . str_repeat('*', max(2, mb_strlen($l) - 1))) : '';
                $maskedName = trim($fMasked . ' ' . $lMasked);
            }

            // Map progress step (1 to 4)
            // 1: Pending (Submitted)
            // 2: Processing (Under Verification)
            // 3: Approved / Shipped (Ready for pickup / Out for delivery)
            // 4: Delivered / Completed (Claimed)
            $status = $row['cr_status'];
            $step = 1;
            $statusBadge = 'Pending';
            $badgeColor = 'amber';

            if (in_array(strtolower($status), ['pending', 'submitted'])) {
                $step = 1;
                $statusBadge = 'Pending Verification';
                $badgeColor = 'amber';
            } elseif (in_array(strtolower($status), ['processing', 'under review', 'verified'])) {
                $step = 2;
                $statusBadge = 'Under Review';
                $badgeColor = 'blue';
            } elseif (in_array(strtolower($status), ['approved', 'shipped', 'ready'])) {
                $step = 3;
                $statusBadge = ($row['cr_payment'] === 'Pick Up' || strpos(strtolower($status), 'ready') !== false) ? 'Ready for Pickup' : 'Dispatched / In Transit';
                $badgeColor = 'indigo';
            } elseif (in_array(strtolower($status), ['delivered', 'completed', 'claimed', 'released'])) {
                $step = 4;
                $statusBadge = 'Completed & Claimed';
                $badgeColor = 'emerald';
            } elseif (in_array(strtolower($status), ['canceled', 'cancelled', 'rejected', 'declined'])) {
                $step = -1;
                $statusBadge = 'Canceled / Declined';
                $badgeColor = 'rose';
            }

            return json_encode([
                "status" => "success",
                "data" => [
                    "tracking_code" => htmlspecialchars($row['cr_code']),
                    "form_type" => htmlspecialchars($row['cr_formtype']),
                    "purpose" => htmlspecialchars($row['cr_purpose']),
                    "payment_method" => htmlspecialchars($row['cr_payment']),
                    "total_amount" => number_format((float)$row['cr_total'], 2),
                    "request_date" => date('F d, Y - h:i A', strtotime($row['cr_request_date'])),
                    "current_status" => $statusBadge,
                    "raw_status" => htmlspecialchars($row['cr_status']),
                    "step" => $step,
                    "badge_color" => $badgeColor,
                    "masked_resident" => $maskedName,
                    "location" => htmlspecialchars(trim(($row['r_barangay'] ?? '') . ', ' . ($row['r_municipality'] ?? ''), ', '))
                ]
            ]);
        }

        $stmt->close();
        return json_encode([
            "status" => "error",
            "message" => "No record found for tracking code \"" . htmlspecialchars($trackingCode) . "\". Please double check your code."
        ]);
    }

    /**
     * Get Published Announcements for Public Display
     */
    public function GetPublicAnnouncements(int $limit = 6): string
    {
        $limit = max(1, min(12, $limit));

        $stmt = $this->conn->prepare("
            SELECT 
                announcement_id,
                title,
                content,
                category,
                image,
                is_pinned,
                created_at
            FROM `announcements`
            WHERE `status` = 'Published'
            ORDER BY `is_pinned` DESC, `created_at` DESC
            LIMIT ?
        ");

        if (!$stmt) {
            return json_encode([
                "status" => "error",
                "message" => "Database query failed."
            ]);
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcements = [];

        while ($row = $result->fetch_assoc()) {
            $announcements[] = [
                'id' => (int)$row['announcement_id'],
                'title' => htmlspecialchars($row['title']),
                'content' => htmlspecialchars($row['content']),
                'snippet' => htmlspecialchars(mb_strimwidth(strip_tags($row['content']), 0, 160, '...')),
                'category' => htmlspecialchars($row['category'] ?? 'General'),
                'image' => !empty($row['image']) ? htmlspecialchars($row['image']) : null,
                'is_pinned' => (int)$row['is_pinned'] === 1,
                'formatted_date' => date('M d, Y', strtotime($row['created_at'])),
                'time_ago' => $this->timeAgo($row['created_at'])
            ];
        }

        $stmt->close();
        return json_encode([
            "status" => "success",
            "data" => $announcements
        ]);
    }

    /**
     * Helper for relative time formatting
     */
    private function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M d, Y', $time);
    }
}