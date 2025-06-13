<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

if ($_SESSION['tipo_usuario'] !== 'admin') {
    http_response_code(403);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
  case 'create':
  case 'update':
    $titulo      = $_POST['titulo'];
    $tipo        = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];
    $file_path   = '';

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES['pdf']['tmp_name'];
        $name = uniqid() . '.pdf';
        move_uploaded_file($tmp, UPLOAD_DIR . 'recursos/' . $name);
        $file_path = UPLOAD_URL . 'recursos/' . $name;
    }

    if ($action === 'create') {
        $sql = "INSERT INTO recursos (titulo, tipo, descripcion, file_path)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$titulo, $tipo, $descripcion, $file_path]);
    } else {
        $id = $_POST['id'];
        $stmt = $conn->prepare("SELECT file_path FROM recursos WHERE id = ?");
        $stmt->execute([$id]);
        $file_path_actual = $stmt->fetchColumn();

        if (!$file_path) {
            $file_path = $file_path_actual;
        } else {
            // Si se subió nuevo PDF elimina el anterior
            if ($file_path_actual) {
                $rutaArchivo = UPLOAD_DIR . 'recursos/' . basename($file_path_actual);
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
            }
        }

        $sql = "UPDATE recursos
                SET titulo=?, tipo=?, descripcion=?, file_path=?
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$titulo, $tipo, $descripcion, $file_path, $id]);
    }

    header('Location: ' . BASE_URL . 'pages/admin/recursos.php');
    break;

  case 'delete':
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT file_path FROM recursos WHERE id = ?");
    $stmt->execute([$id]);
    $file_path = $stmt->fetchColumn();

    if ($file_path) {
        $rutaArchivo = UPLOAD_DIR . 'recursos/' . basename($file_path);
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
    }

    // Elimina el recurso en la bbdd
    $stmt = $conn->prepare("DELETE FROM recursos WHERE id=?");
    $stmt->execute([$id]);

    header('Location: ' . BASE_URL . 'pages/admin/recursos.php');
    break;

  default:
    header('Location: ' . BASE_URL . 'pages/admin/recursos.php');
}
?>
