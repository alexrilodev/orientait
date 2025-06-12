<?php
require_once __DIR__ . '/../config.php';
include '../includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-4">Iniciar Sesión</h1>

  <?php if (!empty($_SESSION['login_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['login_error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['login_error']); ?>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>controllers/loginController.php" method="POST" class="w-50 mx-auto">
    <div class="mb-3">
      <label for="emailLogin" class="form-label">Correo Electrónico:</label>
      <input 
        id="emailLogin"
        name="email"
        type="email" 
        class="form-control" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="passwordLogin" class="form-label">Contraseña:</label>
      <div class="input-group">
        <input 
          id="passwordLogin"
          name="password"
          type="password" 
          class="form-control" 
          required
        >
        <button 
          type="button" 
          id="togglePasswordLogin"
          class="btn btn-outline-secondary"
        >
          Ver
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
  </form>

  <p class="mt-3 text-center">
    <a href="<?= BASE_URL ?>pages/register.php">
      ¿No tienes cuenta? Regístrate aquí
    </a>
  </p>
</div>

<?php include '../includes/footer.php'; ?>
