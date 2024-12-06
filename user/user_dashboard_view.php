<?php
session_start();
include '../includes/db.php'; // Include your database connection

// Fetch payment data from the database
try {
    $stmt = $conn->query("SELECT amount FROM payments WHERE DATE(date) = CURDATE()");
    $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data was fetched successfully
    if (!$paymentData) {
        $paymentData = ['amount' => 0];
    }
} catch (PDOException $e) {
    echo "Error fetching payment data: " . $e->getMessage();
    $paymentData = ['amount' => 0];
}

// Fetch balance stock data
try {
    $stmt = $conn->query("SELECT balance_stock_small, balance_stock_big FROM production_data WHERE date = CURDATE()");
    $balanceData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data was fetched successfully
    if (!$balanceData) {
        $balanceData = ['balance_stock_small' => 0, 'balance_stock_big' => 0];
    }
} catch (PDOException $e) {
    echo "Error fetching balance stock data: " . $e->getMessage();
    $balanceData = ['balance_stock_small' => 0, 'balance_stock_big' => 0];
}

// Assigning values with fallback for HTML output
$balanceStockSmall = $balanceData['balance_stock_small'] ?? 0;
$balanceStockBig = $balanceData['balance_stock_big'] ?? 0;
$paymentAmount = $paymentData['amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard View</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h2>Daily Overview</h2>
    <p>Date: <?php echo date("d/m/Y"); ?></p>

    <h3>Balance Stock</h3>
    <p>Small Boxes: <?php echo $balanceStockSmall; ?></p>
    <p>Big Boxes: <?php echo $balanceStockBig; ?></p>

    <h3>Payment</h3>
    <p>Payment Received: +<?php echo $paymentAmount; ?> (0 boxes)</p>
    <p>Balance Payment: 0/=</p>
</body>
</html>
