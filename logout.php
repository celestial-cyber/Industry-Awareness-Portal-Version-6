<?php
/**
 * Logout Handler
 * Handles logout for both student and admin users
 * Destroys session and redirects appropriately
 */

session_start();

// Determine which type of user is logging out
$is_student = isset($_SESSION['student_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Destroy session
session_destroy();

// Redirect based on user type
if ($is_student) {
    // Student logout
    header("Location: student_login.php?logout=success");
} elseif ($is_admin) {
    // Admin logout
    header("Location: admin_login.php?logout=success");
} else {
    // Default to home page
    header("Location: index.php");
}

exit();
?>
