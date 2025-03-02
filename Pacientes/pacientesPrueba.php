<?php
include '../conexion.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pacientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
        <h2 class="text-center">Formulario de Paciente</h2>

        <!-- FORMULARIO PRINCIPAL PACIENTE -->
        <form id="formPaciente" method="POST" action="guardar_paciente.php" class="mb-4 p-4 border rounded">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Tipo Diabetes</label>
            <select class="form-control" name="producto[]" required>
                <?php 
                $sql = "SELECT IdDiabetes, DESCRIPCION FROM TipoDiabetes";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['IdDiabetes'] . "'>" . $row['DESCRIPCION'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Primer Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Segundo Nombre</label>
            <input type="text" class="form-control" name="nombredos" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Tercer Nombre</label>
            <input type="text" class="form-control" name="nombretres" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Primer Apellido</label>
            <input type="text" class="form-control" name="apellido" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Segundo Apellido</label>
            <input type="text" class="form-control" name="apellidodos" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">DPI</label>
            <input type="text" class="form-control" name="dpi" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="sexo" class="form-label">Sexo</label>
            <select class="form-control" name="sexo" required>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="grupoEtnico" class="form-label">Grupo Étnico</label>
            <select class="form-control" name="grupoEtnico" required>
                <option value="">Seleccione...</option>
                <option value="Ladino">Ladino</option>
                <option value="Mestizo">Mestizo</option>
                <option value="Mestizo">Maya</option>
                <option value="Mestizo">Garifuna</option>
                <option value="Mestizo">Otro</option>
            </select>
        </div>
<!--
        <div class="col-md-6 mb-3">
            <label for="tieneResponsable">¿Tiene responsable?</label>
            <input type="checkbox" id="tieneResponsable" onchange="mostrarModalResponsable()">
            <button id="btnAgregarResponsable" class="btn btn-success" style="display: none;" onclick="abrirModal('responsablePacienteModal')">
                Agregar Responsable
            </button>
        </div>-->
    </div>
    <button type="submit" class="btn btn-primary w-100">Guardar Paciente</button> 
</form>
<div id="mensaje" class="mt-3"></div>
            <!--Aqui iba lo que son las opciones para ingresar los datos del paciente antes...-->
    <!-- MODALES -->
    <!-- MODAL Tipo Diabetes -->
    <div class="modal fade" id="tipoDiabetesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tipo de Diabetes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="guardarTipoDiabetes.php">
                        <input type="hidden" name="id_paciente" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
                        <select class="form-select mb-2" name="tipo_diabetes" required>
                            <option value="">Seleccione...</option>
                            <option value="Pre-diabetes">Pre-diabetes</option>
                            <option value="Diabetes Tipo 1">Diabetes Tipo 1</option>
                            <option value="Diabetes Tipo 2">Diabetes Tipo 2</option>
                            <option value="Diabetes Gestacional">Diabetes Gestacional</option>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 
                    
    <!-- MODAL Antecedentes Personales -->
    <div class="modal fade" id="antecedentesPersonalesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Antecedentes Personales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="guardarAntecedentesPersonales.php">
                        <input type="hidden" name="id_paciente" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
                        <textarea class="form-control mb-2" name="enfermedades_previas" placeholder="Enfermedades Previas" required></textarea>
                        <textarea class="form-control mb-2" name="cirugias" placeholder="Cirugías" required></textarea>
                        <textarea class="form-control mb-2" name="traumatismos" placeholder="Traumatismos" required></textarea>
                        <textarea class="form-control mb-2" name="ginecobstetricos" placeholder="Ginecobstétricos"></textarea>
                        <textarea class="form-control mb-2" name="alergias" placeholder="Alergias"></textarea>
                        <textarea class="form-control mb-2" name="vicios_manias" placeholder="Vicios y Manías"></textarea>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL Responsable del Paciente -->
    <div class="modal fade" id="responsablePacienteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Responsable del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="guardarResponsablePaciente.php">
                        <input type="hidden" name="id_paciente" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
                        <input type="text" class="form-control mb-2" name="primer_nombre" placeholder="Primer Nombre" required>
                        <input type="text" class="form-control mb-2" name="segundo_nombre" placeholder="Segundo Nombre">
                        <input type="text" class="form-control mb-2" name="tercer_nombre" placeholder="Tercer Nombre">
                        <input type="text" class="form-control mb-2" name="primer_apellido" placeholder="Primer Apellido" required>
                        <input type="text" class="form-control mb-2" name="segundo_apellido" placeholder="Segundo Apellido">
                        <input type="text" class="form-control mb-2" name="dpi" placeholder="No DPI" required>
                        <input type="text" class="form-control mb-2" name="telefono" placeholder="Teléfono" required>
                        <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

            <!-- editar responsable-->


    <!-- MODAL para Historia Clínica -->
    <div class="modal fade" id="historiaClinicaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historia Clínica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="guardarHistoriaClinica.php">
                        <input type="hidden" name="id_paciente" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
                        <textarea class="form-control mb-2" name="motivo_consulta" placeholder="Motivo de Consulta" required></textarea>
                        <textarea class="form-control mb-2" name="historia_enfermedad" placeholder="Historia de la Enfermedad Actual" required></textarea>
                        <textarea class="form-control mb-2" name="datos_subjetivos" placeholder="Datos Subjetivos" required></textarea>
                        <textarea class="form-control mb-2" name="examen_fisico" placeholder="Examen Físico" required></textarea>
                        <textarea class="form-control mb-2" name="impresion_clinica" placeholder="Impresión Clínica" required></textarea>
                        <textarea class="form-control mb-2" name="tratamiento" placeholder="Tratamiento" required></textarea>
                        <textarea class="form-control mb-2" name="estudios_laboratorio" placeholder="Estudios de Laboratorio" required></textarea>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL para Signos Vitales -->
<!-- MODAL para Signos Vitales -->
<div class="modal fade" id="datosVitalesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos Vitales del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="guardarDatosVitales.php">
                    <input type="hidden" name="id_paciente" id="id_paciente" value="">
                    <!-- Campos para datos vitales -->
                    <div class="mb-2">
                        <label for="peso" class="form-label">Peso (kg)</label>
                        <input type="number" class="form-control" id="peso" name="peso" placeholder="Ej. 70.00" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="talla" class="form-label">Talla (m)</label>
                        <input type="number" class="form-control" id="talla" name="talla" placeholder="Ej. 1.75" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="presionArterial" class="form-label">Presión Arterial</label>
                        <input type="text" class="form-control" id="presionArterial" name="presion_arterial" placeholder="Ej. 120/80 mmHg" required>
                    </div>
                    <div class="mb-2">
                        <label for="imc" class="form-label">Índice de Masa Corporal (IMC)</label>
                        <input type="number" class="form-control" id="imc" name="imc" placeholder="Ej. 22.86" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="temperatura" class="form-label">Temperatura (°C)</label>
                        <input type="number" class="form-control" id="temperatura" name="temperatura" placeholder="Ej. 36.50" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="frecuenciaCardiaca" class="form-label">Frecuencia Cardíaca (bpm)</label>
                        <input type="number" class="form-control" id="frecuenciaCardiaca" name="frecuencia_cardiaca" placeholder="Ej. 72" required>
                    </div>
                    <div class="mb-2">
                        <label for="oxigenacion" class="form-label">Oxigenación (%)</label>
                        <input type="number" class="form-control" id="oxigenacion" name="oxigenacion" placeholder="Ej. 98.50" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="frecuenciaRespiratoria" class="form-label">Frecuencia Respiratoria (rpm)</label>
                        <input type="number" class="form-control" id="frecuenciaRespiratoria" name="frecuencia_respiratoria" placeholder="Ej. 16" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

            <!-- Modal para editar datos vitales-->
            <div class="modal fade" id="editarDatosVitalesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Datos Vitales del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarDatosVitales">
                    <input type="hidden" name="id_paciente" id="id_paciente_editar" value="">
                    <div class="mb-2">
                        <label for="peso_editar" class="form-label">Peso (kg)</label>
                        <input type="number" class="form-control" id="peso_editar" name="peso" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="talla_editar" class="form-label">Talla (m)</label>
                        <input type="number" class="form-control" id="talla_editar" name="talla" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="presionArterial_editar" class="form-label">Presión Arterial</label>
                        <input type="text" class="form-control" id="presionArterial_editar" name="presion_arterial" required>
                    </div>
                    <div class="mb-2">
                        <label for="imc_editar" class="form-label">Índice de Masa Corporal (IMC)</label>
                        <input type="number" class="form-control" id="imc_editar" name="imc" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="temperatura_editar" class="form-label">Temperatura (°C)</label>
                        <input type="number" class="form-control" id="temperatura_editar" name="temperatura" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="frecuenciaCardiaca_editar" class="form-label">Frecuencia Cardíaca (bpm)</label>
                        <input type="number" class="form-control" id="frecuenciaCardiaca_editar" name="frecuencia_cardiaca" required>
                    </div>
                    <div class="mb-2">
                        <label for="oxigenacion_editar" class="form-label">Oxigenación (%)</label>
                        <input type="number" class="form-control" id="oxigenacion_editar" name="oxigenacion" step="0.01" required>
                    </div>
                    <div class="mb-2">
                        <label for="frecuenciaRespiratoria_editar" class="form-label">Frecuencia Respiratoria (rpm)</label>
                        <input type="number" class="form-control" id="frecuenciaRespiratoria_editar" name="frecuencia_respiratoria" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="guardarDatosVitales()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

    <!-- MODAL Antecedentes Familiares -->
    <div class="modal fade" id="antecedentesFamiliaresModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Antecedentes Familiares</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="guardarAntecedentesFamiliares.php">
                        <input type="hidden" name="id_paciente" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
                        <textarea class="form-control mb-2" name="historial_enfermedades" placeholder="Historial de Enfermedades Familiares"></textarea>
                        <textarea class="form-control mb-2" name="cirugias_familiares" placeholder="Cirugías Familiares"></textarea>
                        <textarea class="form-control mb-2" name="traumatismos_familiares" placeholder="Traumatismos Familiares"></textarea>
                        <textarea class="form-control mb-2" name="ginecobstetricos_familiares" placeholder="Ginecobstétricos Familiares"></textarea>
                        <textarea class="form-control mb-2" name="alergias_familiares" placeholder="Alergias Familiares"></textarea>
                        <textarea class="form-control mb-2" name="vicios_manias_familiares" placeholder="Vicios y Manías Familiares"></textarea>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


            <!--Tabla de los datos del paciente-->

            <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 class=responsive">Datos del Paciente</h4>
    <div>
        <input type="text" id="searchPaciente" class="form-control" placeholder="Buscar Paciente" style="width: 300px; display: inline-block;" oninput="buscarPaciente()">
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nombre Paciente</th>
                <th>Signos Vitales</th>
                <th>Ant. Personales</th>
                <th>Ant. Familiares</th>
                <th>Responsable Paciente</th>
                <th>Historia Clínica</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT IdPaciente, CONCAT(NombreUno, ' ', NombreDos, ' ', NombreTres, ' ', PrimerApellido, ' ', SegundoApellido) AS NombreCompleto FROM Paciente";
            $result = $conn->query($sql);
            $no = 1;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['NombreCompleto']) . "</td>";
                    echo "<td>
                            <div class='d-flex justify-content-center'>
                                <button class='btn btn-info me-2' onclick='abrirModalDatosVitales(" . $row['IdPaciente'] . ")'>
                                    <i class='fas fa-plus'></i> 
                                </button>
                                <button class='btn btn-warning me-2' onclick='abrirModalEditarDatosVitales(" . $row['IdPaciente'] . ")'>
        <i class='fas fa-eye'></i>
    </button>
                            </div>
                          </td>";
                    echo "<td>
                            <div class='d-flex justify-content-center'>
                                <button class='btn btn-info me-2' onclick='abrirModal(\"antecedentesPersonalesModal\")'>
                                    <i class='fas fa-plus'></i> 
                                </button>
                                <button class='btn btn-warning' onclick='abrirModal(\"editarAntecedentesPersonalesModal\")'>
                                    <i class='fas fa-edit'></i> 
                                </button>
                            </div>
                          </td>";
                    echo "<td>
                            <div class='d-flex justify-content-center'>
                                <button class='btn btn-info me-2' onclick='abrirModal(\"antecedentesFamiliaresModal\")'>
                                    <i class='fas fa-plus'></i> 
                                </button>
                                <button class='btn btn-warning' onclick='abrirModal(\"editarAntecedentesFamiliaresModal\")'>
                                    <i class='fas fa-edit'></i> 
                                </button>
                            </div>
                          </td>";
                    echo "<td>
                            <div class='d-flex justify-content-center'>
                                <button class='btn btn-info me-2' onclick='abrirModal(\"responsablePacienteModal\")'>
                                    <i class='fas fa-plus'></i> 
                                </button>
                                <button class='btn btn-warning' onclick='abrirModal(\"editarResponsablePacienteModal\")'>
                                    <i class='fas fa-edit'></i> 
                                </button>
                            </div>
                          </td>";
                    echo "<td>
                            <div class='d-flex justify-content-center'>
                                <button class='btn btn-info me-2' onclick='abrirModal(\"historiaClinicaModal\")'>
                                    <i class='fas fa-plus'></i> 
                                </button>
                                <button class='btn btn-warning' onclick='abrirModal(\"editarHistoriaClinicaModal\")'>
                                    <i class='fas fa-edit'></i> 
                                </button>
                            </div>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No hay pacientes registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

    <script>
        function abrirModal(id) {
            let modal = new bootstrap.Modal(document.getElementById(id));
            modal.show();
        }
    </script>

<script>
        // Función para mostrar u ocultar el botón de agregar responsable
        function mostrarModalResponsable() {
            const checkResponsable = document.getElementById('tieneResponsable');
            const btnAgregarResponsable = document.getElementById('btnAgregarResponsable');

            if (checkResponsable.checked) {
                btnAgregarResponsable.style.display = 'block'; // Mostrar botón
            } else {
                btnAgregarResponsable.style.display = 'none'; // Ocultar botón
            }
        }

        // Función para abrir el modal
        function abrirModal(modalId) {
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--funcion con ajax -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#formPaciente').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        // Enviar la solicitud AJAX
        $.ajax({
            url: $(this).attr('action'), // URL del script PHP
            type: $(this).attr('method'), // Método de envío (POST)
            data: $(this).serialize(), // Serializar los datos del formulario
            success: function(response) {
                $('#mensaje').html('<div class="alert alert-success">Paciente guardado exitosamente!</div>');
                $('#formPaciente')[0].reset(); // Reiniciar el formulario
            },
            error: function(xhr, status, error) {
                $('#mensaje').html('<div class="alert alert-danger">Error al guardar el paciente. Intente de nuevo.</div>');
            }
        });
    });
});
 //Funcion para Buscar el Paciente
 function buscarPaciente() {
    const searchValue = document.getElementById('searchPaciente').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase(); // Asumiendo que el nombre está en la segunda celda
        if (name.includes(searchValue)) {
            row.style.display = ''; // Mostrar fila
        } else {
            row.style.display = 'none'; // Ocultar fila
        }
    });
    
}
function abrirModalDatosVitales(idPaciente) {
    document.getElementById('id_paciente').value = idPaciente; // Asigna el ID al campo oculto
    const modal = new bootstrap.Modal(document.getElementById('datosVitalesModal'));
    modal.show();
}
//editar datos vitales
function abrirModalEditarDatosVitales(idPaciente) {
    $.ajax({
        url: 'obtenerDatosVitales.php', // Asegúrate de que la ruta sea correcta
        method: 'POST',
        data: { id_paciente: idPaciente },
        success: function(data) {
            const datos = JSON.parse(data);
            if (datos) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_paciente_editar').val(idPaciente); // Asegúrate de que este campo tenga el ID correcto
                $('#peso_editar').val(datos.Peso);
                $('#talla_editar').val(datos.Talla);
                $('#presionArterial_editar').val(datos.PresionArterial);
                $('#imc_editar').val(datos.IndiceMasaCorporal);
                $('#temperatura_editar').val(datos.Temperatura);
                $('#frecuenciaCardiaca_editar').val(datos.FrecuenciaCardiaca);
                $('#oxigenacion_editar').val(datos.Oxigenacion);
                $('#frecuenciaRespiratoria_editar').val(datos.FrecuenciaRespiratoria);

                // Abre el modal
                const modal = new bootstrap.Modal(document.getElementById('editarDatosVitalesModal'));
                modal.show();
            } else {
                alert('No se encontraron datos para este paciente.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener datos:', error);
        }
    });
}
//guardar datos vitales
function guardarDatosVitales() {
    const formData = $('#formEditarDatosVitales').serialize(); // Serializa los datos del formulario

    $.ajax({
        url: 'guardarDatosVitales.php', // Asegúrate de que la ruta sea correcta
        method: 'POST',
        data: formData,
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert('Datos guardados exitosamente!');
                // Cierra el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editarDatosVitalesModal'));
                modal.hide();
                // Aquí podrías actualizar la tabla o los datos mostrados
            } else {
                alert('Error al guardar los datos: ' + result.error);
            }
        },
        error: function(xhr, status, error) {
            alert('Error al guardar los datos: ' + error);
        }
    });
}
</script>


</body>
</html>