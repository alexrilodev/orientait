<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include '../includes/header.php';

$ofertas = [];
$por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

try {
    // Conteo total con los filtros incluidos
    $sqlTotal = "SELECT COUNT(*) FROM ofertas o WHERE 1=1";
    $paramsTotal = [];

    if ($_SESSION['tipo_usuario'] === 'empresa') {
        $sqlTotal .= " AND o.usuario_id = :usuario_id";
        $paramsTotal[':usuario_id'] = $_SESSION['id_usuario'];
    }

    if (!empty($_GET['busqueda'])) {
        $sqlTotal .= " AND (o.titulo LIKE :busqueda OR o.descripcion LIKE :busqueda)";
        $paramsTotal[':busqueda'] = '%' . $_GET['busqueda'] . '%';
    }

    if (!empty($_GET['fecha_desde'])) {
        $sqlTotal .= " AND o.fecha_publicacion >= :fecha_desde";
        $paramsTotal[':fecha_desde'] = $_GET['fecha_desde'];
    }

    if (!empty($_GET['fecha_hasta'])) {
        $sqlTotal .= " AND o.fecha_publicacion <= :fecha_hasta";
        $paramsTotal[':fecha_hasta'] = $_GET['fecha_hasta'];
    }

    if (!empty($_GET['usuario']) && $_SESSION['tipo_usuario'] !== 'empresa') {
        $sqlTotal .= " AND o.usuario_id = :usuario";
        $paramsTotal[':usuario'] = $_GET['usuario'];
    }

    $stmtTotal = $conn->prepare($sqlTotal);
    foreach ($paramsTotal as $key => &$value) {
        $stmtTotal->bindParam($key, $value);
    }
    $stmtTotal->execute();
    $total_ofertas = $stmtTotal->fetchColumn();
    $total_paginas = ceil($total_ofertas / $por_pagina);

    // Consulta principal
    $sql = "
        SELECT o.id, o.titulo, o.descripcion, o.fecha_publicacion, 
               o.usuario_id, u.nombre AS nombre_usuario,
               (SELECT COUNT(*) FROM postulaciones p WHERE p.oferta_id = o.id) AS num_postulaciones
        FROM ofertas o
        JOIN usuarios u ON o.usuario_id = u.id
        WHERE 1=1
    ";

    $params = [];

    if ($_SESSION['tipo_usuario'] === 'empresa') {
        $sql .= " AND o.usuario_id = :usuario_id";
        $params[':usuario_id'] = $_SESSION['id_usuario'];
    }

    if (!empty($_GET['busqueda'])) {
        $sql .= " AND (o.titulo LIKE :busqueda OR o.descripcion LIKE :busqueda)";
        $params[':busqueda'] = '%' . $_GET['busqueda'] . '%';
    }

    if (!empty($_GET['fecha_desde'])) {
        $sql .= " AND o.fecha_publicacion >= :fecha_desde";
        $params[':fecha_desde'] = $_GET['fecha_desde'];
    }

    if (!empty($_GET['fecha_hasta'])) {
        $sql .= " AND o.fecha_publicacion <= :fecha_hasta";
        $params[':fecha_hasta'] = $_GET['fecha_hasta'];
    }

    if (!empty($_GET['usuario']) && $_SESSION['tipo_usuario'] !== 'empresa') {
        $sql .= " AND o.usuario_id = :usuario";
        $params[':usuario'] = $_GET['usuario'];
    }

    $sql .= " ORDER BY o.fecha_publicacion DESC LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => &$value) {
        $stmt->bindParam($key, $value);
    }
    $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error al cargar las ofertas: " . $e->getMessage();
}
?>

<div class="container my-5">
    <h1 class="mb-4">Ofertas de Empleo</h1>
    <form method="GET" action="ofertas.php" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label for="busqueda" class="form-label">Búsqueda:</label>
            <input 
            type="text" 
            name="busqueda" 
            id="busqueda"
            class="form-control"
            placeholder="Buscar título o descripción"
            value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>"
            >
        </div>

        <div class="col-md-2">
            <label for="fecha_desde" class="form-label">Desde:</label>
            <input 
            type="date" 
            name="fecha_desde"
            id="fecha_desde"
            class="form-control"
            value="<?= isset($_GET['fecha_desde']) ? htmlspecialchars($_GET['fecha_desde']) : '' ?>"
            >
        </div>

        <div class="col-md-2">
            <label for="fecha_hasta" class="form-label">Hasta:</label>
            <input 
            type="date" 
            name="fecha_hasta"
            id="fecha_hasta"
            class="form-control"
            value="<?= isset($_GET['fecha_hasta']) ? htmlspecialchars($_GET['fecha_hasta']) : '' ?>"
            >
        </div>

        <?php if ($_SESSION['tipo_usuario'] !== 'empresa'): ?>
            <div class="col-md-3">
            <label for="usuario" class="form-label">Empresas:</label>
            <select name="usuario" id="usuario" class="form-select">
                <option value="">Todas</option>
                <?php
                try {
                $stmtUsuarios = $conn->query("SELECT id, nombre FROM usuarios WHERE tipo_usuario='empresa' ORDER BY nombre");
                $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
                foreach ($usuarios as $user) {
                    $selected = (isset($_GET['usuario']) && $_GET['usuario'] == $user['id']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($user['id']) . "' $selected>" 
                        . htmlspecialchars($user['nombre']) 
                        . "</option>";
                }
                } catch (PDOException $e) {
                echo "Error al cargar usuarios: " . $e->getMessage();
                }
                ?>
            </select>
            </div>
        <?php endif; ?>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
            Filtrar
            </button>
        </div>
    </form>

    <?php if (count($ofertas) > 0): ?>
        <ul class="list-unstyled">
            <?php foreach ($ofertas as $oferta): ?>
                <li class="mb-4 border-bottom pb-3">
                    <h3><?= htmlspecialchars($oferta['titulo']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($oferta['descripcion'])) ?></p>
                    <small>
                        Publicado por: <?= htmlspecialchars($oferta['nombre_usuario']) ?>
                        el <?= date("d/m/Y", strtotime($oferta['fecha_publicacion'])) ?>
                    </small>
                    <p class="mt-2 fw-semibold">
                        <?= $oferta['num_postulaciones'] ?> inscripciones
                    </p>

                    <?php if ($_SESSION['tipo_usuario'] === 'candidato'): ?>
                        <?php
                            $stmtPostulacion = $conn->prepare("
                                SELECT 1 FROM postulaciones 
                                WHERE usuario_id = :usuario_id 
                                  AND oferta_id = :oferta_id
                            ");
                            $stmtPostulacion->execute([
                                ':usuario_id' => $_SESSION['id_usuario'], 
                                ':oferta_id' => $oferta['id']
                            ]);
                            $postulado = $stmtPostulacion->fetchColumn();
                        ?>
                        <?php if ($postulado): ?>
                            <form 
                              action="../controllers/despostularController.php"
                              method="POST"
                              class="despostular-form d-inline"
                              onsubmit="return confirm('¿Seguro que deseas anular tu inscripción a esta oferta?')"
                            >
                                <input type="hidden" name="oferta_id" value="<?= $oferta['id'] ?>">
                                <button type="submit" class="btn btn-warning">
                                    Cancelar inscripción
                                </button>
                            </form>
                        <?php else: ?>
                            <form 
                              action="../controllers/postularController.php"
                              method="POST"
                              class="d-inline"
                            >
                                <input type="hidden" name="oferta_id" value="<?= $oferta['id'] ?>">
                                <button type="submit" class="btn btn-primary">
                                    Inscribirse
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($_SESSION['tipo_usuario'] === 'empresa' 
                              && $oferta['usuario_id'] == $_SESSION['id_usuario']): ?>
                        <form 
                          action="editarOferta.php" 
                          method="GET"
                          class="d-inline"
                        >
                            <input type="hidden" name="id" value="<?= $oferta['id'] ?>">
                            <button type="submit" class="btn btn-secondary">
                                Editar
                            </button>
                        </form>
                        <form 
                          action="../controllers/deleteOfertaController.php" 
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('¿Seguro que deseas eliminar esta oferta?')"
                        >
                            <input type="hidden" name="id_oferta" value="<?= $oferta['id'] ?>">
                            <button type="submit" class="btn btn-danger">
                                Eliminar
                            </button>
                        </form>
                        <form 
                          action="verPostulantes.php" 
                          method="GET"
                          class="d-inline"
                        >
                            <input type="hidden" name="id" value="<?= $oferta['id'] ?>">
                            <button type="submit" class="btn btn-info">
                                Ver Inscripciones
                            </button>
                        </form>
                    <?php endif; ?>
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
        <p>No hay ofertas disponibles.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/postulations.js"></script>
