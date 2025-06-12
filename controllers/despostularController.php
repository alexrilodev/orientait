<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && $_SESSION['tipo_usuario'] === 'candidato') {
    $usuario_id = $_SESSION['id_usuario'];
    $oferta_id = $_POST['oferta_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM postulaciones WHERE usuario_id = :usuario_id AND oferta_id = :oferta_id");
        $stmt->execute([':usuario_id' => $usuario_id, ':oferta_id' => $oferta_id]);

        echo json_encode(['status' => 'success']);
        exit();

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Acción no permitida.']);
    exit();
}
