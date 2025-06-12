<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

// Verificar user tipo empresa
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'empresa') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ofertas.php");
    exit();
}

$id = $_GET['id'];

// Obtener la oferta
$stmt = $conn->prepare("SELECT * FROM ofertas WHERE id = ?");
$stmt->execute([$id]);
$oferta = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar que la oferta es del user actual
if (!$oferta || $oferta['usuario_id'] != $_SESSION['id_usuario']) {
    header("Location: ofertas.php");
    exit();
}

include '../includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Editar Oferta</h1>
    <form action="../controllers/editarOfertaController.php" method="POST" class="w-50 mx-auto">
        <input type="hidden" name="id" value="<?= htmlspecialchars($oferta['id']) ?>">
        
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input 
              type="text" 
              name="titulo"
              class="form-control"
              value="<?= htmlspecialchars($oferta['titulo']) ?>"
              required
            >
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea 
              name="descripcion"
              rows="5"
              class="form-control"
              required
            ><?= htmlspecialchars($oferta['descripcion']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            Guardar Cambios
        </button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
