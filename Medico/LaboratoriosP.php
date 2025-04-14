<?php
require_once 'conexion.php';

$idSeleccionado = isset($_GET['id_paciente']) ? $_GET['id_paciente'] : null;

$pacientes = $conn->query("
    SELECT IdPaciente, CONCAT(IFNULL(NombreUno, ''), ' ', IFNULL(PrimerApellido, '')) AS Nombre 
    FROM Paciente
");

if (isset($_GET['term']) && $_GET['type'] === 'medico') {
    $term = $conn->real_escape_string($_GET['term']);
    if (empty($term)) {
        echo json_encode([]);
        exit;
    }
    $query = "
        SELECT 
            IdMedico,
            CONCAT_WS(' ', 
                PrimerNombre, 
                IFNULL(SegundoNombre, ''), 
                IFNULL(TercerNombre, ''), 
                PrimerApellido, 
                IFNULL(SegundoApellido, '')
            ) AS NombreCompleto
        FROM Medico
        WHERE 
            PrimerNombre LIKE '%$term%' OR
            SegundoNombre LIKE '%$term%' OR
            TercerNombre LIKE '%$term%' OR
            PrimerApellido LIKE '%$term%' OR
            SegundoApellido LIKE '%$term%'
        LIMIT 10
    ";

    $result = $conn->query($query);

    $medicos = [];

    while ($row = $result->fetch_assoc()) {
        $medicos[] = [
            'IdMedico' => $row['IdMedico'],
            'NombreCompleto' => $row['NombreCompleto']
        ];
    }

    echo json_encode($medicos);
    exit;
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Exámenes Médicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
</head>
<body class="container mt-4">
    <h2>Solicitar Exámenes Médicos</h2>
    <form method="POST" class="mt-4" id="examenForm">
                <!-- Paciente -->
                <div class="mb-3">
                    <label for="id_paciente" class="form-label">Paciente</label>
                    <select name="IdPaciente" id="IdPaciente" class="form-control">
                    <option value="">Seleccione un paciente...</option>
                    <?php while($paciente = $pacientes->fetch_assoc()): ?>
                        <option value="<?= $paciente['IdPaciente'] ?>">
                            <?= $paciente['Nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                </div>
        <div class="mb-3">
            <label class="form-label">Médico</label>
            <select id="medicoSelect" name="IdMedico" class="form-select select2" required></select>
        </div>
        <div class="mb-3">
            <label class="form-label">Exámenes Solicitados</label>
            <textarea name="ExamenesSolicitados" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Instrucciones</label>
            <textarea name="Instrucciones" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        
        $('#medicoSelect').select2({
            placeholder: "Buscar médico...",
            minimumInputLength: 2,
            ajax: {
                url: 'LaboratoriosP.php',
                type: 'GET',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        term: params.term,
                        type: 'medico'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.IdMedico,
                            text: item.NombreCompleto
                        }))
                    };
                },
                cache: true
            }
        });


            $('#examenForm').on('submit', function(e) {
        e.preventDefault();

        
        if ($('#IdPaciente').val() === "") {
            alert("El campo Paciente es requerido.");
            return;
        }

        $.ajax({
            url: 'guardar_examen.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('Enviando...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#examenForm')[0].reset();
                    $('#IdPaciente, #IdMedico').val(null).trigger('change');
                    // Redirección a otra página
                    window.location.href = 'Pacientes_Turno.php';
                } else {
                    alert('Error: ' + (response.message || 'Error desconocido'));
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error en la conexión';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
                alert(errorMsg);
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Enviar Solicitud');
            }
        });
    });

    </script>
</body>
</html>
