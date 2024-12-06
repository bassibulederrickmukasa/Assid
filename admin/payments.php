<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

include '../includes/db.php'; // Include database connection

// Fetch total produced boxes and calculate total amount demanded
$stmt = $conn->query("SELECT SUM(small_boxes) AS total_small, SUM(big_boxes) AS total_big FROM production_data");
$production_data = $stmt->fetch(PDO::FETCH_ASSOC);
$total_small_boxes = $production_data['total_small'] ?? 0; // Default to 0 if NULL
$total_big_boxes = $production_data['total_big'] ?? 0; // Default to 0 if NULL

// Calculate total amount demanded
$amount_per_small_box = 300;
$amount_per_big_box = 500;
$total_amount_demanded = ($total_small_boxes * $amount_per_small_box) + ($total_big_boxes * $amount_per_big_box);

// Fetch the current balance from the database
$stmt = $conn->query("SELECT balance FROM payments ORDER BY id DESC LIMIT 1");
$current_balance = $stmt->fetch(PDO::FETCH_ASSOC)['balance'] ?? $total_amount_demanded; // Use total amount demanded if no balance found

// Initialize the new_balance variable
$new_balance = $current_balance;

// Process the payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_received = $_POST['payment_received'];
    
    // Update the balance
    $new_balance = $current_balance - $payment_received;

    // Insert the payment into the payments table
    $stmt = $conn->prepare("INSERT INTO payments (payment_received, balance, payment_date) VALUES (?, ?, NOW())");
    $stmt->execute([$payment_received, $new_balance]);

    // Redirect to avoid form re-submission on refresh
    header("Location: payments.php");
    exit();
}

// Fetch all payment records
$stmt = $conn->query("SELECT * FROM payments ORDER BY payment_date DESC");
$payment_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Update the link to the CSS file -->
    <style>
        .form-container {
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-container input {
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="production.php">Production</a></li>
            <li><a href="supplies.php">Supplies</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="payments.php" class="active">Payments</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Payments</h1>

        <!-- Payment Form -->
        <form action="payments.php" method="post">
            <label for="payment_received">Amount Received:</label>
            <input type="number" id="payment_received" name="payment_received" required>
            <button type="submit">Submit Payment</button>
        </form>

        <!-- Display Total Amount Demanded -->
        <h2>Total Amount Demanded: <?php echo number_format($total_amount_demanded); ?> UGX</h2>
        <h3>Breakdown:</h3>
        <ul>
            <li>Small Boxes (<?php echo $total_small_boxes; ?>) at <?php echo number_format($amount_per_small_box); ?> UGX each: <?php echo number_format($total_small_boxes * $amount_per_small_box); ?> UGX</li>
            <li>Big Boxes (<?php echo $total_big_boxes; ?>) at <?php echo number_format($amount_per_big_box); ?> UGX each: <?php echo number_format($total_big_boxes * $amount_per_big_box); ?> UGX</li>
        </ul>

        <!-- Display Current Balance -->
        <h2>Current Balance: <?php echo number_format($new_balance); ?> UGX</h2>

        <!-- Payment Records Table -->
        <?php if ($payment_records): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payment Received</th>
                        <th>New Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payment_records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['payment_date']) ?></td>
                            <td><?= htmlspecialchars(number_format($record['payment_received'])) ?> UGX</td>
                            <td><?= htmlspecialchars(number_format($record['balance'])) ?> UGX</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No payments recorded yet.</p>
        <?php endif; ?>
    </div>

    <script src="../js/script.js"></script> <!-- Link to your JS file -->
</body>
</html>
