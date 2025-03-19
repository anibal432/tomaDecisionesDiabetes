<?php
session_start();
include('conexionL.php');
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="css/Login.css">
  <title>Iniciar sesión</title>
</head>
<body> 
<div class="container">
  <div class="row g-0 justify-content-center pt-5 mt-5">
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">
              <form action="logear.php" method="post">
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
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js\sweetalert2.all.min.js"></script>
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
</script>

</body>
</html>
