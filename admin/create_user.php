<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password, password_changed) VALUES (:username, :password, :password_changed)");
    $stmt->execute([
        'username' => $username,
        'password' => password_hash('123', PASSWORD_DEFAULT), // Default password
        'password_changed' => 0 // Set to 0 as the user must change it
    ]);

    echo "User created successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="production.php">Production</a></li>
            <li><a href="supplies.php">Supplies</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="payments.php">Payments</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Create New User</h1>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <button type="submit">Create User</button>
        </form>
    </div>
</body>
</html>
