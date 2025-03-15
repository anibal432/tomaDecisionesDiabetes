<?php
require 'conexion.php';

// Función para agregar un turno
function agregarTurno($conn, $idPaciente, $idMedico, $idTurnoAsignado, $estado) {
    $query = "INSERT INTO Turnos (IdPaciente, IdMedico, IdTurnoAsignado, Estado) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $idPaciente, $idMedico, $idTurnoAsignado, $estado);
    return $stmt->execute();
}

// Lógica para agregar un turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $idPaciente = $_POST['idPaciente'];
    $idMedico = $_POST['idMedico'];
    $idTurnoAsignado = $_POST['idTurnoAsignado'];
    $estado = 1; // Estado activo por defecto

    if (agregarTurno($conn, $idPaciente, $idMedico, $idTurnoAsignado, $estado)) {
        header("Location: AsignarTurno.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error al asignar el turno.</div>";
    }
}

// Obtener datos de las tablas relacionadas
$turnosDisponibles = $conn->query("SELECT IdTurnoAsignado, FechaAtencion, NombreTurno FROM FechaTurno");
$turnosAsignados = $conn->query("SELECT t.IdTurno, p.NombreUno AS PacienteNombre, p.PrimerApellido AS PacienteApellido, m.PrimerNombre AS MedicoNombre, m.PrimerApellido AS MedicoApellido, ft.FechaAtencion, ft.NombreTurno, t.Estado FROM Turnos t JOIN Paciente p ON t.IdPaciente = p.IdPaciente JOIN Medico m ON t.IdMedico = m.IdMedico JOIN FechaTurno ft ON t.IdTurnoAsignado = ft.IdTurnoAsignado");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Asignación de Turnos</h2>
        <div class="card p-4 shadow">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Paciente:</label>
                    <select class="form-control select2-paciente" name="idPaciente" required></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Médico:</label>
                    <select class="form-control select2-medico" name="idMedico" required></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Turno Disponible:</label>
                    <select class="form-control" name="idTurnoAsignado" required>
                        <option value="">Seleccione un turno</option>
                        <?php while ($turno = $turnosDisponibles->fetch_assoc()): ?>
                            <option value="<?= $turno['IdTurnoAsignado'] ?>"><?= $turno['FechaAtencion'] ?> - <?= $turno['NombreTurno'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="agregar" class="btn btn-primary w-100">Asignar Turno</button>
            </form>
        </div>
        
        <div class="mt-4">
            <h4>Lista de Turnos Asignados</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Fecha y Turno</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($turnoAsignado = $turnosAsignados->fetch_assoc()): ?>
                        <tr>
                            <td><?= $turnoAsignado['IdTurno'] ?></td>
                            <td><?= $turnoAsignado['PacienteNombre'] ?> <?= $turnoAsignado['PacienteApellido'] ?></td>
                            <td><?= $turnoAsignado['MedicoNombre'] ?> <?= $turnoAsignado['MedicoApellido'] ?></td>
                            <td><?= $turnoAsignado['FechaAtencion'] ?> - <?= $turnoAsignado['NombreTurno'] ?></td>
                            <td><?= $turnoAsignado['Estado'] ? 'Activo' : 'Inactivo' ?></td>
                            <td>
                                <a href="AsignarTurno.php?eliminar=<?= $turnoAsignado['IdTurno'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <button class="btn btn-warning btn-sm" onclick="editar(<?= $turnoAsignado['IdTurno'] ?>, <?= $turnoAsignado['IdPaciente'] ?>, <?= $turnoAsignado['IdMedico'] ?>, <?= $turnoAsignado['IdTurnoAsignado'] ?>, <?= $turnoAsignado['Estado'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery y Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Inicializar Select2 para pacientes y médicos con búsqueda dinámica
        $(document).ready(function() {
            // Configuración para pacientes
            $('.select2-paciente').select2({
                ajax: {
                    url: 'buscar_pacientes.php', // Archivo PHP para buscar pacientes
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // Término de búsqueda
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1 // Mínimo de caracteres para buscar
            });

            // Configuración para médicos
            $('.select2-medico').select2({
                ajax: {
                    url: 'buscar_medicos.php', // Archivo PHP para buscar médicos
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // Término de búsqueda
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1 // Mínimo de caracteres para buscar
            });
        });
    </script>
</body>
</html>