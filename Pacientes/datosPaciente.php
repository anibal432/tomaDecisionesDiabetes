<?php
include '../conexionDiabetes.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Formulario de Pacientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="stilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/nav.css">
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!--Bootstrap JS y dependencias (Popper.js y jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <style>
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .table-container {
            margin-top: 20px;
        }
        
        .search-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
        <div class="logo">Diabetes Log</div>
        <ul>            
            <li><a href="../iniciomedico.php" class="active"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
            <li><a href="../Pacientes/pacientesPrueba.php"><i class="fas fa-user-plus"></i> <span>Ing. Paciente</span></a></li>
            <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
            <li><a href="../Pacientes/datosPaciente.php"><i class="fas fa-user-injured"></i> <span>Datos del Paciente</span></a></li>
            <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
            <li><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
        </ul>
    </nav>

<div class="main-content">
    <!-- Modal para Editar Paciente -->
    <div class="modal fade" id="editarPacienteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Datos del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPaciente" method="POST" action="guardarUpdatePaciente.php">
                        <!-- Campo oculto para el ID del paciente -->
                        <input type="hidden" name="id_paciente" id="id_paciente_editar">

                        <!-- Campo para Tipo de Diabetes -->
                        <div class="mb-3">
                            <label for="tipo_diabetes" class="form-label">Tipo de Diabetes</label>
                            <select class="form-control" id="tipo_diabetes" name="tipo_diabetes" required>
                                <?php
                                // Obtener los tipos de diabetes desde la base de datos
                                $sql = "SELECT IdDiabetes, DESCRIPCION FROM TipoDiabetes";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['IdDiabetes'] . "'>" . $row['DESCRIPCION'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Resto de los campos del formulario -->
                        <div class="mb-3">
                            <label for="primer_nombre" class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre">
                        </div>
                        <div class="mb-3">
                            <label for="tercer_nombre" class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" id="tercer_nombre" name="tercer_nombre">
                        </div>
                        <div class="mb-3">
                            <label for="primer_apellido" class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido">
                        </div>
                        <div class="mb-3">
                            <label for="dpi" class="form-label">DPI</label>
                            <input type="text" class="form-control" id="dpi" name="dpi">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-control" id="sexo" name="sexo" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="grupo_etnico" class="form-label">Grupo Étnico</label>
                            <select class="form-control" id="grupo_etnico" name="grupo_etnico" required>
                                <option value="Ladino">Ladino</option>
                                <option value="Mestizo">Mestizo</option>
                                <option value="Maya">Maya</option>
                                <option value="Garifuna">Garifuna</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <button type="submit" id="btnGuardarPaciente" class="btn btn-primary w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Paciente -->
    <div class="modal fade" id="pacienteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPaciente" method="POST" action="guardarUpdatePaciente.php">
                        <div class="mb-3">
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
                        <div class="mb-2">
                            <label class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" name="nombredos" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" name="nombretres">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" name="apellidodos">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">DPI</label>
                            <input type="text" class="form-control" name="dpi" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" required>
                        </div>
                        <div class="mb-2">
                            <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                        </div>
                        <div class="mb-2">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-control" name="sexo" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="grupoEtnico" class="form-label">Grupo Étnico</label>
                            <select class="form-control" name="grupoEtnico" required>
                                <option value="">Seleccione...</option>
                                <option value="Ladino">Ladino</option>
                                <option value="Maya">Maya</option>
                                <option value="Garifuna">Garifuna</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <button type="submit" id="btnGuardarPaciente" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Antecedentes Personales -->
    <div class="modal fade" id="antecedentesPersonalesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Antecedentes Personales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAntecedentesPersonales" method="POST" action="guardarAntecedentesPersonales.php">
                        <input type="hidden" name="id_ant_personal" id="id_ant_personal">
                        <input type="hidden" name="id_paciente" id="id_paciente_antecedentes">
                        <!-- Campos del formulario -->
                        <div class="mb-2">
                            <label for="medicos" class="form-label">Médicos</label>
                            <textarea class="form-control" id="medicos" name="medicos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="quirurgicos" class="form-label">Quirúrgicos</label>
                            <textarea class="form-control" id="quirurgicos" name="quirurgicos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="traumaticos" class="form-label">Traumáticos</label>
                            <textarea class="form-control" id="traumaticos" name="traumaticos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="ginecobstetricos" class="form-label">Ginecobstétricos</label>
                            <textarea class="form-control" id="ginecobstetricos" name="ginecobstetricos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="alergias" class="form-label">Alergias</label>
                            <textarea class="form-control" id="alergias" name="alergias"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="vicios_manias" class="form-label">Vicios y Manías</label>
                            <textarea class="form-control" id="vicios_manias" name="vicios_manias"></textarea>
                        </div>
                        <button type="submit" id="btnGuardarAntecedentes" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL Responsable del Paciente -->
<!-- Modal para Responsable del Paciente -->
<div class="modal fade" id="responsablePacienteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Responsable del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formResponsablePaciente" method="POST" action="guardarResponsablePaciente.php">
                    <input type="hidden" name="id_responsable" id="id_responsable">
                    <input type="hidden" name="id_paciente" id="id_paciente_responsable">
                    <!-- Campos del formulario -->
                    <div class="mb-2">
                        <label for="primer_nombre" class="form-label">Primer Nombre</label>
                        <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" required>
                    </div>
                    <div class="mb-2">
                        <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                        <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre">
                    </div>
                    <div class="mb-2">
                        <label for="tercer_nombre" class="form-label">Tercer Nombre</label>
                        <input type="text" class="form-control" id="tercer_nombre" name="tercer_nombre">
                    </div>
                    <div class="mb-2">
                        <label for="primer_apellido" class="form-label">Primer Apellido</label>
                        <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" required>
                    </div>
                    <div class="mb-2">
                        <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido">
                    </div>
                    <div class="mb-2">
                        <label for="no_dpi" class="form-label">No. DPI</label>
                        <input type="text" class="form-control" id="no_dpi" name="no_dpi" required>
                    </div>
                    <div class="mb-2">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" id="btnGuardarResponsablePaciente" class="btn btn-primary w-100">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- MODAL para Historia Clínica -->
    <div class="modal fade" id="historiaClinicaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historia Clínica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formHistoriaClinica" method="POST" action="guardarHistoriaClinica.php">
                        <input type="hidden" name="id_historia_clinica" id="id_historia_clinica">
                        <input type="hidden" name="id_paciente" id="id_paciente_historia">
                        <!-- Campos del formulario -->
                        <div class="mb-2">
                            <label for="motivo_consulta" class="form-label">Motivo de Consulta</label>
                            <textarea class="form-control" id="motivo_consulta" name="motivo_consulta" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="historia_enf_actual" class="form-label">Historia de la Enfermedad Actual</label>
                            <textarea class="form-control" id="historia_enf_actual" name="historia_enf_actual" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="datos_subjetivos" class="form-label">Datos Subjetivos</label>
                            <textarea class="form-control" id="datos_subjetivos" name="datos_subjetivos" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="examen_fisico" class="form-label">Examen Físico</label>
                            <textarea class="form-control" id="examen_fisico" name="examen_fisico" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="impresion_clinica" class="form-label">Impresión Clínica</label>
                            <textarea class="form-control" id="impresion_clinica" name="impresion_clinica" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="tratamiento" class="form-label">Tratamiento</label>
                            <textarea class="form-control" id="tratamiento" name="tratamiento" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="estudios_laboratorio" class="form-label">Estudios de Laboratorio</label>
                            <textarea class="form-control" id="estudios_laboratorio" name="estudios_laboratorio" required></textarea>
                        </div>
                        <button type="submit" id="btnGuardarHistoriaClinica" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL para Signos Vitales -->
    <div class="modal fade" id="datosVitalesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos Vitales del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formDatosVitales" method="POST" action="guardarDatosVitales.php">
                        <input type="hidden" name="id_paciente" id="id_paciente" value="">
                        <div class="mb-2">
                            <label for="peso" class="form-label">Peso (Lbs)</label>
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
                        <button type="submit" id="btnGuardarDatosVitales" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar datos vitales-->
    <div class="modal fade" id="editarDatosVitalesModal" tabindex="-1" aria-labelledby="editarDatosVitalesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDatosVitalesModalLabel">Editar Datos Vitales del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarDatosVitales">
                        <input type="hidden" name="id_paciente" id="id_paciente_editar" value="">
                        <div class="mb-2">
                            <label for="peso_editar" class="form-label">Peso (Lbs)</label>
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

    <!-- Modal para Antecedentes Familiares -->
    <div class="modal fade" id="antecedentesFamiliaresModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Antecedentes Familiares</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAntecedentesFamiliares" method="POST" action="guardarAntecedentesFamiliares.php">
                        <input type="hidden" name="id_ant_fam" id="id_ant_fam">
                        <input type="hidden" name="id_paciente" id="id_paciente_antecedentes_familiares">
                        <!-- Campos del formulario -->
                        <div class="mb-2">
                            <label for="medicos_fam" class="form-label">Médicos</label>
                            <textarea class="form-control" id="medicos_fam" name="medicos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="quirurgicos_fam" class="form-label">Quirúrgicos</label>
                            <textarea class="form-control" id="quirurgicos_fam" name="quirurgicos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="traumaticos_fam" class="form-label">Traumáticos</label>
                            <textarea class="form-control" id="traumaticos_fam" name="traumaticos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="ginecobstetricos_fam" class="form-label">Ginecobstétricos</label>
                            <textarea class="form-control" id="ginecobstetricos_fam" name="ginecobstetricos"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="alergias_fam" class="form-label">Alergias</label>
                            <textarea class="form-control" id="alergias_fam" name="alergias"></textarea>
                        </div>
                        <div class="mb-2">
                            <label for="vicios_manias_fam" class="form-label">Vicios y Manías</label>
                            <textarea class="form-control" id="vicios_manias_fam" name="vicios_manias"></textarea>
                        </div>
                        <button type="submit" id="btnGuardarAntecedentesFamiliares" class="btn btn-primary w-100">Guardar</button>
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
                        echo "<td>
                                <div class='d-flex justify-content-between align-items-center'>
                        <span>" . htmlspecialchars($row['NombreCompleto']) . "</span>
                        <button class='btn btn-warning me-2' onclick='abrirModalEditarPaciente(" . $row['IdPaciente'] . ")'>
                            <i class='fas fa-edit'></i>
                        </button>
                    </div>
                              </td>";
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
                                    <button class='btn btn-info me-2' onclick='abrirModalAntecedentesPersonales(" . $row['IdPaciente'] . ")'>
                                <i class='fas fa-plus'></i> 
                                    </button>
                                    <button class='btn btn-warning me-2' onclick='abrirModalEditarAntecedentesPersonales(" . $row['IdPaciente'] . ")'>
                                <i class='fas fa-eye'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "<td>
                                <div class='d-flex justify-content-center'>
                                <button class='btn btn-info me-2' onclick='abrirModalAntecedentesFamiliares(" . $row['IdPaciente'] . ")'>
                                <i class='fas fa-plus'></i> 
                                </button>
                                <button class='btn btn-warning me-2' onclick='abrirModalEditarAntecedentesFamiliares(" . $row['IdPaciente'] . ")'>
                            <i class='fas fa-eye'></i>
                            </button>
                            </div>
                              </td>";
                        echo "<td>
                               <div class='d-flex justify-content-center'>
    <button class='btn btn-info me-2' onclick='abrirModalResponsablePaciente(" . $row['IdPaciente'] . ")'>
        <i class='fas fa-plus'></i> 
    </button>
    <button class='btn btn-warning me-2' onclick='abrirModalEditarResponsablePaciente(" . $row['IdPaciente'] . ")'>
        <i class='fas fa-eye'></i>
    </button>
</div>
                              </td>";
                              echo "<td>
                              <div class='d-flex justify-content-center'>
                                  <button class='btn btn-info me-2' onclick='abrirModalHistoriaClinica(" . $row['IdPaciente'] . ", null)'>
                                      <i class='fas fa-plus'></i> 
                                  </button>
                                  <button class='btn btn-warning me-2' onclick='abrirModalEditarHistoriaClinica(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-eye'></i>
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
</div>

<script>
    function abrirModal(id) {
        let modal = new bootstrap.Modal(document.getElementById(id));
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

//modal para datos vitales
function abrirModalDatosVitales(idPaciente) {
    document.getElementById('id_paciente').value = idPaciente; // Asigna el ID al campo oculto
    const modal = new bootstrap.Modal(document.getElementById('datosVitalesModal'));
    modal.show();
}
//editar datos vitales
function abrirModalEditarDatosVitales(idPaciente) {
    $.ajax({
        url: 'obtenerDatosVitales.php',
        method: 'POST',
        data: { id_paciente: idPaciente },
        success: function(data) {
            const datos = JSON.parse(data);
            if (datos) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_paciente').val(idPaciente);
                $('#peso').val(datos.Peso);
                $('#talla').val(datos.Talla);
                $('#presionArterial').val(datos.PresionArterial);
                $('#imc').val(datos.IndiceMasaCorporal);
                $('#temperatura').val(datos.Temperatura);
                $('#frecuenciaCardiaca').val(datos.FrecuenciaCardiaca);
                $('#oxigenacion').val(datos.Oxigenacion);
                $('#frecuenciaRespiratoria').val(datos.FrecuenciaRespiratoria);

                // Cambiar el texto del botón de submit
                $('#btnGuardarDatosVitales').text('Guardar Cambios');

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('datosVitalesModal'));
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


function abrirModalDatosVitales(idPaciente) {
    // Limpiar el formulario
    $('#formDatosVitales')[0].reset();
    // Establecer el ID del paciente en el formulario
    $('#id_paciente').val(idPaciente);
    // Cambiar el texto del botón de submit
    $('#btnGuardarDatosVitales').text('Guardar');
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('datosVitalesModal'));
    modal.show();
}

// Función para abrir el modal en modo "agregar"
function abrirModalAntecedentesPersonales(idPaciente) {
    console.log('Abriendo modal para el paciente:', idPaciente); // Depuración
    // Limpiar el formulario
    $('#formAntecedentesPersonales')[0].reset();
    // Establecer el ID del paciente en el formulario
    $('#id_paciente_antecedentes').val(idPaciente);
    // Cambiar el texto del botón de submit
    $('#btnGuardarAntecedentes').text('Guardar');
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('antecedentesPersonalesModal'));
    modal.show();
}

// Función para abrir el modal en modo "editar"
function abrirModalEditarAntecedentesPersonales(idPaciente) {
    console.log('Abriendo modal para editar antecedentes personales del paciente:', idPaciente); // Depuración
    $.ajax({
        url: 'obtenerAntecedentesPersonales.php', // Archivo PHP que obtiene los datos
        method: 'POST',
        data: { id_paciente: idPaciente }, // Envía el ID del paciente
        success: function(data) {
            const antecedentes = JSON.parse(data); // Convierte la respuesta JSON en un objeto
            if (antecedentes) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_ant_personal').val(antecedentes.IdAntPersonal);
                $('#id_paciente_antecedentes').val(antecedentes.IdPaciente);
                $('#medicos').val(antecedentes.Medicos);
                $('#quirurgicos').val(antecedentes.Quirurgicos);
                $('#traumaticos').val(antecedentes.Traumaticos);
                $('#ginecobstetricos').val(antecedentes.Ginecobstetricos);
                $('#alergias').val(antecedentes.Alergias);
                $('#vicios_manias').val(antecedentes.ViciosManias);

                // Cambiar el texto del botón de submit
                $('#btnGuardarAntecedentes').text('Guardar Cambios');

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('antecedentesPersonalesModal'));
                modal.show();
            } else {
                alert('No se encontraron antecedentes personales para este paciente.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener antecedentes personales:', error);
        }
    });
}

// Manejar el envío del formulario
$(document).ready(function() {
    $('#formAntecedentesPersonales').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = $(this).serialize(); // Serializar los datos del formulario

        $.ajax({
            url: $(this).attr('action'), // URL del script PHP (guardarAntecedentesPersonales.php)
            type: $(this).attr('method'), // Método de envío (POST)
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Antecedentes personales guardados exitosamente!');
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('antecedentesPersonalesModal'));
                    modal.hide();
                    // Recargar la página o actualizar la tabla
                    location.reload(); // Opcional: Recargar la página
                } else {
                    alert('Error al guardar los datos: ' + result.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al guardar los datos: ' + error);
            }
        });
    });
});

function abrirModalAntecedentesFamiliares(idPaciente) {
    console.log('Abriendo modal para el paciente:', idPaciente); // Depuración
    // Limpiar el formulario
    $('#formAntecedentesFamiliares')[0].reset();
    // Establecer el ID del paciente en el formulario
    $('#id_paciente_antecedentes_familiares').val(idPaciente);
    // Cambiar el texto del botón de submit
    $('#btnGuardarAntecedentesFamiliares').text('Guardar');
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('antecedentesFamiliaresModal'));
    modal.show();
}

// Función para abrir el modal en modo "editar"
function abrirModalEditarAntecedentesFamiliares(idPaciente) {
    console.log('Abriendo modal para editar antecedentes familiares del paciente:', idPaciente); // Depuración
    $.ajax({
        url: 'obtenerAntecedentesFamiliares.php', // Archivo PHP que obtiene los datos
        method: 'POST',
        data: { id_paciente: idPaciente }, // Envía el ID del paciente
        success: function(data) {
            const antecedentes = JSON.parse(data); // Convierte la respuesta JSON en un objeto
            if (antecedentes) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_ant_fam').val(antecedentes.IdAntFam);
                $('#id_paciente_antecedentes_familiares').val(antecedentes.IdPaciente);
                $('#medicos_fam').val(antecedentes.Medicos);
                $('#quirurgicos_fam').val(antecedentes.Quirurgicos);
                $('#traumaticos_fam').val(antecedentes.Traumaticos);
                $('#ginecobstetricos_fam').val(antecedentes.Ginecobstetricos);
                $('#alergias_fam').val(antecedentes.Alergias);
                $('#vicios_manias_fam').val(antecedentes.ViciosManias);

                // Cambiar el texto del botón de submit
                $('#btnGuardarAntecedentesFamiliares').text('Guardar Cambios');

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('antecedentesFamiliaresModal'));
                modal.show();
            } else {
                alert('No se encontraron antecedentes familiares para este paciente.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener antecedentes familiares:', error);
        }
    });
}

// Manejar el envío del formulario
$(document).ready(function() {
    $('#formAntecedentesFamiliares').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = $(this).serialize(); // Serializar los datos del formulario

        $.ajax({
            url: $(this).attr('action'), // URL del script PHP (guardarAntecedentesFamiliares.php)
            type: $(this).attr('method'), // Método de envío (POST)
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Antecedentes familiares guardados exitosamente!');
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('antecedentesFamiliaresModal'));
                    modal.hide();
                    // Recargar la página o actualizar la tabla
                    location.reload(); // Opcional: Recargar la página
                } else {
                    alert('Error al guardar los datos: ' + result.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al guardar los datos: ' + error);
            }
        });
    });
});

//funcion para guardar datos vitales

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

$(document).ready(function() {
    $('#formDatosVitales').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = $(this).serialize(); // Serializar los datos del formulario

        $.ajax({
            url: $(this).attr('action'), // URL del script PHP
            type: $(this).attr('method'), // Método de envío (POST)
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Datos guardados exitosamente!');
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('datosVitalesModal'));
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
    });
});

// Función para abrir el modal en modo "editar"
function abrirModalResponsablePaciente(idPaciente) {
    console.log('Abriendo modal para el paciente:', idPaciente); // Depuración
    // Limpiar el formulario
    $('#formResponsablePaciente')[0].reset();
    // Establecer el ID del paciente en el formulario
    $('#id_paciente_responsable').val(idPaciente);
    // Cambiar el texto del botón de submit
    $('#btnGuardarResponsablePaciente').text('Guardar');
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('responsablePacienteModal'));
    modal.show();
}

function abrirModalEditarResponsablePaciente(idPaciente) {
    console.log('Abriendo modal para editar responsable del paciente con IdPaciente:', idPaciente); // Depuración
    $.ajax({
        url: 'obtenerResponsablePaciente.php', // Archivo PHP que obtiene los datos del responsable
        method: 'POST',
        data: { id_paciente: idPaciente }, // Envía el ID del paciente
        success: function(data) {
            const responsable = JSON.parse(data); // Convierte la respuesta JSON en un objeto
            if (responsable) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_responsable').val(responsable.IdResponsable);
                $('#id_paciente_responsable').val(responsable.IdPaciente);
                $('#primer_nombre').val(responsable.PrimerNombre);
                $('#segundo_nombre').val(responsable.SegundoNombre);
                $('#tercer_nombre').val(responsable.TercerNombre);
                $('#primer_apellido').val(responsable.PrimerApellido);
                $('#segundo_apellido').val(responsable.SegundoApellido);
                $('#no_dpi').val(responsable.NoDpi);
                $('#telefono').val(responsable.Telefono);
                $('#email').val(responsable.Email);

                // Cambiar el texto del botón de submit
                $('#btnGuardarResponsablePaciente').text('Guardar Cambios');

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('responsablePacienteModal'));
                modal.show();
            } else {
                alert('No se encontró un responsable asociado a este paciente.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener datos del responsable:', error);
        }
    });
}

$(document).ready(function() {
    $('#formResponsablePaciente').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = $(this).serialize(); // Serializar los datos del formulario

        $.ajax({
            url: $(this).attr('action'), // URL del script PHP (guardarResponsablePaciente.php)
            type: $(this).attr('method'), // Método de envío (POST)
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Responsable del paciente guardado exitosamente!');
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('responsablePacienteModal'));
                    modal.hide();
                    // Recargar la página o actualizar la tabla
                    location.reload(); // Opcional: Recargar la página
                } else {
                    alert('Error al guardar los datos: ' + result.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al guardar los datos: ' + error);
            }
        });
    });
});


//funcion para historial clinica
function abrirModalHistoriaClinica(idPaciente) {
    console.log('Abriendo modal para el paciente:', idPaciente); // Depuración
    // Limpiar el formulario
    $('#formHistoriaClinica')[0].reset();
    // Establecer el ID del paciente en el formulario
    $('#id_paciente_historia').val(idPaciente);
    // Cambiar el texto del botón de submit
    $('#btnGuardarHistoriaClinica').text('Guardar');
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('historiaClinicaModal'));
    modal.show();
}

function abrirModalEditarHistoriaClinica(idHistoriaClinica) {
    console.log('Abriendo modal para editar historia clínica:', idHistoriaClinica); // Depuración
    $.ajax({
        url: 'obtenerHistoriaClinica.php', // Archivo PHP que obtiene los datos de la historia clínica
        method: 'POST',
        data: { id_historia_clinica: idHistoriaClinica }, // Envía el ID de la historia clínica
        success: function(data) {
            const historia = JSON.parse(data); // Convierte la respuesta JSON en un objeto
            if (historia) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_historia_clinica').val(historia.IdHistoriaClinica);
                $('#id_paciente_historia').val(historia.IdPaciente);
                $('#motivo_consulta').val(historia.MotivoConsulta);
                $('#historia_enf_actual').val(historia.HistoriaEnfActual);
                $('#datos_subjetivos').val(historia.DatosSubjetivos);
                $('#examen_fisico').val(historia.ExamenFisico);
                $('#impresion_clinica').val(historia.ImpresionClinica);
                $('#tratamiento').val(historia.Tratamiento);
                $('#estudios_laboratorio').val(historia.EstudiosLaboratorio);

                // Cambiar el texto del botón de submit
                $('#btnGuardarHistoriaClinica').text('Guardar Cambios');

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('historiaClinicaModal'));
                modal.show();
            } else {
                alert('No se encontraron datos para esta historia clínica.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener datos de la historia clínica:', error);
        }
    });
}

$(document).ready(function() {
    $('#formHistoriaClinica').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = $(this).serialize(); // Serializar los datos del formulario

        $.ajax({
            url: $(this).attr('action'), // URL del script PHP (guardarHistoriaClinica.php)
            type: $(this).attr('method'), // Método de envío (POST)
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Historia clínica guardada exitosamente!');
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('historiaClinicaModal'));
                    modal.hide();
                    // Recargar la página o actualizar la tabla
                    location.reload(); // Opcional: Recargar la página
                } else {
                    alert('Error al guardar los datos: ' + result.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al guardar los datos: ' + error);
            }
        });
    });
});

//guardar paciente
$(document).ready(function() {
    $('#formPaciente').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        // Enviar la solicitud AJAX
        $.ajax({
            url: 'guardar_paciente.php', // URL del script PHP
            type: 'POST', // Método de envío
            data: $(this).serialize(), // Serializar los datos del formulario
            success: function(response) {
                const result = JSON.parse(response); // Parsear la respuesta JSON

                if (result.status === 'success') {
                    alert(result.message); // Mostrar mensaje de éxito
                    window.location.href = 'pacientesPrueba.php'; // Redirigir a la página principal
                } else {
                    alert(result.message); // Mostrar mensaje de error
                }
            },
            error: function(xhr, status, error) {
                alert('Error en la solicitud AJAX: ' + error); // Mostrar error de la solicitud
            }
        });
    });
});

//editar paciente
function abrirModalEditarPaciente(idPaciente) {
    console.log('Abriendo modal para editar paciente:', idPaciente); // Depuración

    // Limpiar el formulario
    $('#formEditarPaciente')[0].reset();

    // Obtener los datos del paciente
    $.ajax({
        url: 'obtenerPaciente.php', // Archivo que obtiene los datos del paciente
        method: 'POST',
        data: { id_paciente: idPaciente },
        success: function(data) {
            console.log('Datos del paciente:', data); // Depuración
            const paciente = JSON.parse(data);
            if (paciente) {
                // Rellenar los campos del modal con los datos obtenidos
                $('#id_paciente_editar').val(paciente.IdPaciente);
                $('#primer_nombre').val(paciente.NombreUno);
                $('#segundo_nombre').val(paciente.NombreDos);
                $('#tercer_nombre').val(paciente.NombreTres);
                $('#primer_apellido').val(paciente.PrimerApellido);
                $('#segundo_apellido').val(paciente.SegundoApellido);
                $('#dpi').val(paciente.NoDpi);
                $('#telefono').val(paciente.Telefono);
                $('#fecha_nacimiento').val(paciente.FechaNacimiento);
                $('#sexo').val(paciente.Sexo);
                $('#grupo_etnico').val(paciente.GrupoEtnico);

                // Seleccionar el tipo de diabetes del paciente
                $('#tipo_diabetes').val(paciente.IdDiabetes);

                // Cambiar el texto del botón de submit
                $('#btnGuardarPaciente').text('Guardar Cambios');

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('editarPacienteModal'));
                modal.show();
            } else {
                alert('No se encontraron datos para este paciente.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener datos del paciente:', error);
        }
    });
}

$(document).ready(function() {
    $('#formEditarPaciente').on('submit', function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        const formData = $(this).serialize(); // Serializar los datos del formulario

        $.ajax({
            url: 'guardarUpdatePaciente.php', // Asegúrate de que la ruta sea correcta
            type: 'POST', // Método de envío
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Datos del paciente actualizados exitosamente!');
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editarPacienteModal'));
                    modal.hide();
                    // Recargar la página o actualizar la tabla
                    location.reload(); // Opcional: Recargar la página
                } else {
                    alert('Error al guardar los datos: ' + result.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al guardar los datos: ' + error);
            }
        });
    });
});

</script>

</body>
</html>