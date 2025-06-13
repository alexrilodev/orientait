<?php
$host = "sql206.infinityfree.com";
$dbname = "if0_39217578_orientait";
$username = "if0_39217578";
$password = "Nande1424000";

try {
    $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
