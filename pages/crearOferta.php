<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

// Solo permite acceso empresa
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'empresa') {
    header("Location: ../pages/ofertas.php");
    exit();
}

include '../includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Publicar Oferta de Empleo</h1>
    <form id="formOferta" action="../controllers/ofertasController.php" method="POST" class="w-50 mx-auto">
        <div class="mb-3">
            <label for="tituloOferta" class="form-label">Título de la Oferta:</label>
            <input 
              type="text" 
              id="tituloOferta" 
              name="titulo" 
              class="form-control" 
              required
            >
        </div>
        <div class="mb-3">
            <label for="descripcionOferta" class="form-label">Descripción:</label>
            <textarea 
              id="descripcionOferta" 
              name="descripcion" 
              rows="5"
              class="form-control"
              required
            ></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Publicar Oferta</button>
    </form>
    <p class="mt-3">
        <a href="<?= BASE_URL ?>pages/ofertas.php">Volver a Ofertas</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
