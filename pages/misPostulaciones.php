<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database/db.php';

// Acceso solo candidatos
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'candidato') {
    header("Location: ../index.php");
    exit();
}

// Filtros
$busqueda    = $_GET['busqueda']    ?? '';
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';
$empresa     = $_GET['usuario']     ?? '';

// Paginación
$por_pagina    = 5;
$pagina_actual = max(1, intval($_GET['pagina'] ?? 1));
$offset        = ($pagina_actual - 1) * $por_pagina;

// Conteo total con filtros
$sqlCount  = "
    SELECT COUNT(*) 
    FROM postulaciones p
    JOIN ofertas o ON p.oferta_id = o.id
    WHERE p.usuario_id = :uid
";
$params    = [':uid' => $_SESSION['id_usuario']];

if ($busqueda !== '') {
    $sqlCount .= " AND (o.titulo LIKE :busqueda OR o.descripcion LIKE :busqueda)";
    $params[':busqueda'] = "%{$busqueda}%";
}
if ($fecha_desde !== '') {
    $sqlCount .= " AND o.fecha_publicacion >= :desde";
    $params[':desde'] = $fecha_desde;
}
if ($fecha_hasta !== '') {
    $sqlCount .= " AND o.fecha_publicacion <= :hasta";
    $params[':hasta'] = $fecha_hasta;
}
if ($empresa !== '') {
    $sqlCount .= " AND o.usuario_id = :empresa";
    $params[':empresa'] = $empresa;
}

$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute($params);
$total_postulaciones = (int)$stmtCount->fetchColumn();
$total_paginas       = (int)ceil($total_postulaciones / $por_pagina);

// Consulta paginada con mismos filtros
$sql  = "
    SELECT p.id, o.titulo, o.descripcion, o.fecha_publicacion, u.nombre AS nombre_empresa
    FROM postulaciones p
    JOIN ofertas o  ON p.oferta_id = o.id
    JOIN usuarios u ON o.usuario_id = u.id
    WHERE p.usuario_id = :uid
";
$params[':uid'] = $_SESSION['id_usuario'];

if ($busqueda !== '') {
    $sql .= " AND (o.titulo LIKE :busqueda OR o.descripcion LIKE :busqueda)";
}
if ($fecha_desde !== '') {
    $sql .= " AND o.fecha_publicacion >= :desde";
}
if ($fecha_hasta !== '') {
    $sql .= " AND o.fecha_publicacion <= :hasta";
}
if ($empresa !== '') {
    $sql .= " AND o.usuario_id = :empresa";
}

$sql .= " ORDER BY p.fecha_postulacion DESC
          LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
foreach ($params as $key => $val) {
    $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key, $val, $type);
}
$stmt->bindValue(':limit',  $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
$stmt->execute();

$postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
  <h1 class="mb-4">Mis Inscripciones</h1>

  <form method="GET" action="misPostulaciones.php" class="row g-3 mb-4 align-items-end">
    <div class="col-md-3">
      <label class="form-label">Búsqueda:</label>
      <input
        type="text"
        name="busqueda"
        class="form-control"
        placeholder="Título o descripción"
        value="<?= htmlspecialchars($busqueda) ?>"
      >
    </div>
    <div class="col-md-2">
      <label class="form-label">Desde:</label>
      <input
        type="date"
        name="fecha_desde"
        class="form-control"
        value="<?= htmlspecialchars($fecha_desde) ?>"
      >
    </div>
    <div class="col-md-2">
      <label class="form-label">Hasta:</label>
      <input
        type="date"
        name="fecha_hasta"
        class="form-control"
        value="<?= htmlspecialchars($fecha_hasta) ?>"
      >
    </div>
    <div class="col-md-3">
      <label class="form-label">Empresas:</label>
      <select name="usuario" class="form-select">
        <option value="">Todas</option>
        <?php
        $stmtE = $conn->query("SELECT id, nombre FROM usuarios WHERE tipo_usuario='empresa' ORDER BY nombre");
        while ($e = $stmtE->fetch(PDO::FETCH_ASSOC)):
          $sel = $empresa == $e['id'] ? 'selected' : '';
        ?>
          <option value="<?= $e['id'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($e['nombre']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Filtrar</button>
    </div>
  </form>

  <?php if (count($postulaciones) > 0): ?>
    <ul class="list-unstyled">
      <?php foreach ($postulaciones as $p): ?>
        <li class="mb-4 border-bottom pb-3">
          <h3><?= htmlspecialchars($p['titulo']) ?></h3>
          <p><?= nl2br(htmlspecialchars($p['descripcion'])) ?></p>
          <small>
            Publicado por <?= htmlspecialchars($p['nombre_empresa']) ?>
            el <?= date("d/m/Y", strtotime($p['fecha_publicacion'])) ?>
          </small>
        </li>
      <?php endforeach; ?>
    </ul>

    <nav>
      <ul class="pagination">
        <?php
        $baseQS = $_GET;
        if ($pagina_actual > 1):
          $baseQS['pagina'] = $pagina_actual - 1;
        ?>
          <li class="page-item">
            <a class="page-link" href="?<?= http_build_query($baseQS) ?>">
              « Anterior
            </a>
          </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++):
          $baseQS['pagina'] = $i;
          $active = $i === $pagina_actual ? ' active' : '';
        ?>
          <li class="page-item<?= $active ?>">
            <a class="page-link" href="?<?= http_build_query($baseQS) ?>">
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>

        <?php
        if ($pagina_actual < $total_paginas):
          $baseQS['pagina'] = $pagina_actual + 1;
        ?>
          <li class="page-item">
            <a class="page-link" href="?<?= http_build_query($baseQS) ?>">
              Siguiente »
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php else: ?>
    <p>No te has inscrito a ninguna oferta aún.</p>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
