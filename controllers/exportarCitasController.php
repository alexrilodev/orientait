<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

// Solo permitir acceso a Admin
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ' . BASE_URL);
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=citas_reservadas.csv');

$output = fopen('php://output', 'w');

// Configurar delimitador personalizado
$delimiter = ';';

// Encabezados
fputcsv($output, ['Nombre del Usuario', 'Email del Usuario', 'Telefono del Usuario', 'Fecha de la cita', 'Hora de la cita'], $delimiter);

// Datos
$stmt = $conn->query("
    SELECT u.nombre, u.email, u.telefono, c.fecha, c.hora
    FROM citas c
    JOIN usuarios u ON c.usuario_id = u.id
    ORDER BY c.fecha ASC, c.hora ASC
");

$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($citas as $cita) {
    fputcsv($output, [
        $cita['nombre'],
        $cita['email'],
        $cita['telefono'],
        $cita['fecha'],
        $cita['hora']
    ], $delimiter);
}

fclose($output);
exit();
?>