<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario']     = $usuario['nombre'];
        $_SESSION['id_usuario']  = $usuario['id'];
        $_SESSION['tipo_usuario']= $usuario['tipo_usuario'];

        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['login_error'] = 'Usuario o contraseña incorrectos';
        header('Location: ../pages/login.php');
        exit();
    }
}
