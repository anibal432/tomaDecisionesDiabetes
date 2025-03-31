<?php
session_start();
include('conexionL.php');
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/Login.css">
    <title>Recuperar Contraseña</title>
  </head>
  <body>
    
  <div class="container">
  <div class="login">
    <form method="post" action="recuperar.php" id="changePasswordForm">
      <h3 class="login-heading mb-4 text-center">Cambiar contraseña</h3>

      <div class="form-floating mb-3">
        <input type="email" class="form-control" name="correo" id="correo" placeholder="Correo" required>
        <label for="correo">Correo Electrónico</label>
      </div>

      <div class="d-grid">
        <button class="btn btn-lg btn-outline-secondary fw-bold mb-2 btn-iniciar-sesion" type="submit" name="enviar_correo" value="Enviar correo">Enviar un correo</button>
        <a class="d-block text-center mt-2 small" href="index.php">Volver al LogIn</a>
      </div>
    </form>
  </div>
</div>

      
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
<script src="js\sweetalert2.all.min.js"></script>
    <script>
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