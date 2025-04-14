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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/botonUno.css">
    
</head>
<body>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <nav class="navbar">
        <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
        <div class="logo">Diabetes Log</div>
        <ul>            
            <li><a href="../iniciomedico.php" class="fas user"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="../Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> <span>Ing. Paciente</span></a></li>
            <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
            <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Datos del Paciente</span></a></li>
            <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>
<br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <h2 class="form-title">Formulario de Paciente</h2>
                    
                    <form id="formPaciente" method="POST" action="guardar_paciente.php">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" name="nombredos" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Tercer Nombre</label>
                                <input type="text" class="form-control" name="nombretres">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" name="apellidodos">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">DPI</label>
                                <input type="text" class="form-control" name="dpi">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select class="form-control" name="sexo" required>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label for="grupoEtnico" class="form-label">Grupo Étnico</label>
                                <select class="form-control" name="grupoEtnico" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Ladino">Ladino</option>
                                    <option value="Mestizo">Mestizo</option>
                                    <option value="Maya">Maya</option>
                                    <option value="Garifuna">Garifuna</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">
                            <i class="fas fa-save me-2"></i>Guardar Paciente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>