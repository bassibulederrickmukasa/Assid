<?php
session_start();
include '../includes/db.php'; // Include database connection

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Define actions based on request method
switch ($method) {
    case 'POST':
        // Register or login user based on the request body
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['action'])) {
            if ($input['action'] === 'register') {
                registerUser($input);
            } elseif ($input['action'] === 'login') {
                loginUser($input);
            } elseif ($input['action'] === 'logout') {
                logoutUser();
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid action']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Action is required']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

// Function to register a new user
function registerUser($input) {
    global $conn;

    $username = isset($input['username']) ? sanitizeInput($input['username']) : '';
    $password = isset($input['password']) ? $input['password'] : '';

    // Validate input data
    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Username and password are required']);
        return;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Failed to register user']);
    }
}

// Function to login a user
function loginUser($input) {
    global $conn;

    $username = isset($input['username']) ? sanitizeInput($input['username']) : '';
    $password = isset($input['password']) ? $input['password'] : '';

    // Validate input data
    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Username and password are required']);
        return;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session
        $_SESSION['username'] = $user['username'];
        http_response_code(200); // OK
        echo json_encode(['message' => 'Login successful']);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Invalid username or password']);
    }
}

// Function to logout a user
function logoutUser() {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    http_response_code(200); // OK
    echo json_encode(['message' => 'Logout successful']);
}

// Sanitize input function
function sanitizeInput($data) {
    return htmlspecialchars(trim($data)); // Remove extra spaces and HTML special chars
}
?>
