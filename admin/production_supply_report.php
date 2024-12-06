<?php
session_start();
include '../includes/db.php'; // Include database connection

// Fetch production data
$stmt = $conn->query("SELECT * FROM production_data");
$productionData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare CSV file
$filename = "production_supply_report_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Small Boxes', 'Big Boxes', 'Balance Stock Small', 'Balance Stock Big']); // Column headings

// Fetch data and write to CSV
foreach ($productionData as $data) {
    fputcsv($output, [
        $data['date'], 
        $data['small_boxes'], 
        $data['big_boxes'], 
        $data['balance_stock_small'], 
        $data['balance_stock_big']
    ]);
}
fclose($output);
exit();
?>
