<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

// Solo si hay usuario logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'];

try {
    // Eliminar datos relacionados primero
    if ($tipo_usuario === 'candidato') {
        // Inscripciones candidato
        $stmt = $conn->prepare("DELETE FROM postulaciones WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);

        // Citas candidato
        $stmt = $conn->prepare("DELETE FROM citas WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        
        // Registros en notificaciones_globales_leidas
        $stmt = $conn->prepare("DELETE FROM notificaciones_globales_leidas WHERE id_usuario = ?");
        $stmt->execute([$usuario_id]);

        // PDF CV si existe
        $cvPath = __DIR__ . '/../uploads/cv/' . $usuario_id . '.pdf';
        if (file_exists($cvPath)) {
            unlink($cvPath);
        }
    } elseif ($tipo_usuario === 'empresa') {
        // Eliminar ofertas publicadas por empresa
        $stmt = $conn->prepare("DELETE FROM ofertas WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);

        // Eliminar notificaciones relacionadas
        $stmt = $conn->prepare("DELETE FROM notificaciones WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
    }

    // Eliminar cuenta usuario
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);

    session_destroy();

    header("Location: ../index.php");
    exit();

} catch (PDOException $e) {
    echo "Error al eliminar la cuenta: " . $e->getMessage();
    exit();
}
?>
