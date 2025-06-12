<?php
require_once __DIR__ . '/../config.php';
include '../includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4">Registro de Usuario</h2>
    <form 
      id="formRegistro" 
      action="../controllers/registerController.php" 
      method="POST"
      class="w-50 mx-auto"
    >
        <div class="mb-3">
            <label for="nombreRegistro" class="form-label">Nombre:</label>
            <input 
              type="text" 
              id="nombreRegistro" 
              name="nombre"
              class="form-control"
              required
            >
        </div>

        <div class="mb-3">
            <label for="emailRegistro" class="form-label">Email:</label>
            <input 
              type="email" 
              id="emailRegistro" 
              name="email"
              class="form-control"
              required
            >
        </div>

        <div class="mb-3">
            <label for="passwordRegistro" class="form-label">Contraseña:</label>
            <div class="input-group">
              <input 
                type="password" 
                id="passwordRegistro" 
                name="password"
                class="form-control"
                required
              >
              <button 
                type="button"
                class="btn btn-outline-secondary"
                id="togglePasswordRegistro"
              >
                Ver
              </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="confirmPasswordRegistro" class="form-label">Confirmar Contraseña:</label>
            <div class="input-group">
              <input 
                type="password" 
                id="confirmPasswordRegistro" 
                name="confirmPassword"
                class="form-control"
                required
              >
              <button 
                type="button"
                class="btn btn-outline-secondary"
                id="toggleConfirmPasswordRegistro"
              >
                Ver
              </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="tipo_usuario" class="form-label">Tipo de usuario:</label>
            <select 
              name="tipo_usuario" 
              id="tipo_usuario" 
              class="form-select" 
              required
            >
                <option value="candidato" selected>Candidato</option>
                <option value="empresa">Empresa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">
            Registrarse
        </button>
    </form>

    <p class="mt-3 text-center">
      <a href="<?= BASE_URL ?>pages/login.php">
        ¿Ya tienes cuenta? Inicia sesión aquí
      </a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
