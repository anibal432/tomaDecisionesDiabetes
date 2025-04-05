<?php
require 'conexion.php';

// Procesar agregar nuevo tipo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $descripcion = $_POST['descripcion'];
    $query = "INSERT INTO TipoDiabetes (DESCRIPCION) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $descripcion);
    $stmt->execute();
    header("Location: TipoDiabetes.php");
    exit();
}

// Procesar eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM TipoDiabetes WHERE IdDiabetes = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: TipoDiabetes.php");
    exit();
}

// Procesar editar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $query = "UPDATE TipoDiabetes SET DESCRIPCION=? WHERE IdDiabetes=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $descripcion, $id);
    $stmt->execute();
    header("Location: TipoDiabetes.php");
    exit();
}

// Obtener todos los tipos de diabetes
$result = $conn->query("SELECT * FROM TipoDiabetes ORDER BY DESCRIPCION");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Diabetes</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="../Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> <span>Ing. Paciente</span></a></li>
            <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
            <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Datos del Paciente</span></a></li>
            <li><a href="../Consultas/TipoDiabetes.php" class="active"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Gestión de Tipos de Diabetes</h2>
        
        <!-- Botón para agregar nuevo tipo (modal) -->
        <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Agregar Tipo
            </button>
        </div>
        
        <!-- Modal para agregar -->
        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar Tipo de Diabetes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Descripción:</label>
                                <input type="text" class="form-control" name="descripcion" required maxlength="50" 
                                       placeholder="Ej: Diabetes Tipo 1, Diabetes Tipo 2, etc.">
                            </div>
                            <button type="submit" name="agregar" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lista de tipos -->
        <div class="card shadow">
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['IdDiabetes']) ?></td>
                                        <td><?= htmlspecialchars($row['DESCRIPCION']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="editar(<?= $row['IdDiabetes'] ?>, '<?= addslashes($row['DESCRIPCION']) ?>')">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>
                                            <a href="TipoDiabetes.php?eliminar=<?= $row['IdDiabetes'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('¿Estás seguro de eliminar este tipo de diabetes?');">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No hay tipos de diabetes registrados.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para editar -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tipo de Diabetes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Descripción:</label>
                            <input type="text" class="form-control" name="descripcion" id="editDescripcion" required maxlength="50">
                        </div>
                        <button type="submit" name="editar" class="btn btn-success w-100">
                            <i class="fas fa-check"></i> Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function editar(id, descripcion) {
            document.getElementById('editId').value = id;
            document.getElementById('editDescripcion').value = descripcion;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>
</html>