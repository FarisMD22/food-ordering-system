// File: includes/security.php
<?php
/**
 * Security Functions for FoodieDelight
 * Handles authentication, CSRF protection, and security logging
 */

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Rate limiting for login attempts
 */
function checkRateLimit($identifier, $max_attempts = 5, $time_window = 3600) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as attempts 
            FROM behavior_logs 
            WHERE JSON_EXTRACT(event_data, '$.ip') = ? 
            AND event_type = 'security_event'
            AND JSON_EXTRACT(event_data, '$.event') = 'failed_login'
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");

        $stmt->execute([$identifier, $time_window]);
        $result = $stmt->fetch();

        return $result['attempts'] < $max_attempts;

    } catch (PDOException $e) {
        error_log("Rate limit check failed: " . $e->getMessage());
        return true; // Allow access if check fails
    }
}

/**
 * Log security events
 */
function logSecurityEvent($event, $details = []) {
    $details['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $details['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $details['timestamp'] = date('Y-m-d H:i:s');

    trackBehaviorLog($_SESSION['user_id'] ?? null, 'security_event', array_merge(['event' => $event], $details));
}

/**
 * Check if user is logged in
 */
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
}

/**
 * Check if user is admin
 */
function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php?page=home');
        exit;
    }
}

/**
 * Clean and validate file uploads
 */
function validateFileUpload($file, $allowed_types = ['image/jpeg', 'image/png', 'image/gif']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return ['success' => false, 'message' => 'File too large'];
    }

    return ['success' => true];
}

?>