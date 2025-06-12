<?php
require_once __DIR__ . '/../config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && $_SESSION['tipo_usuario'] === 'empresa') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $usuario_id = $_SESSION['id_usuario'];

    try {
        // Nueva oferta en la tabla ofertas
        $stmt = $conn->prepare("INSERT INTO ofertas (titulo, descripcion, fecha_publicacion, usuario_id) VALUES (?, ?, NOW(), ?)");
        $stmt->execute([$titulo, $descripcion, $usuario_id]);

        $nombre_empresa = $_SESSION['usuario'];

        // Notificación "global" para candidatos
        $mensaje = "Nueva oferta publicada por $nombre_empresa: \"$titulo\".";

        $stmtGlob = $conn->prepare("
            INSERT INTO notificaciones_globales (mensaje, tipo)
            VALUES (:mensaje, 'general')
        ");
        $stmtGlob->execute([':mensaje' => $mensaje]);

        header('Location: ../pages/ofertas.php');
        exit();
    } catch (PDOException $e) {
        echo "Error al crear la oferta: " . $e->getMessage();
    }
} else {
    echo "Acceso denegado";
}
?>
