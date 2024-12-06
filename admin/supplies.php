<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

include '../includes/db.php'; // Include database connection

// Fetch supplies data from the database
try {
    $stmt = $conn->query("SELECT * FROM supplies_data");
    $suppliesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching supplies data: " . $e->getMessage();
    $suppliesData = [];
}

// Fetch production data from the database to calculate balance stock
try {
    $stmt = $conn->query("SELECT SUM(small_boxes) AS total_small_boxes, SUM(big_boxes) AS total_big_boxes FROM production_data");
    $productionData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching production data: " . $e->getMessage();
    $productionData = ['total_small_boxes' => 0, 'total_big_boxes' => 0];
}

// Calculate total supplied boxes
$totalSuppliedSmallBoxes = 0;
$totalSuppliedBigBoxes = 0;
foreach ($suppliesData as $supply) {
    $totalSuppliedSmallBoxes += $supply['small_boxes'];
    $totalSuppliedBigBoxes += $supply['big_boxes'];
}

// Calculate balance stock
$balanceStockSmall = $productionData['total_small_boxes'] - $totalSuppliedSmallBoxes;
$balanceStockBig = $productionData['total_big_boxes'] - $totalSuppliedBigBoxes;

// Handle form submission for adding new supplies
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $personnel = $_POST['personnel'];
    $small_boxes = $_POST['small_boxes'];
    $big_boxes = $_POST['big_boxes'];
    $date = $_POST['date'];

    // Insert the new supply record into the database
    if (!empty($personnel) && !empty($small_boxes) && !empty($big_boxes) && !empty($date)) {
        $insertQuery = "INSERT INTO supplies_data (personnel, small_boxes, big_boxes, date) VALUES (:personnel, :small_boxes, :big_boxes, :date)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':personnel', $personnel);
        $stmt->bindParam(':small_boxes', $small_boxes);
        $stmt->bindParam(':big_boxes', $big_boxes);
        $stmt->bindParam(':date', $date);

        if ($stmt->execute()) {
            header("Location: supplies.php"); // Redirect to the same page to see the updated list
            exit();
        } else {
            echo "Error adding supply record.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Data</title>
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
            <li><a href="supplies.php" class="active">Supplies</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Supplies Data</h1>
        <div class="form-container">
            <h2>Add New Supply Record</h2>
            <form method="POST" action="">
                <input type="text" name="personnel" placeholder="Personnel" required>
                <input type="number" name="small_boxes" placeholder="Small Boxes" required>
                <input type="number" name="big_boxes" placeholder="Big Boxes" required>
                <input type="date" name="date" required>
                <button type="submit">Add Supply</button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Personnel</th>
                    <th>Small Boxes</th>
                    <th>Big Boxes</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($suppliesData)): ?>
                    <?php foreach ($suppliesData as $supply): ?>
                        <tr>
                            <td><?= htmlspecialchars($supply['id']) ?></td>
                            <td><?= htmlspecialchars($supply['personnel']) ?></td>
                            <td><?= htmlspecialchars($supply['small_boxes']) ?></td>
                            <td><?= htmlspecialchars($supply['big_boxes']) ?></td>
                            <td><?= htmlspecialchars($supply['date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No supplies data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>Balance Stock</h2>
        <p><strong>Balance Stock (Small Boxes):</strong> <?= $balanceStockSmall ?></p>
        <p><strong>Balance Stock (Big Boxes):</strong> <?= $balanceStockBig ?></p>
    </div>
</body>
</html>
