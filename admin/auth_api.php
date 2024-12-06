<?php
session_start();

// Enable error reporting for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../database.php'; // Ensure the path to database.php is correct

if (!$pdo) {
    die('Database connection failed. Please check your credentials.');
}

// Function to log in the user
function login($username, $password) {
    global $pdo;

    // Prepare the SQL statement to fetch user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verify the password and set session if successful
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // Store the user's ID in session
        header("Location: dashboard.php"); // Redirect to the dashboard after login
        exit();
    } else {
        // Show an error if login fails
        echo 'Invalid login credentials.';
    }
}

// Check if login form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the login function
    login($username, $password);
}

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to log out the user
function logout() {
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to the login page
    exit();
}
