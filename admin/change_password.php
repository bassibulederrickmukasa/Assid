<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not authenticated
    exit();
}

include '../includes/db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['password'];
    $userId = $_SESSION['user_id'];

    // Update password and set password_changed to 1
    $stmt = $conn->prepare("UPDATE users SET password = :password, password_changed = 1 WHERE id = :id");
    $stmt->bindParam(':password', password_hash($newPassword, PASSWORD_DEFAULT));
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    header("Location: dashboard.php"); // Redirect to dashboard after updating password
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="main-content">
        <h1>Change Password</h1>
        <form method="POST">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
