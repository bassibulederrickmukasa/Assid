<?php
include '../includes/auth.php';
checkAuth('admin');
include '../includes/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="content">
    <h1>Logs</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Action</th>
                <th>Date/Time</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $stmt = $conn->query("SELECT * FROM logs");
        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>{$row['user_id']}</td>
                    <td>{$row['action']}</td>
                    <td>{$row['date_time']}</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
