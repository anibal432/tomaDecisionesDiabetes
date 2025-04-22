<?php
session_start();
include('conexionL.php');
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$mostrar_modal = isset($_SESSION['mostrar_modal_verificacion']) ? $_SESSION['mostrar_modal_verificacion'] : false;
unset($_SESSION['error']);
unset($_SESSION['mostrar_modal_verificacion']);
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/Login.css">
  <title>Iniciar sesión</title>
</head>
<body> 
<div class="container">
  <div class="login">
    <form action="logear.php" method="post" id="loginForm">
      <h3 class="login-heading mb-4 text-center">Ingreso de usuario</h3>
      
      <div class="form-floating mb-3">
        <input type="email" class="form-control" name="correo" id="correo" placeholder="Correo" required>
        <label for="correo">Correo electrónico</label>
      </div>
      
      <div class="form-floating mb-3">
        <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Contraseña" required>
        <label for="contrasena">Contraseña</label>
      </div>
      
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="showPassword">
        <label class="form-check-label" for="showPassword">
          Mostrar contraseña
        </label>
      </div>

      <div class="d-grid">
        <button class="btn btn-lg btn-outline-secondary fw-bold mb-2 btn-iniciar-sesion" type="submit">Iniciar sesión</button>
        <a class="d-block text-center mt-2 small" href="CambioContra.php">¿Olvidaste tu contraseña o quieres cambiarla?</a>
      </div>
    </form>
  </div>
</div>

<div class="modal fade modal-verificacion" id="verificacionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Verificación en Dos Pasos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="verification-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            <path d="M12 8v4M12 16h.01"></path>
          </svg>
        </div>
        <p>Por seguridad, hemos enviado un código de 6 dígitos a tu correo electrónico.</p>
        
        <form id="formVerificacion" action="verificar_codigo.php" method="post">
          <input type="text" class="form-control" id="codigo" name="codigo" maxlength="6" required 
                 placeholder="______" pattern="\d{6}" inputmode="numeric">
          <button type="submit" class="btn btn-verificar mt-3">Verificar Código</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/sweetalert2.all.min.js"></script>
<script>
  document.getElementById("showPassword").addEventListener("change", function() {
    var passwordField = document.getElementById("contrasena");
    passwordField.type = this.checked ? "text" : "password";
  });

  <?php if (!empty($error)): ?>
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: '<?php echo $error; ?>',
    });
  <?php endif; ?>

  <?php if ($mostrar_modal): ?>
    document.addEventListener('DOMContentLoaded', function() {
      var modal = new bootstrap.Modal(document.getElementById('verificacionModal'), {
        keyboard: false,
        backdrop: 'static'
      });
      modal.show();
      
      var codigoInput = document.getElementById('codigo');
      codigoInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
      codigoInput.focus();
      
      document.getElementById('verificacionModal').addEventListener('hidden.bs.modal', function() {
        fetch('limpiar_codigo.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'correo=<?php echo isset($_SESSION['correo_verificacion']) ? $_SESSION['correo_verificacion'] : ""; ?>'
        });
      });
    });
  <?php endif; ?>
</script>
</body>
</html>