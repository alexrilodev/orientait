<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && $_SESSION['tipo_usuario'] === 'empresa') {
    $id = $_POST['id_oferta'];
    $usuario_id = $_SESSION['id_usuario'];

    try {
        // Verificar que la oferta pertenece al usuario actual
        $stmt = $conn->prepare("SELECT id FROM ofertas WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $usuario_id]);
        $oferta = $stmt->fetch();

        if ($oferta) {
            // Eliminar la oferta
            $stmt = $conn->prepare("DELETE FROM ofertas WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: ../pages/ofertas.php");
            exit();
        } else {
            echo "No tienes permiso para eliminar esta oferta.";
        }
    } catch (PDOException $e) {
        echo "Error al eliminar la oferta: " . $e->getMessage();
    }
} else {
    echo "Acceso denegado.";
}
?>
