<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}
include __DIR__ . '/../includes/header.php';

$tipo_usuario = $_SESSION['tipo_usuario'];
$usuario_id   = $_SESSION['id_usuario'];
?>
<div class="container my-5">
  <h1 class="mb-4">Panel de Control</h1>

  <?php if ($tipo_usuario === 'candidato'): ?>
    <h2 class="mb-3">Resumen de Actividad</h2>
    <?php
      $stmt = $conn->prepare("SELECT COUNT(*) FROM postulaciones WHERE usuario_id = ?");
      $stmt->execute([$usuario_id]);
      $total = $stmt->fetchColumn();
    ?>
    <p>Te has inscrito a <strong><?= $total ?></strong> ofertas.</p>
    <p><a href="misPostulaciones.php" class="btn btn-primary">Ver mis inscripciones</a></p>

    <?php
      // Mostrar cita si existe
      $stmt = $conn->prepare("SELECT fecha, hora FROM citas WHERE usuario_id = ?");
      $stmt->execute([$usuario_id]);
      $c = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <?php if ($c): ?>
      <div class="alert alert-info">
        📅 Tienes una cita el 
        <strong><?= date("d/m/Y H:i", strtotime($c['fecha'].' '.$c['hora'])) ?></strong>.
        <button class="btn btn-danger btn-sm ms-3 cancelarCita" data-fecha="<?= $c['fecha'] ?>" data-hora="<?= $c['hora'] ?>">
            Cancelar cita
        </button>
      </div>
    <?php else: ?>
      <p>No tienes cita de asesoría. 
        <a href="<?= BASE_URL ?>pages/recursos.php#selectorCita">¡Reserva aquí!</a>
      </p>
    <?php endif; ?>

    <?php elseif ($tipo_usuario === 'empresa'): ?>
        <h2 class="mb-3">Resumen de Actividad</h2>
        <?php
        // Ofertas publicadas
        $stmt = $conn->prepare("SELECT COUNT(*) FROM ofertas WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $total_ofertas = $stmt->fetchColumn();

        // Total de inscripciones recibidas
        $stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM postulaciones p
            JOIN ofertas o ON p.oferta_id = o.id
            WHERE o.usuario_id = ?
        ");
        $stmt->execute([$usuario_id]);
        $total_postulaciones = $stmt->fetchColumn();
        ?>
        <p>Has publicado <strong><?= $total_ofertas ?></strong> ofertas.</p>
        <p>Has recibido <strong><?= $total_postulaciones ?></strong> inscripciones.</p>
        <p>
            <a href="ofertas.php" class="btn btn-primary">
                Ver mis ofertas
            </a>
        </p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
