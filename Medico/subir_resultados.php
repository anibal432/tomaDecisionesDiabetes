<?php
require_once 'conexion.php';

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

        $uploadDir = 'uploads/resultados/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $uploadDir . $nombreUnico;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            throw new Exception('Error al guardar el archivo.');
        }

        $stmt = $conn->prepare("INSERT INTO ResultadosPaciente 
            (IdSolicitud, NombreArchivo, RutaArchivo, TipoArchivo) 
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $idSolicitud, $archivo['name'], $rutaCompleta, $extension);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Archivo subido correctamente']);
        } else {
            throw new Exception('No se pudo registrar el archivo.');
        }
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Resultados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Solicitudes Pendientes</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($solicitudes as $solicitud): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">#<?= $solicitud['IdSolicitud'] ?> - <?= $solicitud['NombreUno'] . ' ' . $solicitud['PrimerApellido'] ?></h5>
                        <p class="card-text">
                            <strong>Exámenes:</strong> <?= htmlspecialchars($solicitud['ExamenesSolicitados']) ?><br>
                            <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($solicitud['FechaSolicitud'])) ?>
                        </p>
                        <button class="btn btn-primary btnSubir" 
                                data-id="<?= $solicitud['IdSolicitud'] ?>" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalSubida">
                            Subir Resultado
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalSubida" tabindex="-1" aria-labelledby="modalSubidaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formSubida" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Subir Resultado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_solicitud" id="modal_id_solicitud">
                        <div class="mb-3">
                            <label for="archivo_resultado" class="form-label">Seleccionar archivo</label>
                            <input class="form-control" type="file" name="archivo_resultado" id="archivo_resultado" required accept=".pdf,.jpg,.png,.docx,.xlsx">
                        </div>
                        <div id="respuesta" class="text-center text-success"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Subir</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let idActual = 0;

        $('.btnSubir').on('click', function () {
            idActual = $(this).data('id');
            $('#modal_id_solicitud').val(idActual);
            $('#archivo_resultado').val('');
            $('#respuesta').text('');
        });

        $('#formSubida').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: 'subir_resultados.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $('#respuesta').removeClass('text-danger').addClass('text-success').text(resp.message);
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || { message: 'Error inesperado' };
                    $('#respuesta').removeClass('text-success').addClass('text-danger').text(response.message);
                }
            });
        });
    </script>
</body>
</html>

