<?php
require_once 'conexion.php';


$idPaciente = isset($_GET['id_paciente']) ? $_GET['id_paciente'] : null;


$sql = "
    SELECT 
        rp.IdResultado,
        TRIM(CONCAT_WS(' ', p.NombreUno, p.NombreDos, p.NombreTres, p.PrimerApellido, p.SegundoApellido)) AS NombrePaciente,
        rp.NombreArchivo,
        rp.RutaArchivo,
        rp.TipoArchivo,
        rp.FechaSubida
    FROM ResultadosPaciente rp
    INNER JOIN SolicitudExamenes se ON rp.IdSolicitud = se.IdSolicitud
    INNER JOIN Paciente p ON se.IdPaciente = p.IdPaciente
";


if ($idPaciente) {
    $sql .= " WHERE se.IdPaciente = " . intval($idPaciente); 
}

$sql .= " ORDER BY rp.FechaSubida DESC";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Archivos de Resultados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .table-container { max-height: 600px; overflow-y: auto; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Resultados de Pacientes</h2>
    
   
    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar por nombre del paciente...">

    <div class="table-responsive table-container">
        <table class="table table-striped table-bordered" id="tablaResultados">
            <thead class="table-dark">
                <tr>
                    <th>Paciente</th>
                    <th>Archivo</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['NombrePaciente']) ?></td>
                        <td><?= htmlspecialchars($row['NombreArchivo']) ?></td>
                        <td><?= htmlspecialchars($row['TipoArchivo']) ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($row['FechaSubida'])) ?></td>
                        <td>
                        <a href="<?= htmlspecialchars($row['RutaArchivo']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                📄 Abrir
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Sin exámenes existentes.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
document.getElementById('buscador').addEventListener('keyup', function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaResultados tbody tr');

    filas.forEach(fila => {
        const texto = fila.children[0].textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
});
</script>

</body>
</html>

<?php $conn->close(); ?>
