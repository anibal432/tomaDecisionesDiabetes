<?php
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

$nombreMedico = '';
if (!empty($idMedico)) {
    $stmt = $conn->prepare("SELECT CONCAT(PrimerNombre, ' ', SegundoNombre, ' ', PrimerApellido, ' ', SegundoApellido) AS NombreCompleto FROM Medico WHERE IdMedico = ?");
    $stmt->bind_param("i", $idMedico);
    $stmt->execute();
    $stmt->bind_result($nombreMedico);
    $stmt->fetch();
    $stmt->close();
}

$sqlAtendiendo = "(SELECT 
                    IdTurno as id,
                    'turno' as tipo,
                    PrimerNombrePac,
                    SegundoNombrePac,
                    PrimerApellidoPac,
                    SegundoApellidoPac,
                    NoDpi,
                    Telefono,
                    FechaNacimiento,
                    Sexo,
                    GrupoEtnico,
                    EstadoCita as estado,
                    NULL as hora
                FROM 
                    Turnos
                WHERE 
                    IdMedico = ? 
                    AND FechaTurno = CURDATE()
                    AND EstadoCita = 'Atendiendo')
                UNION ALL
                (SELECT 
                    id,
                    'cita' as tipo,
                    primer_nombre as PrimerNombrePac,
                    segundo_nombre as SegundoNombrePac,
                    primer_apellido as PrimerApellidoPac,
                    segundo_apellido as SegundoApellidoPac,
                    NoDpi,
                    numero_celular as Telefono,
                    FechaNacimiento,
                    Sexo,
                    GrupoEtnico,
                    estado,
                    hora
                FROM 
                    citas
                WHERE 
                    IdMedico = ?
                    AND fecha = CURDATE()
                    AND estado = 'Atendiendo')
                ORDER BY 
                    tipo, id DESC";

$stmtAtendiendo = $conn->prepare($sqlAtendiendo);
$stmtAtendiendo->bind_param("ii", $idMedico, $idMedico);
$stmtAtendiendo->execute();
$resultAtendiendo = $stmtAtendiendo->get_result();

$sqlEnEspera = "(SELECT 
                    IdTurno as id,
                    'turno' as tipo,
                    PrimerNombrePac,
                    SegundoNombrePac,
                    PrimerApellidoPac,
                    SegundoApellidoPac,
                    EstadoCita as estado,
                    NULL as hora
                FROM 
                    Turnos
                WHERE 
                    IdMedico = ? 
                    AND FechaTurno = CURDATE()
                    AND EstadoCita IN ('Pendiente', 'Adelante'))
                UNION ALL
                (SELECT 
                    id,
                    'cita' as tipo,
                    primer_nombre as PrimerNombrePac,
                    segundo_nombre as SegundoNombrePac,
                    primer_apellido as PrimerApellidoPac,
                    segundo_apellido as SegundoApellidoPac,
                    estado,
                    hora
                FROM 
                    citas
                WHERE 
                    IdMedico = ?
                    AND fecha = CURDATE()
                    AND estado IN ('Pendiente', 'Confirmada'))
                ORDER BY 
                    CASE 
                        WHEN tipo = 'turno' AND estado = 'Adelante' THEN 1
                        WHEN tipo = 'turno' AND estado = 'Pendiente' THEN 2
                        WHEN tipo = 'cita' AND estado = 'Confirmada' THEN 3
                        WHEN tipo = 'cita' AND estado = 'Pendiente' THEN 4
                        ELSE 5
                    END,
                    CASE WHEN tipo = 'cita' THEN hora ELSE id END";

$stmtEnEspera = $conn->prepare($sqlEnEspera);
$stmtEnEspera->bind_param("ii", $idMedico, $idMedico);
$stmtEnEspera->execute();
$resultEnEspera = $stmtEnEspera->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Turnos y Citas | Diabetes Log</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/consultas.css">
</head>
<style>
    .badge-tipo {
    font-size: 0.75rem;
    margin-left: 5px;
}
.badge-turno {
    background-color: #6c757d;
}
.badge-cita {
    background-color: #0d6efd;
}
.hora-cita {
    font-size: 0.85rem;
    color: #6c757d;
    margin-left: 5px;
}
</style>

<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
    <div class="logo">Diabetes Log</div>
    <ul>            
        <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Gestion de Pacientes</span></a></li>
        <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
        <li><a href="../Medico/Pacientes_Turno.php" class="active"><i class="fa-solid fa-users-rectangle"></i> <span>Consultas</span></a></li>
        <?php if ($esJefeMedico): ?>
            <li><a href="../insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Gestion de Medicos</span></a></li>
        <?php endif; ?>
        <li><a href="../Medico/subir_resultados.php"><i class="fa-solid fa-flask-vial"></i> <span>Laboratorio</span></a></li>
        <li><a href="../Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>
    
<div class="main-content">
    <div class="card-container">
        <div class="page-header">
            <i class="fas fa-users-rectangle"></i>
            <h1>Consultas - <?php echo date('d/m/Y'); ?></h1>
        </div>
        
        <h3 class="section-title"><i class="fas fa-user-clock"></i> Atendiendo Ahora</h3>
        <?php if ($resultAtendiendo->num_rows > 0): ?>
            <div class="table-responsive mb-5">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Nombre del Paciente</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                            <th>Cambiar Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultAtendiendo->fetch_assoc()): ?>
                            <?php
                                $nombreCompleto = trim($row['PrimerNombrePac'] . ' ' . $row['SegundoNombrePac'] . ' ' . 
                                                    $row['PrimerApellidoPac'] . ' ' . $row['SegundoApellidoPac']);
                                
                                $queryPac = $conn->prepare("
                                    SELECT IdPaciente 
                                    FROM Paciente 
                                    WHERE NoDpi = ?
                                    LIMIT 1
                                ");
                                $queryPac->bind_param("s", $row['NoDpi']);
                                $queryPac->execute();
                                $resPac = $queryPac->get_result();
                                $idPaciente = ($resPac->num_rows > 0) ? $resPac->fetch_assoc()['IdPaciente'] : null;
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['tipo'] ?>">
                                        <?php echo ucfirst($row['tipo']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $nombreCompleto; ?>
                                    <?php if ($row['tipo'] == 'cita' && !empty($row['hora'])): ?>
                                        <span class="hora-cita">
                                            <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($row['hora'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="estado-<?php echo strtolower($row['estado']); ?>">
                                    <?php echo $row['estado']; ?>
                                </td>
                                <td>
                                    <?php if ($idPaciente): ?>
                                        <div class="d-flex flex-wrap">
                                            <button class="btn btn-primary btn-action btn-diagnostico" data-id="<?php echo $idPaciente; ?>">
                                                 <i class="fas fa-file-medical"></i> Diagnóstico
                                            </button>
                                            <button class="btn btn-success btn-action btn-receta" data-id="<?php echo $idPaciente; ?>" data-nombre="<?php echo htmlspecialchars($nombreCompleto); ?>">
                                                <i class="fas fa-prescription-bottle-alt"></i> Recetar
                                            </button>
                                            <button class="btn btn-info btn-action btn-laboratorio" 
        data-id="<?php echo $idPaciente; ?>" 
        data-nombre="<?php echo htmlspecialchars($nombreCompleto); ?>">
    <i class="fas fa-flask"></i> Laboratorios
</button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Paciente no registrado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" action="actualizar_consulta.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="tipo" value="<?php echo $row['tipo']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="<?php echo $row['tipo'] == 'turno' ? 'Atendido' : 'Atendida'; ?>">
                                        <button type="submit" name="cambiar_estado" class="btn btn-success btn-cambiar-estado">
                                            <i class="fas fa-check-circle"></i> Marcar Atendido
                                        </button>
                                    </form>
                                    <form method="post" action="actualizar_consulta.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="tipo" value="<?php echo $row['tipo']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="Cancelado">
                                        <button type="submit" name="cambiar_estado" class="btn btn-danger btn-cambiar-estado">
                                            <i class="fas fa-times-circle"></i> Cancelar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay consultas siendo atendidas en este momento.
            </div>
        <?php endif; ?>
        
        <h3 class="section-title"><i class="fas fa-clock"></i> En Espera</h3>
        <?php if ($resultEnEspera->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Nombre del Paciente</th>
                            <th>Estado</th>
                            <th>Cambiar Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultEnEspera->fetch_assoc()): ?>
                            <?php
                                $nombreCompleto = trim($row['PrimerNombrePac'] . ' ' . $row['SegundoNombrePac'] . ' ' . 
                                                    $row['PrimerApellidoPac'] . ' ' . $row['SegundoApellidoPac']);
                                
                                if ($row['tipo'] == 'turno') {
                                    switch($row['estado']) {
                                        case 'Pendiente':
                                            $siguienteEstado = 'Adelante';
                                            $textoBoton = 'Llamar Paciente';
                                            $colorBoton = 'warning';
                                            break;
                                        case 'Adelante':
                                            $siguienteEstado = 'Atendiendo';
                                            $textoBoton = 'Iniciar Consulta';
                                            $colorBoton = 'primary';
                                            break;
                                        default:
                                            $siguienteEstado = 'Atendiendo';
                                            $textoBoton = 'Iniciar Consulta';
                                            $colorBoton = 'primary';
                                    }
                                } else {
                                    $siguienteEstado = 'Atendiendo';
                                    $textoBoton = 'Iniciar Consulta';
                                    $colorBoton = 'primary';
                                }
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['tipo'] ?>">
                                        <?php echo ucfirst($row['tipo']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $nombreCompleto; ?>
                                    <?php if ($row['tipo'] == 'cita' && !empty($row['hora'])): ?>
                                        <span class="hora-cita">
                                            <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($row['hora'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="estado-<?php echo strtolower($row['estado']); ?>">
                                    <?php echo $row['estado']; ?>
                                </td>
                                <td>
                                    <form method="post" action="actualizar_consulta.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="tipo" value="<?php echo $row['tipo']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="<?php echo $siguienteEstado; ?>">
                                        <button type="submit" name="cambiar_estado" class="btn btn-<?php echo $colorBoton; ?> btn-cambiar-estado">
                                            <i class="fas fa-arrow-right"></i> <?php echo $textoBoton; ?>
                                        </button>
                                    </form>
                                    <form method="post" action="actualizar_consulta.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="tipo" value="<?php echo $row['tipo']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="Cancelado">
                                        <button type="submit" name="cambiar_estado" class="btn btn-danger btn-cambiar-estado">
                                            <i class="fas fa-times-circle"></i> Cancelar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No hay consultas en espera para hoy.
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Modal de Diagnóstico -->
<div class="modal fade" id="diagnosticoModal" tabindex="-1" aria-labelledby="diagnosticoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title text-primary d-flex align-items-center" id="diagnosticoModalLabel">
                    <i class="fas fa-notes-medical me-2"></i> Registro de Diagnósticos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Historial de Diagnósticos</h6>
                    <div class="diagnosticos-container">
                        <ul id="listaDiagnosticos" class="list-group">
                        </ul>
                    </div>
                </div>
                
                <div class="nuevo-diagnostico">
                    <h6 class="border-bottom pb-2">Agregar Nuevos Diagnósticos</h6>
                    <form id="diagnosticForm">
                        <div class="mb-3">
                            <label for="search" class="form-label">Buscar en CIE-10:</label>
                            <div class="input-group">
                                <input type="text" id="search" class="form-control" placeholder="Código o descripción..." autocomplete="off">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <ul id="searchResults" class="list-group search-results"></ul>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Diagnósticos seleccionados:</label>
                            <ul id="selectedDiagnosticos" class="list-group">
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Diagnósticos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Recetas -->
<div class="modal fade" id="recetaModal" tabindex="-1" aria-labelledby="recetaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-primary">
            <h5 class="modal-title text-primary d-flex align-items-center" id="diagnosticoModalLabel">
            <i class="fa-solid fa-prescription-bottle-medical me-2"></i> Receta Medica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Historial de Recetas</h6>
                    <div class="recetas-container">
                        <ul id="listaRecetas" class="list-group">
                        </ul>
                    </div>
                </div>
                
                <div class="nueva-receta">
                    <h6 class="border-bottom pb-2">Nueva Receta</h6>
                    <form id="recetaForm">
                        <input type="hidden" id="id_paciente_receta" name="id_paciente">
                        
                        <div class="mb-3">
                            <label class="form-label">Paciente</label>
                            <input type="text" id="nombre_paciente_receta" class="form-control" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Medicamentos</label>
                            <div id="medicamentosContainer">
                                <div class="row mb-2 medicamento-grupo">
                                    <div class="col-md-5">
                                        <input type="text" name="medicamentos[]" class="form-control" placeholder="Nombre del medicamento" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="observaciones_detalle[]" class="form-control" placeholder="Indicaciones (ej: cada 8 horas)">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-medicamento">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="btnAgregarMedicamento" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Agregar medicamento
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Observaciones generales</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Instrucciones adicionales"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Guardar Receta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Solicitud de Exámenes -->
<div class="modal fade" id="laboratorioModal" tabindex="-1" aria-labelledby="laboratorioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary d-flex align-items-center" id="laboratorioModalLabel">
                    <i class="fas fa-flask me-2"></i> Solicitud de Examenes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Historial de Solicitudes</h6>
                    <div class="laboratorios-container">
                        <ul id="listaLaboratorios" class="list-group">
                        </ul>
                    </div>
                </div>
                
                <div class="nueva-solicitud">
                    <h6 class="border-bottom pb-2">Nueva Solicitud</h6>
                    <form id="laboratorioForm">
                        <input type="hidden" id="id_paciente_laboratorio" name="IdPaciente">
                        <input type="hidden" id="id_medico_laboratorio" name="IdMedico">
                        
                        <div class="mb-3">
                            <label class="form-label">Paciente</label>
                            <input type="text" id="nombre_paciente_laboratorio" class="form-control" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Médico Solicitante</label>
                            <input type="text" id="nombre_medico_laboratorio" class="form-control" value="<?php echo isset($nombreMedico) ? $nombreMedico : ''; ?>" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Exámenes Solicitados <span class="text-danger">*</span></label>
                            <textarea name="ExamenesSolicitados" class="form-control" rows="3" placeholder="Ej: Hemograma completo, Glicemia en ayunas..." required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Instrucciones</label>
                            <textarea name="Instrucciones" class="form-control" rows="2" placeholder="Indicaciones especiales para los exámenes"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let currentPacienteId = null;
    
    function cargarDiagnosticos(idPaciente) {
        $.ajax({
            url: 'obtener_diagnosticos.php',
            type: 'GET',
            data: { idPaciente: idPaciente },
            beforeSend: function() {
                $('#listaDiagnosticos').html('<li class="list-group-item text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></li>');
            },
            success: function(response) {
                $('#listaDiagnosticos').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar diagnósticos:", error);
                $('#listaDiagnosticos').html('<li class="list-group-item text-danger">Error al cargar diagnósticos: ' + error + '</li>');
            }
        });
    }

    function eliminarDiagnostico(idDiagnostico) {
        Swal.fire({
            title: '¿Eliminar diagnóstico?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const $button = $(`.btn-eliminar-diagnostico[data-id="${idDiagnostico}"]`);
                const originalHtml = $button.html();
                
                $button.html('<i class="fas fa-spinner fa-spin"></i>');
                $button.prop('disabled', true);
                
                $.ajax({
                    url: 'eliminar_diagnostico.php',
                    type: 'POST',
                    data: { idDiagnostico: idDiagnostico },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.status === 'success') {
                            cargarDiagnosticos(currentPacienteId);
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire('Error', data?.message || 'Error al eliminar el diagnóstico', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la solicitud AJAX:", xhr.responseText);
                        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                    },
                    complete: function() {
                        $button.html(originalHtml);
                        $button.prop('disabled', false);
                    }
                });
            }
        });
    }

    $(document).on('click', '.btn-diagnostico', function() {
        currentPacienteId = $(this).data('id');
        $('#diagnosticoModal').modal('show');
        cargarDiagnosticos(currentPacienteId);
        resetearFormulario();
    });

    function resetearFormulario() {
        $('#search').val('');
        $('#searchResults').empty();
        $('#selectedDiagnosticos').empty();
    }

    $('#search').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        if (searchTerm.length > 2) {
            $.ajax({
                url: 'Diagnostico.php',
                type: 'POST',
                data: { search: searchTerm },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        $('#searchResults').empty();
                        
                        if (data.length > 0) {
                            data.forEach(function(diagnostic) {
                                $('#searchResults').append(
                                    `<li class="list-group-item" data-id="${diagnostic.IdCIE10}">
                                        <strong>${diagnostic.Codigo}</strong> - ${diagnostic.Descripcion}
                                    </li>`
                                );
                            });
                        } else {
                            $('#searchResults').html('<li class="list-group-item text-muted">No se encontraron resultados</li>');
                        }
                    } catch (e) {
                        $('#searchResults').html('<li class="list-group-item text-danger">Error al procesar resultados</li>');
                    }
                },
                error: function() {
                    $('#searchResults').html('<li class="list-group-item text-danger">Error en la búsqueda</li>');
                }
            });
        } else {
            $('#searchResults').empty();
        }
    });
    
    $('#searchResults').on('click', 'li', function() {
        const idCIE10 = $(this).data('id');
        const texto = $(this).text();
        const selectedList = $('#selectedDiagnosticos');
        
        if (selectedList.children().length < 5 && !selectedList.find(`li[data-id="${idCIE10}"]`).length) {
            selectedList.append(
                `<li class="list-group-item d-flex justify-content-between align-items-center" data-id="${idCIE10}">
                    ${texto}
                    <button class="btn btn-danger btn-sm remove-diagnostic">
                        <i class="fas fa-times"></i>
                    </button>
                </li>`
            );
        }
        
        $('#searchResults').empty();
        $('#search').val('');
    });
    
    $('#selectedDiagnosticos').on('click', '.remove-diagnostic', function(e) {
        e.stopPropagation();
        $(this).closest('li').remove();
    });
    
    $('#diagnosticForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentPacienteId) {
            Swal.fire('Error', 'No se identificó al paciente', 'error');
            return;
        }
        
        const diagnosticos = [];
        $('#selectedDiagnosticos li').each(function() {
            diagnosticos.push($(this).data('id'));
        });
        
        if (diagnosticos.length === 0) {
            Swal.fire('Error', 'Por favor seleccione al menos un diagnóstico', 'error');
            return;
        }
        
        const $submitBtn = $('#diagnosticForm button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Guardando...');
        
        $.ajax({
            url: 'Diagnostico.php',
            type: 'POST',
            data: { 
                insertDiagnosticos: true, 
                diagnosticos: diagnosticos, 
                idPaciente: currentPacienteId 
            },
            dataType: 'json',
            success: function(data) {
                if (data && data.status === 'success') {
                    cargarDiagnosticos(currentPacienteId);
                    resetearFormulario();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Error', data?.message || 'Error al guardar los diagnósticos', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", xhr.responseText);
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar Diagnósticos');
            }
        });
    });
    
    $('#diagnosticoModal').on('hidden.bs.modal', function() {
        resetearFormulario();
        currentPacienteId = null;
    });

    $(document).on('click', '.btn-eliminar-diagnostico', function(e) {
        e.preventDefault();
        const idDiagnostico = $(this).data('id');
        eliminarDiagnostico(idDiagnostico);
    });
});
</script>



<script>
$(document).ready(function() {
    let currentPacienteId = null;
    let currentPacienteNombre = null;

    function cargarRecetas(idPaciente) {
        $.ajax({
            url: 'obtener_recetas.php',
            type: 'GET',
            data: { idPaciente: idPaciente },
            beforeSend: function() {
                $('#listaRecetas').html('<li class="list-group-item text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></li>');
            },
            success: function(response) {
                $('#listaRecetas').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar recetas:", error);
                $('#listaRecetas').html('<li class="list-group-item text-danger">Error al cargar recetas</li>');
            }
        });
    }

    function resetearFormularioReceta() {
        $('#medicamentosContainer').html(`
            <div class="row mb-2 medicamento-grupo">
                <div class="col-md-5">
                    <input type="text" name="medicamentos[]" class="form-control" placeholder="Nombre del medicamento" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1" value="1" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="observaciones_detalle[]" class="form-control" placeholder="Indicaciones (ej: cada 8 horas)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-medicamento">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);
        $('textarea[name="observaciones"]').val('');
    }

    function eliminarReceta(idReceta) {
        Swal.fire({
            title: '¿Eliminar receta?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const $button = $(`.btn-eliminar-receta[data-id="${idReceta}"]`);
                const originalHtml = $button.html();
                
                $button.html('<i class="fas fa-spinner fa-spin"></i>');
                $button.prop('disabled', true);
                
                $.ajax({
                    url: 'eliminar_receta.php',
                    type: 'POST',
                    data: { idReceta: idReceta },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.status === 'success') {
                            cargarRecetas(currentPacienteId);
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminada!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire('Error', data?.message || 'Error al eliminar la receta', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la solicitud AJAX:", xhr.responseText);
                        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                    },
                    complete: function() {
                        $button.html(originalHtml);
                        $button.prop('disabled', false);
                    }
                });
            }
        });
    }

    $(document).on('click', '.btn-receta', function() {
        currentPacienteId = $(this).data('id');
        currentPacienteNombre = $(this).data('nombre');
        
        $('#recetaModal').modal('show');
        $('#id_paciente_receta').val(currentPacienteId);
        $('#nombre_paciente_receta').val(currentPacienteNombre);
        
        cargarRecetas(currentPacienteId);
        resetearFormularioReceta();
    });

    $('#btnAgregarMedicamento').click(function() {
        $('#medicamentosContainer').append(`
            <div class="row mb-2 medicamento-grupo">
                <div class="col-md-5">
                    <input type="text" name="medicamentos[]" class="form-control" placeholder="Nombre del medicamento" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1" value="1" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="observaciones_detalle[]" class="form-control" placeholder="Indicaciones (ej: cada 8 horas)">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-medicamento">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.remove-medicamento', function() {
        if ($('.medicamento-grupo').length > 1) {
            $(this).closest('.medicamento-grupo').remove();
        } else {
            Swal.fire('Atención', 'Debe haber al menos un medicamento', 'warning');
        }
    });

    $('#recetaForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentPacienteId) {
            Swal.fire('Error', 'No se identificó al paciente', 'error');
            return;
        }
        
        let medicamentosValidos = false;
        $('input[name="medicamentos[]"]').each(function() {
            if ($(this).val().trim() !== '') {
                medicamentosValidos = true;
                return false;
            }
        });
        
        if (!medicamentosValidos) {
            Swal.fire('Error', 'Debe ingresar al menos un medicamento', 'error');
            return;
        }
        
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Guardando...');
        
        $.ajax({
            url: 'guardar_receta.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data && data.status === 'success') {
                    cargarRecetas(currentPacienteId);
                    resetearFormularioReceta();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire('Error', data?.message || 'Error al guardar la receta', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", xhr.responseText);
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar Receta');
            }
        });
    });

    $(document).on('click', '.btn-eliminar-receta', function(e) {
        e.preventDefault();
        const idReceta = $(this).data('id');
        eliminarReceta(idReceta);
    });

    $('#recetaModal').on('hidden.bs.modal', function() {
        currentPacienteId = null;
        currentPacienteNombre = null;
    });
});
</script>

<script>
$(document).ready(function() {
    let currentPacienteId = null;
    let currentPacienteNombre = null;
    let currentMedicoId = <?php echo $idMedico ?? 'null'; ?>;
    let currentMedicoNombre = "<?php echo isset($nombreMedico) ? addslashes($nombreMedico) : ''; ?>";

    function cargarSolicitudesExamenes(idPaciente) {
        $.ajax({
            url: 'obtener_solicitudes_examenes.php',
            type: 'GET',
            data: { idPaciente: idPaciente },
            beforeSend: function() {
                $('#listaLaboratorios').html('<li class="list-group-item text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div> Cargando historial...</li>');
            },
            success: function(response) {
                $('#listaLaboratorios').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar solicitudes:", error);
                $('#listaLaboratorios').html('<li class="list-group-item text-danger"><i class="fas fa-exclamation-circle me-2"></i>Error al cargar historial de solicitudes</li>');
            }
        });
    }

    function eliminarSolicitudExamen(idSolicitud) {
        Swal.fire({
            title: '¿Eliminar solicitud?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const $button = $(`.btn-eliminar-solicitud[data-id="${idSolicitud}"]`);
                const originalHtml = $button.html();
                
                $button.html('<i class="fas fa-spinner fa-spin"></i>');
                $button.prop('disabled', true);
                
                $.ajax({
                    url: 'eliminar_solicitud_examen.php',
                    type: 'POST',
                    data: { idSolicitud: idSolicitud },
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.status === 'success') {
                            cargarSolicitudesExamenes(currentPacienteId);
                            Swal.fire({
                                icon: 'success',
                                title: '¡Eliminada!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire('Error', data?.message || 'Error al eliminar la solicitud', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la solicitud AJAX:", xhr.responseText);
                        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                    },
                    complete: function() {
                        $button.html(originalHtml);
                        $button.prop('disabled', false);
                    }
                });
            }
        });
    }

    $(document).on('click', '.btn-laboratorio', function() {
        currentPacienteId = $(this).data('id');
        currentPacienteNombre = $(this).data('nombre');
        
        console.log('Datos del paciente:', {
            id: currentPacienteId,
            nombre: currentPacienteNombre
        });
        console.log('Datos del médico:', {
            id: currentMedicoId,
            nombre: currentMedicoNombre
        });

        $('#laboratorioModal').modal('show');
        $('#id_paciente_laboratorio').val(currentPacienteId);
        $('#nombre_paciente_laboratorio').val(currentPacienteNombre || 'Nombre no disponible');
        $('#id_medico_laboratorio').val(currentMedicoId);
        $('#nombre_medico_laboratorio').val(currentMedicoNombre || 'Médico no disponible');
        
        cargarSolicitudesExamenes(currentPacienteId);
        
        $('#laboratorioForm textarea').val('');
    });

    $('#laboratorioForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentPacienteId || !currentMedicoId) {
            Swal.fire('Error', 'Datos incompletos del paciente o médico', 'error');
            return;
        }
        
        const examenes = $('textarea[name="ExamenesSolicitados"]').val().trim();
        if (!examenes) {
            Swal.fire('Error', 'Debe especificar los exámenes solicitados', 'error');
            return;
        }
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');
        
        $.ajax({
            url: 'guardar_solicitud_examen.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data && data.status === 'success') {                    
                    cargarSolicitudesExamenes(currentPacienteId);
                    
                    $('#laboratorioForm textarea').val('');
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Solicitud enviada!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data?.message || 'Error al enviar la solicitud',
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la solicitud AJAX:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonText: 'Entendido'
                });
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    $(document).on('click', '.btn-eliminar-solicitud', function(e) {
        e.preventDefault();
        const idSolicitud = $(this).data('id');
        eliminarSolicitudExamen(idSolicitud);
    });
    $('#laboratorioModal').on('hidden.bs.modal', function() {
        currentPacienteId = null;
        currentPacienteNombre = null;
        $('#listaLaboratorios').empty();
    });
});
</script>

</body>
</html>