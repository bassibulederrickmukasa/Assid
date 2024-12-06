<?php
include '../includes/auth.php';
checkAuth('user'); // Ensure user is logged in
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="content">
    <h1>User Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
    <p>Overview of your supplies and payments.</p>
    
    <!-- Include your stats and graphs here -->
</div>
