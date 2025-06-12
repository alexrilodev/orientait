<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'];

$por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

try {
    require_once '../database/db.php';

    if ($tipo_usuario === 'empresa') {
        // Notificaciones empresa
        $stmtTotal = $conn->prepare("
            SELECT COUNT(*) FROM notificaciones 
            WHERE usuario_id = :id AND tipo = 'postulacion'
        ");
        $stmtTotal->execute([':id' => $usuario_id]);
        $total_notificaciones = $stmtTotal->fetchColumn();
        $total_paginas = ceil($total_notificaciones / $por_pagina);

        $stmt = $conn->prepare("
            SELECT * 
            FROM notificaciones
            WHERE usuario_id = :id AND tipo = 'postulacion'
            ORDER BY fecha DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Leídas
        $update = $conn->prepare("
            UPDATE notificaciones 
            SET leido = 1 
            WHERE usuario_id = :id 
              AND leido = 0 
              AND tipo = 'postulacion'
        ");
        $update->execute([':id' => $usuario_id]);

    } elseif ($tipo_usuario === 'admin') {
        // Notificaciones admin
        $stmtTotal = $conn->prepare("
            SELECT COUNT(*) FROM notificaciones 
            WHERE usuario_id = :id AND tipo = 'admin'
        ");
        $stmtTotal->execute([':id' => $usuario_id]);
        $total_notificaciones = $stmtTotal->fetchColumn();
        $total_paginas = ceil($total_notificaciones / $por_pagina);

        $stmt = $conn->prepare("
            SELECT * 
            FROM notificaciones
            WHERE usuario_id = :id AND tipo = 'admin'
            ORDER BY fecha DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Leídas
        $update = $conn->prepare("
            UPDATE notificaciones 
            SET leido = 1 
            WHERE usuario_id = :id 
              AND leido = 0 
              AND tipo = 'admin'
        ");
        $update->execute([':id' => $usuario_id]);

    } else {
        // Notificaciones candidato
        $stmtTotal = $conn->prepare("
            SELECT COUNT(*)
            FROM notificaciones_globales ng
            WHERE ng.tipo = 'general'
        ");
        $stmtTotal->execute();
        $total_notificaciones = $stmtTotal->fetchColumn();
        $total_paginas = ceil($total_notificaciones / $por_pagina);

        $stmt = $conn->prepare("
            SELECT ng.id, ng.mensaje, ng.tipo, ng.fecha,
                   IFNULL(nl.leido, 0) AS leido
            FROM notificaciones_globales ng
            LEFT JOIN notificaciones_globales_leidas nl
                   ON ng.id = nl.id_notificacion
                  AND nl.id_usuario = :usuario_id
            WHERE ng.tipo = 'general'
            ORDER BY ng.fecha DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Leídas
        if (count($notificaciones) > 0) {
            $updateGlobales = $conn->prepare("
                INSERT INTO notificaciones_globales_leidas (id_notificacion, id_usuario, leido, fecha_lectura)
                SELECT ng.id, :idUsuario, 1, NOW()
                FROM notificaciones_globales ng
                LEFT JOIN notificaciones_globales_leidas nl
                       ON ng.id = nl.id_notificacion
                      AND nl.id_usuario = :idUsuario
                WHERE ng.tipo = 'general'
                  AND IFNULL(nl.leido, 0) = 0
                ON DUPLICATE KEY UPDATE
                    leido = VALUES(leido),
                    fecha_lectura = VALUES(fecha_lectura)
            ");
            $updateGlobales->execute([':idUsuario' => $usuario_id]);
        }
    }

} catch (PDOException $e) {
    die("Error al cargar las notificaciones: " . $e->getMessage());
}

include '../includes/header.php';
?>


<div class="container my-5">
    <h2 class="mb-4">Mis Notificaciones</h2>

    <?php if (count($notificaciones) > 0): ?>
        <ul class="list-unstyled">
            <?php foreach ($notificaciones as $noti): ?>
                <li class="mb-3 border-bottom pb-2">
                    <i class="bi bi-bell-fill"></i> <?= $noti['mensaje'] ?><br>
                    <small class="text-muted">
                        📅 <?= date("d/m/Y H:i", strtotime($noti['fecha'])) ?>
                    </small>
                    <?php if ($tipo_usuario === 'empresa'): ?>
                        <?php if (!$noti['leido']): ?>
                            <strong class="text-danger"> (Nueva)</strong>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (isset($noti['leido']) && $noti['leido'] == 0): ?>
                            <strong class="text-danger"> (Nueva)</strong>
                        <?php endif; ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <nav>
            <ul class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a 
                          class="page-link" 
                          href="?pagina=<?= $pagina_actual - 1 ?>"
                        >
                          ← Anterior
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a 
                          class="page-link" 
                          href="?pagina=<?= $pagina_actual + 1 ?>"
                        >
                          Siguiente →
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php else: ?>
        <p>No tienes notificaciones.</p>
    <?php endif; ?>
</div>


<?php include '../includes/footer.php'; ?>
