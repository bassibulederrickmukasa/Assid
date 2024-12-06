<?php
include '../includes/auth.php';
checkAuth('user');
include '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment = $_POST['payment'];
    $balance = $_POST['balance'];
    $box_type = $_POST['box_type'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO payments (payment_amount, payment_balance, box_type, date)
                            VALUES (:payment, :balance, :box_type, :date)");
    $stmt->bindParam(':payment', $payment);
    $stmt->bindParam(':balance', $balance);
    $stmt->bindParam(':box_type', $box_type);
    $stmt->bindParam(':date', $date);
    $stmt->execute();

    echo "Payment recorded!";
}
?>

<form method="POST" action="">
    <input type="number" name="payment" placeholder="Payment" required />
    <input type="number" name="balance" placeholder="Balance" required />
    <select name="box_type">
        <option value="small">Small</option>
        <option value="big">Big</option>
    </select>
    <input type="date" name="date" required />
    <button type="submit">Record Payment</button>
</form>

<div class="content">
    <h1>Your Payments</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Payment</th>
                <th>Balance</th>
                <th>Box Type</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $personnel = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT * FROM payments WHERE personnel = :personnel");
        $stmt->bindParam(':personnel', $personnel);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$row['payment_amount']}</td>
                    <td>{$row['payment_balance']}</td>
                    <td>{$row['box_type']}</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
