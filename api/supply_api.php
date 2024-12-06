<?php
session_start();
include '../includes/db.php'; // Include database connection
include '../includes/functions.php'; // Include functions for input validation

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['message' => 'Unauthorized access']);
    exit();
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Define actions based on request method
switch ($method) {
    case 'POST':
        // Add new supply
        addSupply();
        break;

    case 'GET':
        // Get supply data
        getSupplies();
        break;

    case 'PUT':
        // Update supply
        updateSupply();
        break;

    case 'DELETE':
        // Delete supply
        deleteSupply();
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

// Function to add new supply
function addSupply() {
    global $conn;

    // Get the data from the request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    $small_boxes = isset($input['small_boxes']) ? intval($input['small_boxes']) : 0;
    $big_boxes = isset($input['big_boxes']) ? intval($input['big_boxes']) : 0;
    $personnel = isset($input['personnel']) ? sanitizeInput($input['personnel']) : '';

    if ($small_boxes < 0 || $big_boxes < 0 || empty($personnel)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid input data']);
        return;
    }

    // Insert supply into the database
    try {
        $stmt = $conn->prepare("INSERT INTO supplies_data (small_boxes, big_boxes, personnel) VALUES (:small_boxes, :big_boxes, :personnel)");
        $stmt->bindParam(':small_boxes', $small_boxes);
        $stmt->bindParam(':big_boxes', $big_boxes);
        $stmt->bindParam(':personnel', $personnel);

        if ($stmt->execute()) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Supply added successfully']);
        } else {
            throw new Exception('Failed to add supply');
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => $e->getMessage()]);
    }
}

// Function to get all supplies
function getSupplies() {
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT * FROM supplies_data");
        $stmt->execute();
        $supplies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        http_response_code(200); // OK
        echo json_encode($supplies);
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Failed to retrieve supplies']);
    }
}

// Function to update supply
function updateSupply() {
    global $conn;

    // Get the data from the request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = isset($input['id']) ? intval($input['id']) : 0;
    $small_boxes = isset($input['small_boxes']) ? intval($input['small_boxes']) : 0;
    $big_boxes = isset($input['big_boxes']) ? intval($input['big_boxes']) : 0;
    $personnel = isset($input['personnel']) ? sanitizeInput($input['personnel']) : '';

    if ($id <= 0 || $small_boxes < 0 || $big_boxes < 0 || empty($personnel)) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid input data']);
        return;
    }

    // Update supply in the database
    try {
        $stmt = $conn->prepare("UPDATE supplies_data SET small_boxes = :small_boxes, big_boxes = :big_boxes, personnel = :personnel WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':small_boxes', $small_boxes);
        $stmt->bindParam(':big_boxes', $big_boxes);
        $stmt->bindParam(':personnel', $personnel);

        if ($stmt->execute()) {
            http_response_code(200); // OK
            echo json_encode(['message' => 'Supply updated successfully']);
        } else {
            throw new Exception('Failed to update supply');
        }
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => $e->getMessage()]);
    }
}

// Function to delete supply
function deleteSupply() {
    global $conn;

    // Get the data from the request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = isset($input['id']) ? intval($input['id']) : 0;

    if ($id <= 0) {
        http_response_code(400); // Bad Request
        echo json_encode(['message' => 'Invalid ID']);
        return;
    }

    // Delete supply from the database
    try {
        $stmt = $conn->prepare("DELETE FROM supplies_data WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            http_response_code(200); // OK
            echo json_encode(['message
