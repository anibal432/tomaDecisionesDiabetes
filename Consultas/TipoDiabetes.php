<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $descripcion = $_POST['descripcion'];
    $query = "INSERT INTO TipoDiabetes (DESCRIPCION) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $descripcion);
    $stmt->execute();
    $_SESSION['notificacion'] = [
        'mensaje' => 'Tipo de diabetes agregado correctamente',
        'tipo' => 'success'
    ];
    header("Location: TipoDiabetes.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM TipoDiabetes WHERE IdDiabetes = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['notificacion'] = [
        'mensaje' => 'Tipo de diabetes eliminado correctamente',
        'tipo' => 'success'
    ];
    header("Location: TipoDiabetes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $query = "UPDATE TipoDiabetes SET DESCRIPCION=? WHERE IdDiabetes=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $descripcion, $id);
    $stmt->execute();
    $_SESSION['notificacion'] = [
        'mensaje' => 'Tipo de diabetes actualizado correctamente',
        'tipo' => 'success'
    ];
    header("Location: TipoDiabetes.php");
    exit();
}

$idMedico = null;
$esJefeMedico = false;

if (!empty($_SESSION['correo'])) {
    $stmt = $conn->prepare("SELECT IdMedico FROM Medico WHERE CorreoMedico = ?");
    $stmt->bind_param("s", $_SESSION['correo']);
    $stmt->execute();
    $stmt->bind_result($idMedico);
    
    if ($stmt->fetch()) {
        $stmt->free_result();
        $stmt->close();
        
        $stmt_jefe = $conn->prepare("SELECT IdJefeM FROM JefeMed WHERE IdMedico = ?");
        $stmt_jefe->bind_param("i", $idMedico);
        $stmt_jefe->execute();
        $stmt_jefe->store_result();
        
        if ($stmt_jefe->num_rows > 0) {
            $esJefeMedico = true;
        }
        $stmt_jefe->close();
    } else {
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM TipoDiabetes ORDER BY DESCRIPCION");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Diabetes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/tipos.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
    <div class="logo">Diabetes Log</div>
    <ul>            
        <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Gestion de Pacientes</span></a></li>
        <li><a href="../Consultas/TipoDiabetes.php" class="active"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
        <li><a href="../Medico/Pacientes_Turno.php"><i class="fa-solid fa-users-rectangle"></i> <span>Consultas</span></a></li>
        <?php if ($esJefeMedico): ?>
            <li><a href="../insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Gestion de Medicos</span></a></li>
        <?php endif; ?>
        <li><a href="../Medico/subir_resultados.php"><i class="fa-solid fa-flask-vial"></i> <span>Laboratorio</span></a></li>
        <li><a href="../Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>

<div class="container mt-4 main-content">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-vial me-3 text-primary"></i>
                    <h4 class="mb-1 text-primary">Gestión de Tipos de Diabetes</h4>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus me-1"></i> Agregar Tipo
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-hover rounded">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="align-middle"><?= htmlspecialchars($row['IdDiabetes']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($row['DESCRIPCION']) ?></td>
                                    <td class="align-middle text-end">
                                        <button class="btn btn-warning btn-action me-1" 
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                onclick="editar(<?= $row['IdDiabetes'] ?>, '<?= addslashes($row['DESCRIPCION']) ?>')">
                                            <i class="fas fa-pencil-alt me-1"></i> Editar
                                        </button>
                                        <a href="#" 
                                           class="btn btn-danger btn-action eliminar-btn" 
                                           data-id="<?= $row['IdDiabetes'] ?>">
                                            <i class="fas fa-trash me-1"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info mb-0">No hay tipos de diabetes registrados.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary">Agregar Tipo de Diabetes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregar" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" required maxlength="50" 
                               placeholder="Ej: Diabetes Tipo 1, Diabetes Tipo 2, etc.">
                    </div>
                    <button type="submit" name="agregar" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-primary">Editar Tipo de Diabetes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditar" method="POST">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" id="editDescripcion" required maxlength="50">
                    </div>
                    <button type="submit" name="editar" class="btn btn-success w-100">
                        <i class="fas fa-check me-1"></i> Guardar cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function editar(id, descripcion) {
        document.getElementById('editId').value = id;
        document.getElementById('editDescripcion').value = descripcion;
    }

    <?php if (isset($_SESSION['notificacion'])): ?>
        Swal.fire({
            icon: '<?= $_SESSION['notificacion']['tipo'] ?>',
            title: '<?= $_SESSION['notificacion']['mensaje'] ?>',
            showConfirmButton: false,
            timer: 2000
        });
        <?php unset($_SESSION['notificacion']); ?>
    <?php endif; ?>

    document.querySelectorAll('.eliminar-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `TipoDiabetes.php?eliminar=${id}`;
                }
            });
        });
    });

    document.getElementById('formAgregar').addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Agregando tipo...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                this.submit();
            }
        });
    });
    document.getElementById('formEditar').addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Actualizando tipo...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                this.submit();
            }
        });
    });
</script>
</body>
</html>