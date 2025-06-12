<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'candidato') {
    http_response_code(403);
    exit;
}

$action = $_GET['action'] ?? '';
header('Content-Type: application/json');

switch ($action) {
    case 'slots':
        // Traer solo las horas ocupadas
        $slots = $conn->query("SELECT DISTINCT hora FROM citas WHERE CONCAT(fecha, ' ', hora) >= NOW()")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($slots);
        break;

    case 'book':
        $fh = $_POST['fecha_hora'] ?? '';
        if (!$fh) {
            echo json_encode(['error' => 'Fecha u hora inválida']);
            exit;
        }

        list($fecha, $hora) = explode(' ', $fh);
        $dow = date('N', strtotime($fecha));

        if ($dow > 5) {
            echo json_encode(['error' => 'Solo Lunes–Viernes']);
            exit;
        }

        $h = (int) substr($hora, 0, 2);
        if (!((8 <= $h && $h < 14) || (15 <= $h && $h < 17)) || substr($hora, 3) !== '00') {
            echo json_encode(['error' => 'Hora no permitida']);
            exit;
        }

        // Verificar disponibilidad de slot
        $stmt = $conn->prepare("SELECT COUNT(*) FROM citas WHERE fecha = ? AND hora = ?");
        $stmt->execute([$fecha, $hora]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['error' => 'Sesión no disponible']);
            exit;
        }

        // Verificar que el usuario no tenga otra cita
        $stmt = $conn->prepare("SELECT COUNT(*) FROM citas WHERE usuario_id = ? AND CONCAT(fecha, ' ', hora) >= NOW()");
        $stmt->execute([$_SESSION['id_usuario']]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['error' => 'Ya tienes una cita reservada']);
            exit;
        }

        // Validar que el usuario tenga CV y teléfono
        $stmt = $conn->prepare("SELECT cv_path, telefono FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['id_usuario']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($usuario['cv_path']) || empty($usuario['telefono'])) {
            echo json_encode(['error' => 'Debes subir tu CV y completar tu teléfono antes de reservar una cita.']);
            exit;
        }

        // Insertar cita
        $ins = $conn->prepare("INSERT INTO citas (usuario_id, fecha, hora) VALUES (?, ?, ?)");
        $ins->execute([$_SESSION['id_usuario'], $fecha, $hora]);

        // Notificar al admin
        $adminId = $conn->query("SELECT id FROM usuarios WHERE tipo_usuario = 'admin' LIMIT 1")->fetchColumn();

        $cv_url = BASE_URL . "uploads/cv/{$_SESSION['id_usuario']}.pdf";
        $stmtNotif = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje, tipo) VALUES (?, ?, 'admin')");
        $stmtNotif->execute([
            $adminId,
            "Cita reservada por usuario {$_SESSION['usuario']} para la fecha: $fecha a las $hora. <a href='{$cv_url}' target='_blank'><i class='bi bi-file-earmark-pdf-fill'></i> Ver CV</a>"
        ]);       

        echo json_encode(['success' => 'Cita reservada']);
        break;

    case 'cancel':
        $fecha = $_POST['fecha'] ?? '';
        $hora = $_POST['hora'] ?? '';

        $stmt = $conn->prepare("DELETE FROM citas WHERE usuario_id = ? AND fecha = ? AND hora = ?");
        if ($stmt->execute([$_SESSION['id_usuario'], $fecha, $hora])) {
            // Notificar al admin
            $adminId = $conn->query("SELECT id FROM usuarios WHERE tipo_usuario = 'admin' LIMIT 1")->fetchColumn();

            $stmtNotif = $conn->prepare("INSERT INTO notificaciones (usuario_id, mensaje, tipo) VALUES (?, ?, 'admin')");
            $stmtNotif->execute([
                $adminId,
                "Cita cancelada por usuario {$_SESSION['usuario']} para la fecha: $fecha a las $hora."
            ]);

            echo json_encode(['success' => 'Cita cancelada']);
        } else {
            echo json_encode(['error' => 'Error al cancelar la cita']);
        }
        break;

    default:
        echo json_encode(['error' => 'Acción desconocida']);
        break;
}
