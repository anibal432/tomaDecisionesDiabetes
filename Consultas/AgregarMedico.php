<?php
require 'conexion.php';

// Funciones para manejar la base de datos
function agregarMedico($conn, $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $correoMedico, $contraMedico, $codigoContra, $noColegiado) {
    $query = "INSERT INTO Medico (PrimerNombre, SegundoNombre, TercerNombre, PrimerApellido, SegundoApellido, CorreoMedico, ContraMedico, CodigoContra, NoColegiado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $correoMedico, $contraMedico, $codigoContra, $noColegiado);
    return $stmt->execute();
}

function eliminarMedico($conn, $id) {
    $query = "DELETE FROM Medico WHERE IdMedico = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function editarMedico($conn, $id, $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $correoMedico, $contraMedico, $codigoContra, $noColegiado) {
    $query = "UPDATE Medico SET PrimerNombre=?, SegundoNombre=?, TercerNombre=?, PrimerApellido=?, SegundoApellido=?, CorreoMedico=?, ContraMedico=?, CodigoContra=?, NoColegiado=? WHERE IdMedico=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssi", $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $correoMedico, $contraMedico, $codigoContra, $noColegiado, $id);
    return $stmt->execute();
}

// Lógica para agregar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $primerNombre = htmlspecialchars($_POST['primerNombre'], ENT_QUOTES, 'UTF-8');
    $segundoNombre = htmlspecialchars($_POST['segundoNombre'], ENT_QUOTES, 'UTF-8');
    $tercerNombre = htmlspecialchars($_POST['tercerNombre'], ENT_QUOTES, 'UTF-8');
    $primerApellido = htmlspecialchars($_POST['primerApellido'], ENT_QUOTES, 'UTF-8');
    $segundoApellido = htmlspecialchars($_POST['segundoApellido'], ENT_QUOTES, 'UTF-8');
    $correoMedico = htmlspecialchars($_POST['correoMedico'], ENT_QUOTES, 'UTF-8');
    $contraMedico = htmlspecialchars($_POST['contraMedico'], ENT_QUOTES, 'UTF-8');
    $codigoContra = htmlspecialchars($_POST['codigoContra'], ENT_QUOTES, 'UTF-8');
    $noColegiado = htmlspecialchars($_POST['noColegiado'], ENT_QUOTES, 'UTF-8');

    if (agregarMedico($conn, $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $correoMedico, $contraMedico, $codigoContra, $noColegiado)) {
        header("Location: AgregarMedico.php"); // Cambiado a AgregarMedico.php
        exit();
    }
}

// Lógica para eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    if (eliminarMedico($conn, $id)) {
        header("Location: AgregarMedico.php"); // Cambiado a AgregarMedico.php
        exit();
    }
}

// Lógica para editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $primerNombre = htmlspecialchars($_POST['primerNombre'], ENT_QUOTES, 'UTF-8');
    $segundoNombre = htmlspecialchars($_POST['segundoNombre'], ENT_QUOTES, 'UTF-8');
    $tercerNombre = htmlspecialchars($_POST['tercerNombre'], ENT_QUOTES, 'UTF-8');
    $primerApellido = htmlspecialchars($_POST['primerApellido'], ENT_QUOTES, 'UTF-8');
    $segundoApellido = htmlspecialchars($_POST['segundoApellido'], ENT_QUOTES, 'UTF-8');
    $correoMedico = htmlspecialchars($_POST['correoMedico'], ENT_QUOTES, 'UTF-8');
    $contraMedico = htmlspecialchars($_POST['contraMedico'], ENT_QUOTES, 'UTF-8');
    $codigoContra = htmlspecialchars($_POST['codigoContra'], ENT_QUOTES, 'UTF-8');
    $noColegiado = htmlspecialchars($_POST['noColegiado'], ENT_QUOTES, 'UTF-8');

    if (editarMedico($conn, $id, $primerNombre, $segundoNombre, $tercerNombre, $primerApellido, $segundoApellido, $correoMedico, $contraMedico, $codigoContra, $noColegiado)) {
        header("Location: AgregarMedico.php"); // Cambiado a AgregarMedico.php
        exit();
    }
}

// Obtener datos de la tabla
$result = $conn->query("SELECT * FROM Medico");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Médicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Médicos</h2>
        <div class="card p-4 shadow">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Primer Nombre:</label>
                    <input type="text" class="form-control" name="primerNombre" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label">Segundo Nombre:</label>
                    <input type="text" class="form-control" name="segundoNombre" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tercer Nombre:</label>
                    <input type="text" class="form-control" name="tercerNombre" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label">Primer Apellido:</label>
                    <input type="text" class="form-control" name="primerApellido" required maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label">Segundo Apellido:</label>
                    <input type="text" class="form-control" name="segundoApellido" maxlength="50">
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" name="correoMedico" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" name="contraMedico" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Código de Contraseña:</label>
                    <input type="text" class="form-control" name="codigoContra" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Número de Colegiado:</label>
                    <input type="text" class="form-control" name="noColegiado" required maxlength="100">
                </div>
                <button type="submit" name="agregar" class="btn btn-primary w-100">Agregar Médico</button>
            </form>
        </div>
        
        <div class="mt-4">
            <h4>Lista de Médicos</h4>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Primer Nombre</th>
                        <th>Segundo Nombre</th>
                        <th>Tercer Nombre</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Contraseña</th>
                        <th>Código de Contraseña</th>
                        <th>Número de Colegiado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['IdMedico'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['PrimerNombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['SegundoNombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['TercerNombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['PrimerApellido'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['SegundoApellido'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['CorreoMedico'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['ContraMedico'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['CodigoContra'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['NoColegiado'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="AgregarMedico.php?eliminar=<?= $row['IdMedico'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <button class="btn btn-warning btn-sm" onclick="editar(<?= $row['IdMedico'] ?>, '<?= addslashes($row['PrimerNombre']) ?>', '<?= addslashes($row['SegundoNombre']) ?>', '<?= addslashes($row['TercerNombre']) ?>', '<?= addslashes($row['PrimerApellido']) ?>', '<?= addslashes($row['SegundoApellido']) ?>', '<?= addslashes($row['CorreoMedico']) ?>', '<?= addslashes($row['ContraMedico']) ?>', '<?= addslashes($row['CodigoContra']) ?>', '<?= addslashes($row['NoColegiado']) ?>')">
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
                    <h5 class="modal-title">Editar Médico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Primer Nombre:</label>
                            <input type="text" class="form-control" name="primerNombre" id="editPrimerNombre" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Segundo Nombre:</label>
                            <input type="text" class="form-control" name="segundoNombre" id="editSegundoNombre" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tercer Nombre:</label>
                            <input type="text" class="form-control" name="tercerNombre" id="editTercerNombre" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Primer Apellido:</label>
                            <input type="text" class="form-control" name="primerApellido" id="editPrimerApellido" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Segundo Apellido:</label>
                            <input type="text" class="form-control" name="segundoApellido" id="editSegundoApellido" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico:</label>
                            <input type="email" class="form-control" name="correoMedico" id="editCorreoMedico" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" name="contraMedico" id="editContraMedico" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Código de Contraseña:</label>
                            <input type="text" class="form-control" name="codigoContra" id="editCodigoContra" maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número de Colegiado:</label>
                            <input type="text" class="form-control" name="noColegiado" id="editNoColegiado" required maxlength="100">
                        </div>
                        <button type="submit" name="editar" class="btn btn-success w-100">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editar(id, primerNombre, segundoNombre, tercerNombre, primerApellido, segundoApellido, correoMedico, contraMedico, codigoContra, noColegiado) {
            document.getElementById('editId').value = id;
            document.getElementById('editPrimerNombre').value = primerNombre;
            document.getElementById('editSegundoNombre').value = segundoNombre;
            document.getElementById('editTercerNombre').value = tercerNombre;
            document.getElementById('editPrimerApellido').value = primerApellido;
            document.getElementById('editSegundoApellido').value = segundoApellido;
            document.getElementById('editCorreoMedico').value = correoMedico;
            document.getElementById('editContraMedico').value = contraMedico;
            document.getElementById('editCodigoContra').value = codigoContra;
            document.getElementById('editNoColegiado').value = noColegiado;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html>