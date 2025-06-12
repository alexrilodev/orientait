<?php
session_start();
require_once __DIR__ . '/../../config.php';
if ($_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ' . BASE_URL);
    exit;
}
require_once __DIR__ . '/../../database/db.php';
include __DIR__ . '/../../includes/header.php';

$stmt = $conn->query("SELECT * FROM recursos ORDER BY fecha_subida DESC");
$recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container my-5">
  <h1 class="mb-4">Gestión de Recursos</h1>
  <a href="formrecurso.php" class="btn btn-purple mb-3">Nuevo Recurso</a>
  <table class="table table-dark">
    <thead>
      <tr>
        <th>Título</th>
        <th>Tipo</th>
        <th>Subido</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($recursos as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['titulo']) ?></td>
        <td><?= htmlspecialchars($r['tipo']) ?></td>
        <td><?= date('d/m/Y', strtotime($r['fecha_subida'])) ?></td>
        <td>
          <a href="formrecurso.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
          <a href="<?= BASE_URL ?>controllers/recursosController.php?action=delete&id=<?= $r['id'] ?>"
             class="btn btn-sm btn-danger"
             onclick="return confirm('¿Eliminar recurso?')"
          >Borrar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
