<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment = $_POST['payment'];
    $balance = $_POST['balance'];
    $box_type = $_POST['box_type'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO payments (payment_amount, payment_balance, box_type, date)
                            VALUES (:payment, :balance, :box_type, :date)");
    $stmt->bindParam(':payment', $payment);
    $stmt->bindParam(':balance', $balance);
    $stmt->bindParam(':box_type', $box_type);
    $stmt->bindParam(':date', $date);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
