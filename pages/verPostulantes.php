<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'empresa') {
    header("Location: ofertas.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ofertas.php");
    exit();
}

$oferta_id = (int) $_GET['id'];
$usuario_id = $_SESSION['id_usuario'];

$stmt = $conn->prepare("
    SELECT titulo 
    FROM ofertas 
    WHERE id = :id 
      AND usuario_id = :usuario_id
");
$stmt->execute([':id' => $oferta_id, ':usuario_id' => $usuario_id]);
$oferta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$oferta) {
    header("Location: ofertas.php");
    exit();
}

$por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

$stmtTotal = $conn->prepare("
    SELECT COUNT(*) 
    FROM postulaciones 
    WHERE oferta_id = :oferta_id
");
$stmtTotal->execute([':oferta_id' => $oferta_id]);
$total_postulantes = $stmtTotal->fetchColumn();
$total_paginas = ceil($total_postulantes / $por_pagina);

$stmt = $conn->prepare("
    SELECT u.id, u.nombre, u.email, u.telefono, p.fecha_postulacion
    FROM postulaciones p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.oferta_id = :oferta_id
    ORDER BY p.fecha_postulacion DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':oferta_id', $oferta_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$postulantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">
        Inscripciones a la oferta: <?= htmlspecialchars($oferta['titulo']) ?>
    </h2>

    <?php if (count($postulantes) > 0): ?>
        <ul class="list-unstyled">
            <?php foreach ($postulantes as $p): ?>
                <li class="mb-3 border-bottom pb-3">
                    <strong><?= htmlspecialchars($p['nombre']) ?></strong><br>
                    ✉️ <?= htmlspecialchars($p['email']) ?><br>
                    📞 <?= !empty($p['telefono']) ? htmlspecialchars($p['telefono']) : '<em>Teléfono no disponible</em>' ?><br>
                    <small>
                        📅 <?= date("d/m/Y H:i", strtotime($p['fecha_postulacion'])) ?>
                    </small>
                    <br>
                    <?php
                        $cvPath = "/uploads/cv/" . $p['id'] . ".pdf";
                        $cvServerPath = UPLOAD_DIR . "cv/" . $p['id'] . ".pdf";
                        if (file_exists($cvServerPath)) {
                            echo "<a href='$cvPath' target='_blank'>📄 Ver CV</a>";
                        } else {
                            echo "<em>CV no disponible</em>";
                        }
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <nav>
            <ul class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a 
                          class="page-link" 
                          href="?id=<?= $oferta_id ?>&pagina=<?= $pagina_actual - 1 ?>"
                        >
                          ← Anterior
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a 
                          class="page-link" 
                          href="?id=<?= $oferta_id ?>&pagina=<?= $i ?>"
                        >
                          <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a 
                          class="page-link" 
                          href="?id=<?= $oferta_id ?>&pagina=<?= $pagina_actual + 1 ?>"
                        >
                          Siguiente →
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php else: ?>
        <p>Aún no hay inscripciones para esta oferta.</p>
    <?php endif; ?>

    <p class="mt-3">
        <a href="ofertas.php">← Volver a mis ofertas</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
