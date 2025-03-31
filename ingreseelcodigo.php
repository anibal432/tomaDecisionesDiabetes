<?php
session_start();
if (isset($_SESSION['correo_ingresado'])) {
    $_SESSION['correo_usuario'] = $_SESSION['correo_ingresado'];
} else {
    header("Location:CambioContra.php");
    exit();
}

include('conexionL.php');
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/code.css">
</head>

<body>
<div class="container">
    <div class="row g-0 justify-content-center pt-5 mt-5 mr-1">
        <div class="col-md-8 col-lg-6">
            <div class="login d-flex py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-70 mx-auto">
                            <h3 class="login-heading mb-4 text-center ">Cambio de Contraseña</h3>
                            <label>Correo Ingresado: <?php echo isset($_SESSION['correo_ingresado']) ? $_SESSION['correo_ingresado'] : ''; ?></label>
                            <form action="actualizarcontraseña.php" method="POST">
                                <br>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código" required>
                                    <label for="codigo">Código</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Contrasena" required>
                                    <label for="contrasena">Contraseña</label>
                                    <div class="progress">
                                            <div class="progress-bar progress-bar-yellow" role="progressbar" style="width: 0%;" id="passwordStrength"></div>
                                        </div>
                                        <div id="passwordError" class="text-danger" style="display: none;"></div>
                                        <div id="spaceError" class="text-danger" style="display: none;"></div>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input password-checkbox" type="checkbox" id="showPassword">
                                        <label class="form-check-label" for="showPassword">Mostrar contraseña</label>
                                    </div>
                                <div class="d-grid">
                                    <button class="btn btn-lg fw-bold mb-2 btn-iniciar-sesion" type="submit" id="submitButton" disabled>Actualizar Contraseña</button>
                                    <a class="d-block text-center mt-2 small" href="index.php">Volver al LogIn</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
        const passwordField = document.getElementById("contrasena");
        const passwordStrength = document.getElementById("passwordStrength");
        const passwordError = document.getElementById("passwordError");
        const spaceError = document.getElementById("spaceError");
        const submitButton = document.getElementById("submitButton");

        function validatePasswordRequirements(password) {
            const hasNumber = /\d/;
            const hasUpperCase = /[A-Z]/;
            const hasSpecialCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;

            let requirements = [];
            if (!hasNumber.test(password)) {
                requirements.push("un número");
            }
            if (!hasUpperCase.test(password)) {
                requirements.push("una letra mayúscula");
            }
            if (!hasSpecialCharacter.test(password)) {
                requirements.push("un carácter especial");
            }

            return requirements;
        }

        passwordField.addEventListener("input", function () {
            const password = this.value;
            const trimmedPassword = password.trim(); 
            const strength = (trimmedPassword.length >= 8) ? 100 : (trimmedPassword.length / 8 * 100);
            passwordStrength.style.width = strength + "%";

            if (password.includes(" ")) {
                spaceError.textContent = "La contraseña no puede contener espacios";
                spaceError.style.display = "block";
                passwordStrength.classList.remove('progress-bar-yellow', 'progress-bar-green'); 
                passwordStrength.classList.add('progress-bar-red');
                passwordStrength.style.width = "100%";
                submitButton.disabled = true;
                return;
            } else {
                spaceError.style.display = "none";
            }

            const requirements = validatePasswordRequirements(trimmedPassword);
            if (requirements.length > 0) {
                passwordError.textContent = "La contraseña necesita " + requirements.join(", ") + ".";
                passwordError.style.display = "block";
                passwordStrength.classList.remove('progress-bar-yellow', 'progress-bar-green');
                passwordStrength.classList.add('progress-bar-red');
                passwordStrength.style.width = "100%";
                submitButton.disabled = true;
            } else {
                passwordError.style.display = "none";
                if (trimmedPassword.length >= 8) {
                    passwordStrength.classList.remove('progress-bar-red', 'progress-bar-yellow');
                    passwordStrength.classList.add('progress-bar-green');
                    submitButton.disabled = false;
                } else {
                    passwordStrength.classList.remove('progress-bar-red', 'progress-bar-green');
                    passwordStrength.classList.add('progress-bar-yellow');
                    submitButton.disabled = true;
                }
            }
        });

        const showPasswordCheckbox = document.getElementById("showPassword");
        showPasswordCheckbox.addEventListener("change", function () {
            const type = this.checked ? "text" : "password";
            passwordField.setAttribute("type", type);
        });
    </script>

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
