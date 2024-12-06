<?php
session_start();
include 'includes/db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page in the admin folder
    header('Location: /Assid/admin/login.php');
    exit();
}

// Fetch the user's role from the session
$userRole = $_SESSION['role'] ?? null;

if ($userRole === 'admin') {
    // If the user is an admin, redirect to the admin dashboard
    header('Location: /Assid/admin/dashboard.php');
    exit();
} elseif ($userRole === 'user') {
    // If the user is a regular user, redirect to the user dashboard
    header("Location: user\user_dashboard_view.php");
    exit();
} else {
    // If the role is not recognized, log out and redirect to login page in the admin folder
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
