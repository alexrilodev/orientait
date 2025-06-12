<?php
$host = "sql206.infinityfree.com";
$dbname = "if0_39217578_orientait";
$username = "if0_39217578";
$password = "Nande1424000";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>