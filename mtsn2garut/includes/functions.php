<?php
/**
 * Helper functions for the website
 */

/**
 * Get setting value from database
 * @param string $key Setting key
 * @param string $default Default value if setting not found
 * @return string Setting value
 */
function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch(PDOException $e) {
        error_log("Error getting setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Update setting value in database
 * @param string $key Setting key
 * @param string $value Setting value
 * @return bool Success status
 */
function update_setting($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        return $stmt->execute([$key, $value]);
    } catch(PDOException $e) {
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Sanitize output
 * @param string $text Text to sanitize
 * @return string Sanitized text
 */
function safe_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date to Indonesian format
 * @param string $date Date string
 * @param bool $with_time Include time in output
 * @return string Formatted date
 */
function format_date($date, $with_time = false) {
    $months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    
    if ($with_time) {
        $time = date('H:i', $timestamp);
        return "$day $month $year, $time WIB";
    }
    
    return "$day $month $year";
}

/**
 * Generate random string
 * @param int $length Length of string
 * @return string Random string
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

/**
 * Upload file with security checks
 * @param array $file $_FILES array element
 * @param string $destination Destination directory
 * @param array $allowed_types Allowed file types
 * @param int $max_size Maximum file size in bytes
 * @return array ['success' => bool, 'message' => string, 'filename' => string]
 */
function upload_file($file, $destination, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 2097152) {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'Error uploading file.';
        return $result;
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        $result['message'] = 'File too large. Maximum size is ' . ($max_size / 1024 / 1024) . 'MB.';
        return $result;
    }
    
    // Check file type
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);
    
    if (!in_array($extension, $allowed_types)) {
        $result['message'] = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types);
        return $result;
    }
    
    // Generate unique filename
    $filename = uniqid() . '.' . $extension;
    $filepath = rtrim($destination, '/') . '/' . $filename;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['message'] = 'Error moving uploaded file.';
    }
    
    return $result;
}

/**
 * Delete file with security checks
 * @param string $filepath File path
 * @return bool Success status
 */
function delete_file($filepath) {
    // Security check: ensure file is within allowed directories
    $allowed_dirs = [
        realpath(__DIR__ . '/../assets/uploads/teachers'),
        realpath(__DIR__ . '/../assets/uploads/news'),
        realpath(__DIR__ . '/../assets/uploads/achievements'),
        realpath(__DIR__ . '/../assets/uploads/facilities'),
        realpath(__DIR__ . '/../assets/uploads/gallery'),
        realpath(__DIR__ . '/../assets/images')
    ];
    
    $real_path = realpath($filepath);
    if (!$real_path) {
        return false;
    }
    
    $is_allowed = false;
    foreach ($allowed_dirs as $dir) {
        if (strpos($real_path, $dir) === 0) {
            $is_allowed = true;
            break;
        }
    }
    
    if (!$is_allowed) {
        return false;
    }
    
    // Delete file if exists
    if (file_exists($real_path)) {
        return unlink($real_path);
    }
    
    return false;
}

/**
 * Log activity
 * @param string $action Action performed
 * @param string $details Action details
 * @param int $user_id User ID who performed the action
 */
function log_activity($action, $details, $user_id) {
    global $pdo;
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO activity_log (action, details, user_id, ip_address, user_agent) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$action, $details, $user_id, $ip_address, $user_agent]);
    } catch(PDOException $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}
