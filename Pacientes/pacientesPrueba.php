<?php
include '../conexionDiabetes.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pacientes</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="stilos.css">
    <link rel="stylesheet" href="../css/nav.css">
    <!-- jQuery y dependencias de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar">
        <div class="logo">Diabetes Log</div>
        <ul>
        <li><a href="../iniciomedico.php"><i class="fas fa-home"></i>Inicio</a></li>
            <li><a href="#"><i class="fas fa-solid fa-users"></i>Boton 1</a></li>
            <li><a href="../Pacientes/pacientesPrueba.php"><i class="fas fa-solid fa-hospital-user"></i>Ing. Paciente</a></li>
            <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-solid fa-hospital-user"></i>Datos del Paciente</a></li>

            <li><a href="Logout.php"><i class="fas fa-solid fa-right-from-bracket"></i>LogOut</a></li>
        </ul>
    </nav>
    <br>
</body>

<div class="container mt-5">
        <h2 class="text-center">Formulario de Paciente</h2>

        <!-- FORMULARIO PRINCIPAL PACIENTE -->
        <form id="formPaciente" method="POST" action="guardar_paciente.php" class="mb-4 p-4 border rounded">
    <div class="row">
        
        <div class="col-md-6 mb-3">
            <label class="form-label">Primer Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Segundo Nombre</label>
            <input type="text" class="form-control" name="nombredos" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Tercer Nombre</label>
            <input type="text" class="form-control" name="nombretres">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Primer Apellido</label>
            <input type="text" class="form-control" name="apellido" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Segundo Apellido</label>
            <input type="text" class="form-control" name="apellidodos">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">DPI</label>
            <input type="text" class="form-control" name="dpi">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono">
        </div>
        <div class="col-md-6 mb-3">
            <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select class="form-control" name="sexo" required>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="grupoEtnico" class="form-label">Grupo Étnico</label>
            <select class="form-control" name="grupoEtnico" required>
                <option value="">Seleccione...</option>
                <option value="Ladino">Ladino</option>
                <option value="Mestizo">Mestizo</option>
                <option value="Mestizo">Maya</option>
                <option value="Mestizo">Garifuna</option>
                <option value="Mestizo">Otro</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100">Guardar Paciente</button> 
</form>



</body>
</html>

