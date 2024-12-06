<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

include '../includes/db.php'; // Include database connection

// Check if form data has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the form input
    $personnel = htmlspecialchars($_POST['personnel']);
    $small_boxes = (int) $_POST['small_boxes'];
    $big_boxes = (int) $_POST['big_boxes'];
    $date = $_POST['date'];

    // Insert the supply data into the database
    try {
        $stmt = $conn->prepare("INSERT INTO supplies_data (personnel, small_boxes, big_boxes, date) VALUES (:personnel, :small_boxes, :big_boxes, :date)");
        $stmt->bindParam(':personnel', $personnel);
        $stmt->bindParam(':small_boxes', $small_boxes);
        $stmt->bindParam(':big_boxes', $big_boxes);
        $stmt->bindParam(':date', $date);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Supply record added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add the supply record.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // Redirect back to the supplies page
    header("Location: supplies.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: supplies.php");
    exit();
}
?>
