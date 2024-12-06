<?php 
session_start();
include '../includes/db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user data from the database
$userId = $_SESSION['user_id'];

// Example SQL queries to fetch data (update these to match your actual database structure)
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT * FROM supplies_data WHERE date = :date AND user_id = :user_id");
$stmt->execute(['date' => $today, 'user_id' => $userId]);
$supplyData = $stmt->fetch();

$stmtBalance = $conn->prepare("SELECT * FROM production_data WHERE date = :date");
$stmtBalance->execute(['date' => $today]);
$balanceData = $stmtBalance->fetch();

// Initialize variables for display
$smallbx = $supplyData ? $supplyData['small_boxes'] : 0;
$bigbx = $supplyData ? $supplyData['big_boxes'] : 0;
$packingSmall = $supplyData ? $supplyData['packing_small'] : 0;
$packingBig = $supplyData ? $supplyData['packing_big'] : 0;
$balanceStockSmall = $balanceData ? $balanceData['balance_stock_small'] : 0;
$balanceStockBig = $balanceData ? $balanceData['balance_stock_big'] : 0;
$paymentAmount = $smallbx * 300 + $bigbx * 500; // Assuming 300 for small box and 500 for big box
$balancePayment = 204200; // Replace with actual balance payment logic
$userName = "Salm aleikhm"; // You can replace this with the actual user's name from the session

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1><?php echo htmlspecialchars($userName); ?></h1>
    <p><?php echo date('d/m/y'); ?></p>

    <h2>Small and Big Boxes</h2>
    <p>smallbx = <?php echo $smallbx; ?></p>
    <p>bigbx = <?php echo $bigbx; ?></p>

    <h2>Packing</h2>
    <p>small = <?php echo $packingSmall; ?></p>
    <p>big = <?php echo $packingBig; ?></p>

    <h2>Supply</h2>
    <p>wilber = <?php echo $smallbx; ?></p> <!-- Replace "wilber" with dynamic user information if necessary -->

    <h2>Balance Stock</h2>
    <p>small = <?php echo $balanceStockSmall; ?></p>
    <p>bigbx = <?php echo $balanceStockBig; ?></p>

    <h2>Payment</h2>
    <p>+<?php echo $paymentAmount; ?>(<?php echo $smallbx + $bigbx; ?> bxs)</p>
    <p>Balpayment = <?php echo $balancePayment; ?>/=, on a daily basis</p>
</body>
</html>
