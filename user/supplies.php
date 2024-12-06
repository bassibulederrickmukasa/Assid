<?php
include '../includes/auth.php';
checkAuth('user');
include '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $box_type = $_POST['box_type'];
    $supply_amount = $_POST['supply_amount'];
    $personnel = $_SESSION['username']; // Get the logged-in user as personnel
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO supplies_data (personnel, box_type, supply_amount, date)
                            VALUES (:personnel, :box_type, :supply_amount, :date)");
    $stmt->bindParam(':personnel', $personnel);
    $stmt->bindParam(':box_type', $box_type);
    $stmt->bindParam(':supply_amount', $supply_amount);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    echo "Supply data recorded!";
}
?>

<form method="POST" action="">
    <select name="box_type">
        <option value="small">Small</option>
        <option value="big">Big</option>
    </select>
    <input type="number" name="supply_amount" placeholder="Supply Amount" required />
    <input type="date" name="date" required />
    <button type="submit">Record Supply</button>
</form>

<div class="content">
    <h1>Your Supplies</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Box Type</th>
                <th>Supply Amount</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $personnel = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT * FROM supplies_data WHERE personnel = :personnel");
        $stmt->bindParam(':personnel', $personnel);
        $stmt->execute();
        
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['box_type']}</td>
                    <td>{$row['supply_amount']}</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
