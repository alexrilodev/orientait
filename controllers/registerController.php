<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = htmlspecialchars($_POST['email']);
    $tipo_usuario = $_POST['tipo_usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password, tipo_usuario) VALUES (:nombre, :email, :password, :tipo_usuario)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':tipo_usuario', $tipo_usuario);

    if ($stmt->execute()) {
        header("Location: ../pages/login.php");
        exit();
    } else {
        echo "Error al registrar usuario.";
    }
}
?>
