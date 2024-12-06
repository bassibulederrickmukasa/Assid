<?php 
session_start();
include '../includes/db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement to fetch the user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Check if user exists and verify the password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // Store user role in session
        
        // Check if the password needs to be changed
        if ($user['password_changed'] == 0) {
            header("Location: change_password.php"); // Redirect to change password page
            exit();
        }

        // Redirect based on the user's role
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: dashboard.php"); // Admin dashboard
                break;
            case 'user':
                header("Location: ../user/user_dashboard_view.php"); // Regular user dashboard
                break;
            // Add more roles here if needed
            default:
                header("Location: login.php"); // Redirect to login for undefined roles
                exit();
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo '<p>' . htmlspecialchars($error) . '</p>'; ?>
</body>
</html>
