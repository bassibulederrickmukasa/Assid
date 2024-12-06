<?php
$host = 'localhost';
$db = 'inventory_system'; // Replace with your actual database name
$user = 'root'; // Replace with your actual database username
$pass = ''; // Replace with your actual database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
