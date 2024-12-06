<?php
session_start();
if (!isset($_SESSION['user_id'])) { // Check if user is logged in based on user_id session
    header("Location: login.php"); // Redirect to login page if not authenticated
    exit();
}
include '../includes/db.php'; // Include database connection

// Fetch current user details based on session user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="production.php">Production</a></li>
            <li><a href="supplies.php">Supplies</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="payments.php">Payments</a></li>
            <li><a href="create_user.php">create_user</a></li>
            <li><a href="settings.php" class="active">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Account Settings</h1>
        
        <form id="updateUserForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
            
            <button type="submit">Update</button>
        </form>

        <div id="updateMessage"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle form submission for updating user settings
            $('#updateUserForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission
                const formData = {
                    username: $('#username').val(),
                    password: $('#password').val() // Password can be left blank
                };

                $.ajax({
                    url: '../api/auth_api.php', // Path to the authentication API
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ action: 'update', ...formData }), // Include action to specify update operation
                    success: function(response) {
                        $('#updateMessage').html('<p style="color: green;">' + response.message + '</p>');
                    },
                    error: function(xhr) {
                        $('#updateMessage').html('<p style="color: red;">' + xhr.responseJSON.message + '</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>
