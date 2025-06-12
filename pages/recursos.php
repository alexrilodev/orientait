<?php
session_start();
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../database/db.php';
include __DIR__ . '/../includes/header.php';

$stmt = $conn->query("SELECT * FROM recursos ORDER BY fecha_subida DESC");
$recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container my-5">
  <h1 class="mb-4">Recursos para Candidatos</h1>
  <div class="row">
    <?php foreach ($recursos as $r): ?>
    <div class="col-md-4 mb-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($r['titulo']) ?></h5>
          <p class="card-text"><?= nl2br(htmlspecialchars($r['descripcion'])) ?></p>
          <a href="<?= $r['file_path'] ?>" target="_blank" class="btn btn-purple">
            Abrir
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <hr>
  <h2>Asesoría personalizada</h2>
  <p>Elige día y hora para una sesión de 1 hora:</p>
  <input id="selectorCita" class="form-control w-25 d-inline-block me-2" readonly>
  <button id="btnReservar" class="btn btn-purple">Reservar cita</button>
  <div id="statusCita" class="mt-3"></div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="<?= BASE_URL ?>assets/js/citas.js"></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
