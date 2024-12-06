<?php
// auth.php: Handles user authentication and role verification
session_start();

function checkAuth($role = null) {
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    if ($role && $_SESSION['role'] !== $role) {
        header('Location: index.php');
        exit();
    }
}
?>
