<div class="sidebar">
    <ul>
        <li><a href="../index.php">Home</a></li>
        <li><a href="../logout.php">Logout</a></li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="dashboard.php">Admin Dashboard</a></li>
            <li><a href="supplies.php">Manage Supplies</a></li>
            <li><a href="payments.php">Manage Payments</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logs.php">Logs</a></li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
            <li><a href="dashboard.php">User Dashboard</a></li>
            <li><a href="supplies.php">View Supplies</a></li>
            <li><a href="payments.php">View Payments</a></li>
        <?php else: ?>
            <!-- Default links if no role is set -->
            <li><a href="dashboard.php">Dashboard</a></li>
        <?php endif; ?>
    </ul>
</div>
