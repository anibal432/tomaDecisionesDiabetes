<?php
require_once 'conexion.php';

// Agregamos IdPaciente a la consulta para poder pasarlo como parámetro
$sql = "SELECT 
            t.IdTurno,
            p.IdPaciente,
            CONCAT_WS(' ', p.NombreUno, p.NombreDos, p.PrimerApellido, p.SegundoApellido) AS NombreCompleto,
            ft.FechaAtencion,
            t.IdTurnoAsignado,
            t.Estado
        FROM 
            Turnos t
        INNER JOIN 
            Paciente p ON t.IdPaciente = p.IdPaciente
        INNER JOIN
            FechaTurno ft ON t.IdTurnoAsignado = ft.IdTurnoAsignado
        ORDER BY 
            t.IdTurno DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Completo de Turnos</title>
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
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1 class="text-center">Listado Completo de Turnos</h1>
            <p class="text-center mb-0">Total de turnos registrados</p>
        </div>
    </div>
    
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th># Turno</th>
                            <th>Nombre Completo</th>
                            <th>Fecha de Atención</th>
                            <th>Turno Asignado</th>
                            <th>Estado</th>
                            <th>Acción</th> <!-- Columna para el botón -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['IdTurno']; ?></td>
                                <td><?php echo $row['NombreCompleto']; ?></td>
                                <td><?php echo date('d-m-Y H:i:s', strtotime($row['FechaAtencion'])); ?></td> <!-- Formatea la fecha -->
                                <td><?php echo $row['IdTurnoAsignado']; ?></td>
                                <td class="<?php echo ($row['Estado'] == 1) ? 'estado-activo' : 'estado-inactivo'; ?>">
                                    <?php echo ($row['Estado'] == 1) ? 'Activo' : 'Inactivo'; ?>
                                </td>
                                <td>
                                      <!-- Botón para ir a diagnóstico -->
                                        <a href="Diagnostico.php?turno=<?php echo $row['IdTurno']; ?>" class="btn btn-primary btn-sm">Diagnóstico</a>

                                        <!-- Botón para ir a formulario_recetas.php enviando el IdPaciente -->
                                         <a href="formulario_receta.php?id_paciente=<?php echo $row['IdPaciente']; ?>" class="btn btn-success btn-sm mt-1">Recetar</a>
                                         <!-- Botón para ir a formulario_recetas.php enviando el IdPaciente -->

                                         <a href="LaboratoriosP.php?id_paciente=<?php echo $row['IdPaciente']; ?>" class="btn btn btn-info btn-sm mt-1">Laboratorios</a>
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

    <footer class="mt-5 py-3 bg-light">
        <div class="container text-center">
            <p class="mb-0">Sistema de Gestión de Turnos - <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
