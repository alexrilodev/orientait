<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calcula notis pendientes si hay usuario logueado
$notificaciones_pendientes = 0;
if (isset($_SESSION['usuario'])) {
    $usuario_id    = $_SESSION['id_usuario'];
    $tipo_usuario  = $_SESSION['tipo_usuario'] ?? '';

    try {
        if ($tipo_usuario === 'admin') {
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM notificaciones 
                WHERE usuario_id = :id 
                  AND leido = 0 
                  AND tipo = 'admin'");
            $stmt->execute([':id' => $usuario_id]);
            $notificaciones_pendientes = (int)$stmt->fetchColumn();
        } elseif ($tipo_usuario === 'empresa') {
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM notificaciones
                WHERE usuario_id = :id
                  AND leido = 0
                  AND tipo = 'postulacion'
            ");
            $stmt->execute([':id' => $usuario_id]);
            $notificaciones_pendientes = (int)$stmt->fetchColumn();

        } elseif ($tipo_usuario === 'candidato') {
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM notificaciones_globales ng
                LEFT JOIN notificaciones_globales_leidas nl
                       ON ng.id = nl.id_notificacion
                      AND nl.id_usuario = :usuario_id
                WHERE ng.tipo = 'general'
                  AND IFNULL(nl.leido, 0) = 0
            ");
            $stmt->execute([':usuario_id' => $usuario_id]);
            $notificaciones_pendientes = (int)$stmt->fetchColumn();
        }
    } catch (PDOException $e) {
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>IT Juniors</title>
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
  >
  <link  
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    >
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a class="navbar-brand font-bold" href="<?= BASE_URL ?>index.php">
        IT Juniors
      </a>
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          <?php if (!isset($_SESSION['usuario'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>pages/register.php">Registro</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= BASE_URL ?>pages/login.php">Login</a>
            </li>

          <?php else: ?>

            <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/perfil.php">Mi perfil</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/admin/recursos.php">Gestión de Recursos</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>controllers/exportarCitasController.php">Consultar Citas</a>
              </li>

            <?php elseif ($_SESSION['tipo_usuario'] === 'empresa'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/dashboard.php">Panel de Control</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/perfil.php">Mi perfil</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/ofertas.php">Ofertas de Empleo</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/crearOferta.php">Publicar Oferta</a>
              </li>

            <?php elseif ($_SESSION['tipo_usuario'] === 'candidato'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/dashboard.php">Panel de Control</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/perfil.php">Mi perfil</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/ofertas.php">Ofertas de Empleo</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/misPostulaciones.php">Mis inscripciones</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>pages/recursos.php">Recursos</a>
              </li>
            <?php endif; ?>

            <li class="nav-item position-relative">
              <a class="nav-link" href="<?= BASE_URL ?>pages/notificaciones.php">
                <i class="bi bi-bell"></i>
                <?php if ($notificaciones_pendientes > 0): ?>
                  <span 
                    class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                  >
                    <?= $notificaciones_pendientes ?>
                  </span>
                <?php endif; ?>
              </a>
            </li>

            <li class="nav-item">
              <a 
                class="nav-link text-danger" 
                href="<?= BASE_URL ?>controllers/logoutController.php"
              >
                Cerrar sesión
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </nav>
</header>
