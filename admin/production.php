<?php
// Include database connection
include '../includes/db.php'; // Adjust the path to your db.php file

// Fetch production data from the database
$stmt = $pdo->prepare("SELECT * FROM production_data");
$stmt->execute();
$productionData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Data</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Update the link to the CSS file -->
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="production.php"class="active">Production</a></li>
            <li><a href="supplies.php">Supplies</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="payments.php">Payments</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Production Data</h1>

        <form action="insert_production.php" method="POST">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="small_boxes">Small Boxes:</label>
            <input type="number" id="small_boxes" name="small_boxes" required>

            <label for="big_boxes">Big Boxes:</label>
            <input type="number" id="big_boxes" name="big_boxes" required>

            <button type="submit">Add Production</button>
        </form>

        <h2>Production Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Small Boxes</th>
                    <th>Big Boxes</th>
                    <th>Balance Stock Small</th>
                    <th>Balance Stock Big</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productionData as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['small_boxes']); ?></td>
                        <td><?php echo htmlspecialchars($row['big_boxes']); ?></td>
                        <td><?php echo htmlspecialchars($row['balance_stock_small']); ?></td>
                        <td><?php echo htmlspecialchars($row['balance_stock_big']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
