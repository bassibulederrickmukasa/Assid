<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Include required libraries at the top
require 'C:/xampp/htdocs/Assid/fpdf/fpdf.php';
require 'C:/xampp/htdocs/Assid/vendor/autoload.php';
require 'C:/xampp/htdocs/Assid/vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;

// Fetch production data
$stmt = $conn->query("SELECT * FROM production_data");
$productionData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch finance data
$financeStmt = $conn->query("SELECT * FROM payments");
$financeData = $financeStmt->fetchAll(PDO::FETCH_ASSOC);

// Function to generate PDF
function generatePDF($startDate, $endDate, $productionData, $financeData) {
    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, 'Report from ' . $startDate . ' to ' . $endDate, 0, 1, 'C');

    // Production & Supply Records
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Production & Supply Records', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 10);
    foreach ($productionData as $data) {
        $pdf->Cell(30, 10, $data['date'], 1);
        $pdf->Cell(30, 10, $data['small_boxes'], 1);
        $pdf->Cell(30, 10, $data['big_boxes'], 1);
        $pdf->Cell(50, 10, $data['balance_stock_small'], 1);
        $pdf->Cell(50, 10, $data['balance_stock_big'], 1);
        $pdf->Ln();
    }

    // Finance Records
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Finance Records', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 10);
    foreach ($financeData as $data) {
        $pdf->Cell(50, 10, $data['payment_date'], 1);
        $pdf->Cell(50, 10, $data['payment_received'], 1);
        $pdf->Cell(50, 10, $data['balance'], 1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'report_' . $startDate . '_to_' . $endDate . '.pdf');
}

// Function to generate Excel
function generateExcel($startDate, $endDate, $productionData, $financeData) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Title
    $sheet->setCellValue('A1', 'Report from ' . $startDate . ' to ' . $endDate);

    // Production & Supply Records
    $sheet->setCellValue('A3', 'Production & Supply Records');
    $sheet->setCellValue('A4', 'Date');
    $sheet->setCellValue('B4', 'Small Boxes');
    $sheet->setCellValue('C4', 'Big Boxes');
    $sheet->setCellValue('D4', 'Balance Small');
    $sheet->setCellValue('E4', 'Balance Big');

    $row = 5;
    foreach ($productionData as $data) {
        $sheet->setCellValue('A' . $row, $data['date']);
        $sheet->setCellValue('B' . $row, $data['small_boxes']);
        $sheet->setCellValue('C' . $row, $data['big_boxes']);
        $sheet->setCellValue('D' . $row, $data['balance_stock_small']);
        $sheet->setCellValue('E' . $row, $data['balance_stock_big']);
        $row++;
    }

    // Finance Records
    $row += 2;
    $sheet->setCellValue('A' . $row, 'Finance Records');
    $sheet->setCellValue('A' . ($row + 1), 'Date');
    $sheet->setCellValue('B' . ($row + 1), 'Amount Received');
    $sheet->setCellValue('C' . ($row + 1), 'Balance');

    $row += 2;
    foreach ($financeData as $data) {
        $sheet->setCellValue('A' . $row, $data['payment_date']);
        $sheet->setCellValue('B' . $row, $data['payment_received']);
        $sheet->setCellValue('C' . $row, $data['balance']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report_' . $startDate . '_to_' . $endDate . '.xlsx"');
    $writer->save('php://output');
}

// Function to generate Word
function generateWord($startDate, $endDate, $productionData, $financeData) {
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();

    // Title
    $section->addText('Report from ' . $startDate . ' to ' . $endDate, ['bold' => true, 'size' => 16]);

    // Production & Supply Records
    $section->addText('Production & Supply Records', ['bold' => true, 'size' => 14]);
    foreach ($productionData as $data) {
        $section->addText('Date: ' . $data['date'] . ', Small Boxes: ' . $data['small_boxes'] . ', Big Boxes: ' . $data['big_boxes'] . ', Balance Small: ' . $data['balance_stock_small'] . ', Balance Big: ' . $data['balance_stock_big']);
    }

    // Finance Records
    $section->addText('Finance Records', ['bold' => true, 'size' => 14]);
    foreach ($financeData as $data) {
        $section->addText('Date: ' . $data['payment_date'] . ', Amount Received: ' . $data['payment_received'] . ', Balance: ' . $data['balance']);
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="report_' . $startDate . '_to_' . $endDate . '.docx"');
    $phpWord->save('php://output');
}

// Handle report generation based on user input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $format = $_POST['format'];

    // Filter production and finance data by date range
    $filteredProductionData = array_filter($productionData, function($data) use ($startDate, $endDate) {
        return ($data['date'] >= $startDate && $data['date'] <= $endDate);
    });

    $filteredFinanceData = array_filter($financeData, function($data) use ($startDate, $endDate) {
        return ($data['payment_date'] >= $startDate && $data['payment_date'] <= $endDate);
    });

    // Generate the selected format
    if ($format === 'pdf') {
        generatePDF($startDate, $endDate, $filteredProductionData, $filteredFinanceData);
    } elseif ($format === 'excel') {
        generateExcel($startDate, $endDate, $filteredProductionData, $filteredFinanceData);
    } elseif ($format === 'word') {
        generateWord($startDate, $endDate, $filteredProductionData, $filteredFinanceData);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Trello Style</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="production.php">Production</a></li>
                <li><a href="supplies.php">Supplies</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h1>Generate Reports</h1>

            <form action="reports.php" method="POST">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>

                <label for="format">Select Format:</label>
                <select id="format" name="format" required>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                    <option value="word">Word</option>
                </select>

                <input type="submit" value="Generate Report">
            </form>
        </div>
    </div>

</body>
</html>
