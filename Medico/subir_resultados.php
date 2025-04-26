<?php
require_once '../conexionL.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_resultado'])) {
    header('Content-Type: application/json');
    try {
        if (empty($_POST['id_solicitud'])) {
            throw new Exception('ID de solicitud es requerido');
        }

        $idSolicitud = intval($_POST['id_solicitud']);
        $archivo = $_FILES['archivo_resultado'];
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo: ' . $archivo['error']);
        }

        $extension = strtoupper(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $tiposPermitidos = ['PDF', 'JPG', 'PNG', 'DOCX', 'XLSX'];
        if (!in_array($extension, $tiposPermitidos)) {
            throw new Exception('Tipo de archivo no permitido.');
        }

        $uploadDir = '../Medico/uploads/resultados/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $uploadDir . $nombreUnico;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            throw new Exception('Error al guardar el archivo.');
        }

        $stmt = $conn->prepare("SELECT IdPaciente FROM SolicitudExamenes WHERE IdSolicitud = ?");
        $stmt->bind_param("i", $idSolicitud);
        $stmt->execute();
        $stmt->bind_result($idPaciente);
        if (!$stmt->fetch()) {
            throw new Exception("No se encontró la solicitud.");
        }
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO ResultadosPaciente 
            (IdSolicitud, NombreArchivo, RutaArchivo, TipoArchivo) 
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $idSolicitud, $archivo['name'], $rutaCompleta, $extension);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            throw new Exception('No se pudo registrar el archivo.');
        }
        $stmt->close();

        require_once 'enviar_correo.php';
        enviarResultadoYActualizar($conn, $idPaciente, $idSolicitud, $rutaCompleta);

        echo json_encode(['success' => true, 'message' => 'Archivo subido correctamente']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}


$solicitudes = [];
try {
    $query = "SELECT s.IdSolicitud, p.NombreUno, p.PrimerApellido, s.ExamenesSolicitados, s.FechaSolicitud 
              FROM SolicitudExamenes s
              JOIN Paciente p ON s.IdPaciente = p.IdPaciente
              WHERE s.Estado = 'Pendiente'
              ORDER BY s.FechaSolicitud DESC";
    $result = $conn->query($query);
    $solicitudes = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error = "Error al obtener solicitudes: " . $e->getMessage();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../conexionL.php');

session_start();
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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes | Diabetes Log</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/solicitud.css">
</head>
<body class="container mt-5">
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
    <div class="logo">Diabetes Log</div>
    <ul>            
        <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Gestion de Pacientes</span></a></li>
        <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
        <li><a href="../Medico/Pacientes_Turno.php"><i class="fa-solid fa-users-rectangle"></i> <span>Consultas</span></a></li>
        <?php if ($esJefeMedico): ?>
            <li><a href="../insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Gestion de Medicos</span></a></li>
        <?php endif; ?>
        <li><a href="../Medico/subir_resultados.php" class="active"><i class="fa-solid fa-flask-vial"></i> <span>Laboratorio</span></a></li>
        <li><a href="../Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>
<div class="main-content">
    <h2 class="page-title">
        <i class="fas fa-file-upload"></i> Solicitudes Pendientes
    </h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="cards-container">
        <div class="cards-grid">
            <?php foreach ($solicitudes as $solicitud): ?>
                <div class="card-solicitud">
                    <div class="card-body">
                        <h5 class="card-title">#<?= $solicitud['IdSolicitud'] ?> - <?= $solicitud['NombreUno'] . ' ' . $solicitud['PrimerApellido'] ?></h5>
                        <p class="card-text">
                            <strong>Exámenes:</strong> <?= htmlspecialchars($solicitud['ExamenesSolicitados']) ?><br>
                            <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($solicitud['FechaSolicitud'])) ?>
                        </p>
                        <button class="btn btn-primary btn-subir btnSubir" 
                                data-id="<?= $solicitud['IdSolicitud'] ?>" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalSubida">
                            <i class="fas fa-upload me-2"></i> Subir Resultado
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<div class="modal fade" id="modalSubida" tabindex="-1" aria-labelledby="modalSubidaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formSubida" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-upload me-2"></i> Subir Resultado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_solicitud" id="modal_id_solicitud">
                    <div class="mb-3">
                        <label for="archivo_resultado" class="form-label">Seleccionar archivo</label>
                        <input class="form-control" type="file" name="archivo_resultado" id="archivo_resultado" required accept=".pdf,.jpg,.png,.docx,.xlsx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i> Subir</button>
                    <button type="button" id="btnCancelarSolicitud" class="btn btn-danger"><i class="fas fa-times me-2"></i> Cancelar Solicitud</button>
                </div>
            </div>
        </form>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let idActual = 0;

$('.btnSubir').on('click', function () {
    const idSolicitud = $(this).data('id');
    $('#modal_id_solicitud').val(idSolicitud);
});

function actualizarEstadoSolicitud(idSolicitud, estado) {
    return $.ajax({
        url: 'actualizar_estado.php',
        type: 'POST',
        data: { 
            id_solicitud: idSolicitud,
            estado: estado
        },
        dataType: 'json'
    });
}

$('#formSubida').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const idSolicitud = $('#modal_id_solicitud').val();

    $.ajax({
        url: 'subir_resultados.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            if(resp.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: resp.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    $('#modalSubida').modal('hide');
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: resp.message
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || { message: 'Error inesperado' };
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message
            });
        }
    });
});

$('#btnCancelarSolicitud').on('click', function() {
    const idSolicitud = $('#modal_id_solicitud').val();
    
    Swal.fire({
        title: '¿Cancelar solicitud?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, volver'
    }).then((result) => {
        if (result.isConfirmed) {
            actualizarEstadoSolicitud(idSolicitud, 'Cancelado')
                .done(function(resp) {
                    if(resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Solicitud cancelada',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#modalSubida').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resp.message
                        });
                    }
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cancelar la solicitud'
                    });
                });
        }
    });
});
</script>
</body>
</html>

