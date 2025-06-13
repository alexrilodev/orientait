<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once '../database/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];

try {
    $stmt = $conn->prepare("
        SELECT nombre, email, telefono, tipo_usuario, fecha_creacion 
        FROM usuarios 
        WHERE id = ?
    ");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al cargar el perfil: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Mi Perfil</h1>

    <?php if ($usuario): ?>
        <form 
          action="../controllers/editarPerfilController.php" 
          method="POST" 
          enctype="multipart/form-data"
          class="w-50 mx-auto"
        >
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input 
                  type="text" 
                  name="nombre" 
                  class="form-control"
                  value="<?= htmlspecialchars($usuario['nombre']) ?>" 
                  required
                >
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input 
                  type="email" 
                  name="email" 
                  class="form-control"
                  value="<?= htmlspecialchars($usuario['email']) ?>" 
                  required
                >
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono de contacto:</label>
                <input 
                    type="text" 
                    name="telefono"
                    class="form-control"
                    value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" 
                    required
                >
            </div>
            <div class="mb-3">
                <label for="passwordPerfil" class="form-label">Nueva contraseña (opcional):</label>
                <div class="input-group">
                  <input 
                    type="password"
                    name="password"
                    class="form-control"
                    id="passwordPerfil"
                  >
                  <button 
                    type="button"
                    class="btn btn-outline-secondary"
                    id="togglePasswordPerfil"
                  >
                    Ver
                  </button>
                </div>
            </div>

            <p>
                <strong>Tipo de Usuario:</strong>
                <?= ucfirst(htmlspecialchars($usuario['tipo_usuario'])) ?>
            </p>
            <p>
                <strong>Fecha de Registro:</strong>
                <?= date("d/m/Y H:i", strtotime($usuario['fecha_creacion'])) ?>
            </p>

            <?php if ($_SESSION['tipo_usuario'] === 'candidato'): ?>
                <div class="mb-3">
                    <label for="cv" class="form-label">
                        Subir CV (PDF):
                    </label>
                    <input 
                      type="file" 
                      name="cv"
                      id="cv"
                      accept=".pdf"
                      class="form-control"
                    >
                </div>
                <?php
                $ruta_fisica = __DIR__ . '/uploads/cv/' . $usuario_id . '.pdf';
                $ruta_web = BASE_URL . '/uploads/cv/' . $usuario_id . '.pdf';

                if (file_exists($ruta_fisica)): ?>
                    <p>
                        <a href="<?= $ruta_web ?>" target="_blank">
                            📄 Ver CV Actual
                        </a>
                    </p>
                <?php else: ?>
                    <p>No has subido un CV aún.</p>
                <?php endif; ?>
            <?php endif; ?>

            <button 
              type="submit" 
              class="btn btn-primary"
            >
              Guardar Cambios
            </button><br>
            <a 
                href="<?= BASE_URL ?>controllers/eliminarUsuarioController.php"
                class="btn btn-danger mt-3"
                onclick="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.')"
                >
                Eliminar cuenta
            </a>

        </form>
    <?php else: ?>
        <p>No se encontraron datos de usuario.</p>
    <?php endif; ?>

    <p class="mt-3 text-center">
        <a href="<?= BASE_URL ?>pages/dashboard.php">Volver al Panel</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputCV = document.getElementById('cv');
    const formPerfil = document.querySelector('form');

    if (inputCV && formPerfil) {
        formPerfil.addEventListener('submit', function (e) {
            const archivo = inputCV.files[0];
            if (archivo && archivo.type !== 'application/pdf') {
                e.preventDefault();
                alert('Solo se permiten archivos PDF como CV.');
            }
        });
    }
});
</script>
