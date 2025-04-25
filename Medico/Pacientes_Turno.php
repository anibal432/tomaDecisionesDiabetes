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

// Obtener turnos atendiendo ahora (estado = 'Atendiendo')
$sqlAtendiendo = "SELECT 
                    IdTurno,
                    PrimerNombrePac,
                    SegundoNombrePac,
                    PrimerApellidoPac,
                    SegundoApellidoPac,
                    EstadoCita
                FROM 
                    Turnos
                WHERE 
                    IdMedico = ? 
                    AND FechaTurno = CURDATE()
                    AND EstadoCita = 'Atendiendo'
                ORDER BY 
                    IdTurno DESC";

$stmtAtendiendo = $conn->prepare($sqlAtendiendo);
$stmtAtendiendo->bind_param("i", $idMedico);
$stmtAtendiendo->execute();
$resultAtendiendo = $stmtAtendiendo->get_result();

// Obtener turnos en espera
$sqlEnEspera = "SELECT 
                    IdTurno,
                    PrimerNombrePac,
                    SegundoNombrePac,
                    PrimerApellidoPac,
                    SegundoApellidoPac,
                    EstadoCita
                FROM 
                    Turnos
                WHERE 
                    IdMedico = ? 
                    AND FechaTurno = CURDATE()
                    AND EstadoCita IN ('Pendiente', 'Adelante')
                ORDER BY 
                    CASE EstadoCita
                        WHEN 'Adelante' THEN 1
                        WHEN 'Pendiente' THEN 2
                        ELSE 3
                    END,
                    IdTurno DESC";

$stmtEnEspera = $conn->prepare($sqlEnEspera);
$stmtEnEspera->bind_param("i", $idMedico);
$stmtEnEspera->execute();
$resultEnEspera = $stmtEnEspera->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Turnos | Diabetes Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="../css/consultas.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
    <div class="logo">Diabetes Log</div>
    <ul>            
        <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
        <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Gestion de Pacientes</span></a></li>
        <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
        <li><a href="../Medico/Pacientes_Turno.php" class="active"><i class="fa-solid fa-users-rectangle"></i> <span>Consultas</span></a></li>
        <?php if ($esJefeMedico): ?>
            <li><a href="../insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
        <?php endif; ?>
        <li><a href="../Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>
    
<div class="main-content">
    <div class="card-container">
        <div class="page-header">
            <i class="fas fa-users-rectangle"></i>
            <h1>Consultas - <?php echo date('d/m/Y'); ?></h1>
        </div>
        
        <!-- Tabla de Turnos Atendiendo Ahora -->
        <h3 class="section-title"><i class="fas fa-user-clock"></i> Turnos Atendiendo Ahora</h3>
        <?php if ($resultAtendiendo->num_rows > 0): ?>
            <div class="table-responsive mb-5">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th># Turno</th>
                            <th>Nombre del Paciente</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                            <th>Cambiar Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultAtendiendo->fetch_assoc()): ?>
                            <?php
                                $nombreTurno = trim($row['PrimerNombrePac'] . ' ' . $row['SegundoNombrePac'] . ' ' . $row['PrimerApellidoPac'] . ' ' . $row['SegundoApellidoPac']);

                                $queryPac = $conn->prepare("
                                    SELECT IdPaciente 
                                    FROM Paciente 
                                    WHERE CONCAT(IFNULL(NombreUno, ''), ' ', IFNULL(NombreDos, ''), ' ', IFNULL(PrimerApellido, ''), ' ', IFNULL(SegundoApellido, '')) 
                                          LIKE CONCAT('%', ?, '%') 
                                    LIMIT 1
                                ");
                                $queryPac->bind_param("s", $nombreTurno);
                                $queryPac->execute();
                                $resPac = $queryPac->get_result();
                                $idPaciente = ($resPac->num_rows > 0) ? $resPac->fetch_assoc()['IdPaciente'] : 'no_encontrado';
                            ?>
                            <tr>
                                <td><?php echo $row['IdTurno']; ?></td>
                                <td><?php echo $nombreTurno; ?></td>
                                <td class="estado-<?php echo strtolower($row['EstadoCita']); ?>">
                                    <?php echo $row['EstadoCita']; ?>
                                </td>
                                <td>
                                    <?php if ($idPaciente !== 'no_encontrado'): ?>
                                        <div class="d-flex flex-wrap">
                                            <a href="Diagnostico.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-primary btn-action">
                                                <i class="fas fa-file-medical"></i> Diagnóstico
                                            </a>
                                            <a href="formulario_receta.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-success btn-action">
                                                <i class="fas fa-prescription-bottle-alt"></i> Recetar
                                            </a>
                                            <a href="LaboratoriosP.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-info btn-action">
                                                <i class="fas fa-flask"></i> Laboratorios
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Paciente no encontrado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" action="actualizar_turnos.php" class="d-inline">
                                        <input type="hidden" name="id_turno" value="<?php echo $row['IdTurno']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="Atendido">
                                        <button type="submit" name="cambiar_estado" class="btn btn-success btn-cambiar-estado">
                                            <i class="fas fa-check-circle"></i> Marcar Atendido
                                        </button>
                                    </form>
                                    <form method="post" action="actualizar_turnos.php" class="d-inline">
                                        <input type="hidden" name="id_turno" value="<?php echo $row['IdTurno']; ?>">
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
                <i class="fas fa-info-circle"></i> No hay turnos siendo atendidos en este momento.
            </div>
        <?php endif; ?>
        
        <!-- Tabla de Turnos en Espera -->
        <h3 class="section-title"><i class="fas fa-clock"></i> Turnos en Espera</h3>
        <?php if ($resultEnEspera->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th># Turno</th>
                            <th>Nombre del Paciente</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                            <th>Cambiar Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultEnEspera->fetch_assoc()): ?>
                            <?php
                                $nombreTurno = trim($row['PrimerNombrePac'] . ' ' . $row['SegundoNombrePac'] . ' ' . $row['PrimerApellidoPac'] . ' ' . $row['SegundoApellidoPac']);

                                $queryPac = $conn->prepare("
                                    SELECT IdPaciente 
                                    FROM Paciente 
                                    WHERE CONCAT(IFNULL(NombreUno, ''), ' ', IFNULL(NombreDos, ''), ' ', IFNULL(PrimerApellido, ''), ' ', IFNULL(SegundoApellido, '')) 
                                          LIKE CONCAT('%', ?, '%') 
                                    LIMIT 1
                                ");
                                $queryPac->bind_param("s", $nombreTurno);
                                $queryPac->execute();
                                $resPac = $queryPac->get_result();
                                $idPaciente = ($resPac->num_rows > 0) ? $resPac->fetch_assoc()['IdPaciente'] : 'no_encontrado';
                                
                                // Determinar el siguiente estado y texto del botón
                                $siguienteEstado = '';
                                $textoBoton = '';
                                $colorBoton = '';
                                
                                switch($row['EstadoCita']) {
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
                            ?>
                            <tr>
                                <td><?php echo $row['IdTurno']; ?></td>
                                <td><?php echo $nombreTurno; ?></td>
                                <td class="estado-<?php echo strtolower($row['EstadoCita']); ?>">
                                    <?php echo $row['EstadoCita']; ?>
                                </td>
                                <td>
                                    <?php if ($idPaciente !== 'no_encontrado'): ?>
                                        <div class="d-flex flex-wrap">
                                            <a href="Diagnostico.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-primary btn-action">
                                                <i class="fas fa-file-medical"></i> Diagnóstico
                                            </a>
                                            <a href="formulario_receta.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-success btn-action">
                                                <i class="fas fa-prescription-bottle-alt"></i> Recetar
                                            </a>
                                            <a href="LaboratoriosP.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-info btn-action">
                                                <i class="fas fa-flask"></i> Laboratorios
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Paciente no encontrado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" action="actualizar_turnos.php" class="d-inline">
                                        <input type="hidden" name="id_turno" value="<?php echo $row['IdTurno']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="<?php echo $siguienteEstado; ?>">
                                        <button type="submit" name="cambiar_estado" class="btn btn-<?php echo $colorBoton; ?> btn-cambiar-estado">
                                            <i class="fas fa-arrow-right"></i> <?php echo $textoBoton; ?>
                                        </button>
                                    </form>
                                    <form method="post" action="actualizar_turnos.php" class="d-inline">
                                        <input type="hidden" name="id_turno" value="<?php echo $row['IdTurno']; ?>">
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
                <i class="fas fa-info-circle"></i> No hay turnos en espera para hoy.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>