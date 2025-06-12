<?php
session_start();
require_once __DIR__ . '/../../config.php';
if ($_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ' . BASE_URL);
    exit;
}
require_once __DIR__ . '/../../database/db.php';
include __DIR__ . '/../../includes/header.php';

$id = $_GET['id'] ?? null;
$titulo = $tipo = $descripcion = '';
$file_path = '';
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM recursos WHERE id = ?");
    $stmt->execute([$id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $titulo      = $r['titulo'];
    $tipo        = $r['tipo'];
    $descripcion = $r['descripcion'];
    $file_path   = $r['file_path'];
}
?>
<div class="container my-5">
  <h1><?= $id ? 'Editar' : 'Nuevo' ?> Recurso</h1>
  <form action="<?= BASE_URL ?>controllers/recursosController.php?action=<?= $id?'update':'create' ?>"
        method="POST" enctype="multipart/form-data" class="w-50 mx-auto">
    <?php if ($id): ?>
      <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Título:</label>
      <input name="titulo" class="form-control" required value="<?= htmlspecialchars($titulo) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Tipo:</label>
      <select name="tipo" class="form-select" required>
        <option value="pdf" <?= $tipo==='pdf'?'selected':'' ?>>PDF</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción:</label>
      <textarea name="descripcion" class="form-control" rows="4" required><?= htmlspecialchars($descripcion) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Archivo PDF:</label>
      <input type="file" name="pdf" accept="application/pdf" class="form-control" <?= $id?'':'required' ?>>
      <?php if ($file_path): ?>
        <p class="mt-2">
          <a href="<?= $file_path ?>" target="_blank">Ver archivo actual</a>
        </p>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-purple">
      <?= $id ? 'Actualizar' : 'Crear' ?>
    </button>
    <a href="recursos.php" class="btn btn-secondary ms-2">Cancelar</a>
  </form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
