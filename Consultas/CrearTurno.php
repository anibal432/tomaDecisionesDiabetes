<?php
require 'conexion.php';

// Funciones para manejar la base de datos
function agregarTurno($conn, $horaAtencion, $nombreTurno) {
    $query = "INSERT INTO FechaTurno (FechaAtencion, NombreTurno) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $horaAtencion, $nombreTurno);
    return $stmt->execute();
}

function eliminarTurno($conn, $id) {
    $query = "DELETE FROM FechaTurno WHERE IdTurnoAsignado = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function editarTurno($conn, $id, $horaAtencion, $nombreTurno) {
    $query = "UPDATE FechaTurno SET FechaAtencion=?, NombreTurno=? WHERE IdTurnoAsignado=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $horaAtencion, $nombreTurno, $id);
    return $stmt->execute();
}

// Función para determinar el nombre del turno según la hora
function determinarNombreTurno($hora) {
    $horaInicio = explode(" - ", $hora)[0]; // Obtener la hora de inicio del rango
    $horaEntero = (int) explode(":", $horaInicio)[0]; // Extraer la hora (8, 9, etc.)
    return ($horaEntero >= 8 && $horaEntero <= 12) ? "Mañana" : "Tarde";
}

// Lógica para agregar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $horaAtencion = htmlspecialchars($_POST['horaAtencion'], ENT_QUOTES, 'UTF-8');
    $nombreTurno = determinarNombreTurno($horaAtencion); // Asignar automáticamente "Mañana" o "Tarde"

    if (agregarTurno($conn, $horaAtencion, $nombreTurno)) {
        header("Location: CrearTurno.php");
        exit();
    }
}

// Lógica para eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    if (eliminarTurno($conn, $id)) {
        header("Location: CrearTurno.php");
        exit();
    }
}

// Lógica para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $horaAtencion = htmlspecialchars($_POST['horaAtencion'], ENT_QUOTES, 'UTF-8');
    $nombreTurno = determinarNombreTurno($horaAtencion); // Asignar automáticamente "Mañana" o "Tarde"

    if (editarTurno($conn, $id, $horaAtencion, $nombreTurno)) {
        header("Location: GestionTurnos.php");
        exit();
    }
}

// Obtener datos de la tabla
$result = $conn->query("SELECT * FROM FechaTurno");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .turno-manana { color: green; }
        .turno-tarde { color: orange; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Turnos</h2>
        <div class="card p-4 shadow">
            <form method="POST" id="formTurno">
                <div class="mb-3">
                    <label class="form-label">Hora de Atención:</label>
                    <select class="form-control" name="horaAtencion" id="horaAtencion" required>
                        <?php
                        // Generar horas desde las 8:00 hasta las 17:00, con una hora de almuerzo
                        $horaInicio = strtotime("08:00");
                        $horaFin = strtotime("17:00");
                        $horaAlmuerzoInicio = strtotime("12:00");
                        $horaAlmuerzoFin = strtotime("13:00");

                        for ($hora = $horaInicio; $hora <= $horaFin; $hora = strtotime("+1 hour", $hora)) {
                            if ($hora >= $horaAlmuerzoInicio && $hora < $horaAlmuerzoFin) {
                                continue; // Saltar la hora de almuerzo
                            }
                            $horaFormateada = date("H:i", $hora);
                            $horaSiguiente = date("H:i", strtotime("+1 hour", $hora));
                            echo "<option value='$horaFormateada - $horaSiguiente'>$horaFormateada - $horaSiguiente</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre del Turno:</label>
                    <input type="text" class="form-control" name="nombreTurno" id="nombreTurno" readonly>
                </div>
                <button type="submit" name="agregar" class="btn btn-primary w-100">Agregar Turno</button>
            </form>
        </div>
        
        <div class="mt-4">
            <h4>Lista de Turnos</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hora de Atención</th>
                        <th>Nombre del Turno</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['IdTurnoAsignado'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['FechaAtencion'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="<?= ($row['NombreTurno'] == 'Mañana') ? 'turno-manana' : 'turno-tarde' ?>">
                                <?= htmlspecialchars($row['NombreTurno'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <a href="CrearTurno.php?eliminar=<?= $row['IdTurnoAsignado'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <button class="btn btn-warning btn-sm" onclick="editar(<?= $row['IdTurnoAsignado'] ?>, '<?= addslashes($row['FechaAtencion']) ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para editar -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Turno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formEditarTurno">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Hora de Atención:</label>
                            <select class="form-control" name="horaAtencion" id="editHoraAtencion" required>
                                <?php
                                // Generar horas para el modal de edición
                                for ($hora = $horaInicio; $hora <= $horaFin; $hora = strtotime("+1 hour", $hora)) {
                                    if ($hora >= $horaAlmuerzoInicio && $hora < $horaAlmuerzoFin) {
                                        continue; // Saltar la hora de almuerzo
                                    }
                                    $horaFormateada = date("H:i", $hora);
                                    $horaSiguiente = date("H:i", strtotime("+1 hour", $hora));
                                    echo "<option value='$horaFormateada - $horaSiguiente'>$horaFormateada - $horaSiguiente</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre del Turno:</label>
                            <input type="text" class="form-control" name="nombreTurno" id="editNombreTurno" readonly>
                        </div>
                        <button type="submit" name="editar" class="btn btn-success w-100">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para determinar el nombre del turno según la hora seleccionada
        function actualizarNombreTurno() {
            const horaAtencion = document.getElementById('horaAtencion').value;
            const horaInicio = parseInt(horaAtencion.split(" - ")[0].split(":")[0]);
            const nombreTurno = (horaInicio >= 8 && horaInicio <= 12) ? "Mañana" : "Tarde";
            document.getElementById('nombreTurno').value = nombreTurno;
        }

        // Función para editar
        function editar(id, horaAtencion) {
            document.getElementById('editId').value = id;
            document.getElementById('editHoraAtencion').value = horaAtencion;
            const horaInicio = parseInt(horaAtencion.split(" - ")[0].split(":")[0]);
            const nombreTurno = (horaInicio >= 8 && horaInicio <= 12) ? "Mañana" : "Tarde";
            document.getElementById('editNombreTurno').value = nombreTurno;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        // Actualizar el nombre del turno al cambiar la hora
        document.getElementById('horaAtencion').addEventListener('change', actualizarNombreTurno);
        document.getElementById('editHoraAtencion').addEventListener('change', function() {
            const horaAtencion = this.value;
            const horaInicio = parseInt(horaAtencion.split(" - ")[0].split(":")[0]);
            const nombreTurno = (horaInicio >= 8 && horaInicio <= 12) ? "Mañana" : "Tarde";
            document.getElementById('editNombreTurno').value = nombreTurno;
        });

        // Actualizar el nombre del turno al cargar la página
        actualizarNombreTurno();
    </script>
</body>
</html>