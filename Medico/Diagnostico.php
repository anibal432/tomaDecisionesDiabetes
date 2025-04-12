<?php
require_once 'conexion.php';


if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $sql = "SELECT IdCIE10, Codigo, Descripcion FROM CIE10 WHERE Codigo LIKE ? OR Descripcion LIKE ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $likeTerm = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $likeTerm, $likeTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $diagnostics = [];
    while ($row = $result->fetch_assoc()) {
        $diagnostics[] = $row;
    }
    echo json_encode($diagnostics);
    exit;
}

// Insertar diagnósticos en la tabla Diagnostico
if (isset($_POST['insertDiagnosticos'])) {
    $diagnosticos = $_POST['diagnosticos'];  // Recibe el array de diagnósticos seleccionados
    $idPaciente = $_POST['idPaciente'];  // ID del paciente (esto debe venir del formulario o de la sesión)

    // Insertar los diagnósticos seleccionados
    foreach ($diagnosticos as $idCIE10) {
        $sql = "INSERT INTO Diagnostico (IdPaciente, IdCIE10, FechaDiagnostico) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $idPaciente, $idCIE10);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success', 'message' => 'Diagnósticos insertados correctamente']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Diagnósticos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .search-results {
            max-height: 200px;
            overflow-y: auto;
        }
        .search-results li {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Buscar Diagnósticos</h2>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form id="diagnosticForm">
                    <div class="mb-3">
                        <label for="search" class="form-label">Buscar CIE10</label>
                        <input type="text" id="search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
                    </div>
                    <ul id="searchResults" class="list-group search-results"></ul>

                    <div class="mt-3">
                        <label for="selectedDiagnosticos" class="form-label">Diagnósticos Seleccionados</label>
                        <ul id="selectedDiagnosticos" class="list-group">
                            <!-- Diagnósticos seleccionados se agregarán aquí -->
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Guardar Diagnósticos</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Buscar diagnósticos mientras el usuario escribe
            $('#search').on('input', function() {
                var searchTerm = $(this).val();
                if (searchTerm.length > 2) {
                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: { search: searchTerm },
                        success: function(response) {
                            var data = JSON.parse(response);
                            $('#searchResults').empty();
                            data.forEach(function(diagnostic) {
                                $('#searchResults').append('<li class="list-group-item" data-id="'+diagnostic.IdCIE10+'">'+diagnostic.Codigo+' - '+diagnostic.Descripcion+'</li>');
                            });
                        }
                    });
                } else {
                    $('#searchResults').empty();
                }
            });

            // Seleccionar diagnóstico
            $('#searchResults').on('click', 'li', function() {
                var idCIE10 = $(this).data('id');
                var text = $(this).text();
                var selectedList = $('#selectedDiagnosticos');

                // Agregar al listado de seleccionados si no está ya
                if (selectedList.children().length < 5 && !$('#selectedDiagnosticos li[data-id="'+idCIE10+'"]').length) {
                    selectedList.append('<li class="list-group-item" data-id="'+idCIE10+'">'+text+' <button class="btn btn-danger btn-sm float-end remove-diagnostic">Eliminar</button></li>');
                }

                // Limpiar resultados de búsqueda
                $('#searchResults').empty();
                $('#search').val('');
            });

            // Eliminar diagnóstico de la lista seleccionada
            $('#selectedDiagnosticos').on('click', '.remove-diagnostic', function() {
                $(this).closest('li').remove();
            });

            // Enviar formulario para insertar los diagnósticos seleccionados
            $('#diagnosticForm').submit(function(e) {
                e.preventDefault();

                var diagnosticos = [];
                $('#selectedDiagnosticos li').each(function() {
                    diagnosticos.push($(this).data('id'));
                });

                if (diagnosticos.length > 0) {
                    var idPaciente = 1; // Asegúrate de reemplazar esto por el ID del paciente actual
                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: { insertDiagnosticos: true, diagnosticos: diagnosticos, idPaciente: idPaciente },
                        success: function(response) {
                            var data = JSON.parse(response);
                            alert(data.message);
                            // Limpiar lista de seleccionados
                            $('#selectedDiagnosticos').empty();
                        }
                    });
                } else {
                    alert('Por favor, selecciona al menos un diagnóstico.');
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
