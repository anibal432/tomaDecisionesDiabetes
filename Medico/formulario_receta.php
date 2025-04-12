<?php
require_once 'conexion.php';

$idSeleccionado = isset($_GET['id_paciente']) ? $_GET['id_paciente'] : null;

$pacientes = $conn->query("
    SELECT IdPaciente, CONCAT(IFNULL(NombreUno, ''), ' ', IFNULL(PrimerApellido, '')) AS Nombre 
    FROM Paciente
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Recetas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Nueva Receta Médica</h4>
        </div>
        <div class="card-body">
            <form id="formReceta" method="POST" action="guardar_receta.php">
                <!-- Paciente -->
                <div class="mb-3">
                    <label for="id_paciente" class="form-label">Paciente</label>
                    <select name="id_paciente" id="id_paciente" class="form-select" required>
                        <option value="">Seleccione un paciente</option>
                        <?php
                        if ($pacientes && $pacientes->num_rows > 0) {
                            while ($p = $pacientes->fetch_assoc()) {
                                $selected = ($idSeleccionado == $p['IdPaciente']) ? "selected" : "";
                                echo "<option value='{$p['IdPaciente']}' $selected>{$p['Nombre']}</option>";
                            }
                        } else {
                            echo "<option value=''>No hay pacientes registrados</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Medicamentos dinámicos -->
                <div class="mb-3">
                    <label class="form-label">Medicamentos</label>
                    <div id="medicamentosContainer">
                        <div class="row mb-2 medicamento-grupo">
                            <div class="col-md-4">
                                <input type="text" name="medicamentos[]" class="form-control" placeholder="Nombre del medicamento" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="observaciones_detalle[]" class="form-control" placeholder="Horario para medicacion">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger" onclick="this.closest('.medicamento-grupo').remove()">X</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="agregarMedicamento()">Agregar otro medicamento</button>
                </div>

                <!-- Observaciones -->
                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3" placeholder="Detalles adicionales"></textarea>
                </div>

                <!-- Botón -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Guardar receta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Bootstrap y Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function agregarMedicamento() {
    const container = document.getElementById("medicamentosContainer");
    const div = document.createElement("div");
    div.className = "row mb-2 medicamento-grupo";
    div.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="medicamentos[]" class="form-control" placeholder="Nombre del medicamento" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1" value="1" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="observaciones_detalle[]" class="form-control" placeholder="Observaciones para este medicamento">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger" onclick="this.closest('.medicamento-grupo').remove()">X</button>
        </div>
    `;
    container.appendChild(div);
}
</script>

</body>
</html>
