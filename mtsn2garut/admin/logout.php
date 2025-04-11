<?php
session_start();
require_once '../includes/functions.php';

// Log logout activity if user was logged in
if (isset($_SESSION['admin_id'])) {
    log_activity(
        'Logout',
        'User logged out from admin panel',
        $_SESSION['admin_id']
    );
}

// Destroy all session data
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
