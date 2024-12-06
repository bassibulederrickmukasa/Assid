<?php
include '../includes/db.php';

function logAction($userId, $action) {
    global $conn;

    $stmt = $conn->prepare("INSERT INTO logs (user_id, action) VALUES (:user_id, :action)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':action', $action);
    $stmt->execute();
}

// Example usage: logAction(1, 'User logged in');
