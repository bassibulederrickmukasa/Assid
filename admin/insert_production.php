<?php
// Include database connection
include '../includes/db.php'; // Adjust the path if necessary

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $date = $_POST['date'];
    $small_boxes = $_POST['small_boxes'];
    $big_boxes = $_POST['big_boxes'];

    // Check if the inputs are valid
    if (!empty($date) && is_numeric($small_boxes) && is_numeric($big_boxes)) {
        // Calculate balance stock based on existing production records
        // You may need to modify this part based on how you want to calculate the balance
        $stmt = $pdo->query("SELECT balance_stock_small, balance_stock_big FROM production_data ORDER BY id DESC LIMIT 1");
        $lastRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastRecord) {
            // If there's a previous record, update balance stock by adding new production
            $balance_stock_small = $lastRecord['balance_stock_small'] + $small_boxes;
            $balance_stock_big = $lastRecord['balance_stock_big'] + $big_boxes;
        } else {
            // If no previous record, balance stock equals today's production
            $balance_stock_small = $small_boxes;
            $balance_stock_big = $big_boxes;
        }

        // Insert the new production data into the database
        $stmt = $pdo->prepare("INSERT INTO production_data (date, small_boxes, big_boxes, balance_stock_small, balance_stock_big) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$date, $small_boxes, $big_boxes, $balance_stock_small, $balance_stock_big]);

        // Redirect back to the production page or show a success message
        header('Location: production.php'); // Redirect back to the production page
        exit();
    } else {
        echo "Invalid input. Please make sure all fields are filled in correctly.";
    }
} else {
    // If the form wasn't submitted, redirect back to the production page
    header('Location: production.php');
    exit();
}
?>
