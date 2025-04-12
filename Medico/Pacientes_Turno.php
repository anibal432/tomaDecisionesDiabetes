<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'conexion.php';

// Consulta directa a la tabla Turnos
$sql = "SELECT 
            IdTurno,
            PrimerNombrePac,
            SegundoNombrePac,
            PrimerApellidoPac,
            SegundoApellidoPac,
            EstadoCita
        FROM 
            Turnos
        ORDER BY 
            IdTurno DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .estado-activo {
            color: green;
            font-weight: bold;
        }
        .estado-inactivo {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container">
            <h1>Listado de Turnos</h1>
            <p>Total de turnos registrados</p>
        </div>
    </div>
    
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th># Turno</th>
                            <th>Nombre del Paciente</th>
                            <th>Estado de la Cita</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <?php
                                // Armar nombre completo del paciente en Turnos
                                $nombreTurno = trim($row['PrimerNombrePac'] . ' ' . $row['SegundoNombrePac'] . ' ' . $row['PrimerApellidoPac'] . ' ' . $row['SegundoApellidoPac']);

                                // Buscar en la tabla Paciente un nombre parecido
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
                                <td class="<?php echo ($row['EstadoCita'] === 'Activo') ? 'estado-activo' : 'estado-inactivo'; ?>">
                                    <?php echo $row['EstadoCita']; ?>
                                </td>
                                <td>
                                    <?php if ($idPaciente !== 'no_encontrado'): ?>
                                        <a href="Diagnostico.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-primary btn-sm">Diagnóstico</a>
                                        <a href="formulario_receta.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-success btn-sm mt-1">Recetar</a>
                                        <a href="LaboratoriosP.php?id_paciente=<?php echo $idPaciente; ?>" class="btn btn-info btn-sm mt-1">Laboratorios</a>
                                    <?php else: ?>
                                        <span class="text-danger">Paciente no encontrado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                No hay turnos registrados en el sistema.
            </div>
        <?php endif; ?>
    </div>

    <footer class="mt-5 py-3 bg-light text-center">
        <div class="container">
            <p class="mb-0">Sistema de Gestión de Turnos - <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
