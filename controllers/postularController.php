<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['usuario']) && $_SESSION['tipo_usuario'] === 'candidato') {
    $usuario_id = $_SESSION['id_usuario'];
    $nombre_postulante = $_SESSION['usuario'];
    $oferta_id = $_POST['oferta_id'];

    try {
        // Verificar si ya está inscrito
        $stmt = $conn->prepare("SELECT 1 FROM postulaciones WHERE usuario_id = ? AND oferta_id = ?");
        $stmt->execute([$usuario_id, $oferta_id]);
        $yaPostulado = $stmt->fetchColumn();

        if ($yaPostulado) {
            echo "Ya te has inscrito a esta oferta.";
        } else {
            // Insertar postulación
            $stmt = $conn->prepare("INSERT INTO postulaciones (usuario_id, oferta_id) VALUES (?, ?)");
            $stmt->execute([$usuario_id, $oferta_id]);

            // Obtener info de la oferta
            $stmt = $conn->prepare("
                SELECT o.titulo, o.usuario_id AS autor_id
                FROM ofertas o
                WHERE o.id = ?
            ");
            $stmt->execute([$oferta_id]);
            $oferta = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($oferta) {
                $autor_id = $oferta['autor_id'];
                $titulo_oferta = $oferta['titulo'];

                // Crea notif para empresa
                $mensaje = "El usuario $nombre_postulante se ha inscrito a tu oferta \"$titulo_oferta\".";
                $stmt = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje, tipo) VALUES (?, ?, 'postulacion')");
                $stmt->execute([$autor_id, $mensaje]);
            }

            header('Location: ../pages/ofertas.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Error al procesar la inscripción: " . $e->getMessage();
    }
} else {
    echo "Acción no permitida.";
}
?>
