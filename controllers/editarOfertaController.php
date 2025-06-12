<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && $_SESSION['tipo_usuario'] === 'empresa') {
    $id = $_POST['id'];
    $titulo = htmlspecialchars($_POST['titulo']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $usuario_id = $_SESSION['id_usuario'];

    // Verificar que la oferta pertenece al usuario actual
    $stmt = $conn->prepare("SELECT id FROM ofertas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);
    $oferta = $stmt->fetch();

    if ($oferta) {
        // Actualizar oferta
        $stmt = $conn->prepare("UPDATE ofertas SET titulo = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([$titulo, $descripcion, $id]);

        header("Location: ../pages/ofertas.php");
        exit();
    } else {
        echo "No tienes permiso para editar esta oferta.";
    }
} else {
    echo "Acceso denegado.";
}
?>
