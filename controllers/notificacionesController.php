<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit;
}

$usuario_id = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'];

try {
    // Define tipo de notificaciones que ve cada usuario
    $tipo_notificacion = ($tipo_usuario === 'empresa') ? 'postulacion' : 'general';

    // Obtener notificaciones no leídas
    $stmt = $conn->prepare("SELECT id, mensaje, fecha FROM notificaciones 
                            WHERE usuario_id = :usuario_id 
                            AND tipo = :tipo 
                            AND leido = FALSE 
                            ORDER BY fecha DESC");
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':tipo' => $tipo_notificacion
    ]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Para las notis de Admin
    if ($tipo_usuario === 'admin') {
        $stmtUpdate = $conn->prepare("UPDATE notificaciones 
                                      SET leido = 1 
                                      WHERE usuario_id = :usuario_id 
                                      AND leido = 0 
                                      AND tipo = 'admin'");
        $stmtUpdate->execute([':usuario_id' => $usuario_id]);
    }

    echo json_encode(['status' => 'success', 'notificaciones' => $notificaciones]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al obtener notificaciones']);
}
?>
