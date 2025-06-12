<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$nombre = trim($_POST['nombre']);
$email = trim($_POST['email']);
$telefono = trim($_POST['telefono']);
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

try {
    if ($password) {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono, password = :password WHERE id = :id";
        $params = [':nombre' => $nombre, ':email' => $email, ':telefono' => $telefono, ':password' => $password, ':id' => $usuario_id];
    } else {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, telefono = :telefono WHERE id = :id";
        $params = [':nombre' => $nombre, ':email' => $email, ':telefono' => $telefono, ':id' => $usuario_id];
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $_SESSION['usuario'] = $nombre;

    // Subida de CV para usuarios tipo candidato
    if (
        $_SESSION['tipo_usuario'] === 'candidato' &&
        isset($_FILES['cv']) &&
        $_FILES['cv']['error'] === UPLOAD_ERR_OK
    ) {
        $archivo_tmp = $_FILES['cv']['tmp_name'];
        $tipo_mime = mime_content_type($archivo_tmp);

        if ($tipo_mime === 'application/pdf') {
            $nombre_archivo = $usuario_id . ".pdf";
            $destino = __DIR__ . '/../uploads/cv/' . $nombre_archivo;

            // Eliminar el archivo anterior si existe
            if (file_exists($destino)) {
                unlink($destino);
            }

            // Subir nuevo CV
            if (move_uploaded_file($archivo_tmp, $destino)) {
                $stmt = $conn->prepare("UPDATE usuarios SET cv_path = :cv_path WHERE id = :id");
                $stmt->execute([
                    ':cv_path' => $nombre_archivo,
                    ':id' => $usuario_id
                ]);
            } else {
                echo "No se pudo guardar el archivo.";
                exit();
            }
        } else {
            echo "El archivo debe ser un PDF válido.";
            exit();
        }
    }

    header("Location: ../pages/perfil.php");
    exit();
} catch (PDOException $e) {
    echo "Error al actualizar el perfil: " . $e->getMessage();
}
?>
