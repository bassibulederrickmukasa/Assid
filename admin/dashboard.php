<?php
session_start();
include '../includes/db.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch current stock for small and big boxes by subtracting supplied boxes from production boxes
$current_small_boxes = 0;
$current_big_boxes = 0;

// Query to get current stock of small and big boxes
$sql = "SELECT 
            SUM(balance_stock_small) as total_small, 
            SUM(balance_stock_big) as total_big 
        FROM production_data";
$stmt = $conn->prepare($sql);
$stmt->execute();
$production_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Query to get total supplied boxes
$sql = "SELECT 
            SUM(small_boxes) as supplied_small, 
            SUM(big_boxes) as supplied_big 
        FROM supplies_data";
$stmt = $conn->prepare($sql);
$stmt->execute();
$supplied_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate current stock by subtracting supplied from total
$current_small_boxes = $production_data['total_small'] - $supplied_data['supplied_small'];
$current_big_boxes = $production_data['total_big'] - $supplied_data['supplied_big'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Dashboard card layout */
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .dashboard-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .dashboard-card h3 {
            margin-bottom: 10px;
            font-size: 24px;
            color: #333;
        }

        .dashboard-card p {
            font-size: 18px;
            color: #555;
        }

        .dashboard-card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .dashboard-card a:hover {
            background-color: #0056b3;
        }

        /* Logout button styling */
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <!-- Logout button -->
    <a href="../logout.php" class="logout-btn">Logout</a>

    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>

    <div class="dashboard-container">
        <!-- Current stock of small boxes card -->
        <div class="dashboard-card">
            <h3>Current Small Box Stock</h3>
            <p><?php echo $current_small_boxes; ?></p>
            <a href="production.php">View Production</a>
        </div>

        <!-- Current stock of big boxes card -->
        <div class="dashboard-card">
            <h3>Current Big Box Stock</h3>
            <p><?php echo $current_big_boxes; ?></p>
            <a href="production.php">View Production</a>
        </div>

        <!-- Total supplies card -->
        <div class="dashboard-card">
            <h3>Total Supplies (Small & Big)</h3>
            <p>Small: <?php echo $supplied_data['supplied_small']; ?> | Big: <?php echo $supplied_data['supplied_big']; ?></p>
            <a href="supplies.php">View Supplies</a>
        </div>

        <!-- Payout card -->
        <div class="dashboard-card">
            <h3>Payout Summary</h3>
            <p>Manage and view financial payouts</p>
            <a href="payments.php">View Payouts</a>
        </div>

        <!-- Inventory card -->
        <div class="dashboard-card">
            <h3>Inventory</h3>
            <p>Track and manage inventory stock</p>
            <a href="inventory.php">Manage Inventory</a>
        </div>

        <!-- Reports card -->
        <div class="dashboard-card">
            <h3>Generate Reports</h3>
            <p>Create and download reports</p>
            <a href="reports.php">Generate Report</a>
        </div>
    </div>

</body>
</html>
