<?php
include '../conexionDiabetes.php'; 
include('../conexionL.php');

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Gestion Pacientes | Diabetes Log</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/nav.css">
<link rel="stylesheet" href="../css/pacientes.css">
    
    
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<nav class="navbar">
    <div class="navbar-icon"><i class="fa-solid fa-user-doctor"></i></div>
    <div class="logo">Diabetes Log</div>
    <ul>            
        <li><a href="../iniciomedico.php"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="../Consultas/AsignarTurno.php"><i class="fas fa-calendar-check"></i> <span>Asignar Turno</span></a></li>
        <li><a href="../Pacientes/datosPaciente.php" class="active"><i class="fas fa-user-injured"></i> <span>Gestion de Pacientes</span></a></li>
        <li><a href="../Consultas/TipoDiabetes.php"><i class="fas fa-vial"></i> <span>Tipos de Diabetes</span></a></li>
        <li><a href="../Medico/Pacientes_Turno.php"><i class="fa-solid fa-users-rectangle"></i> <span>Consultas</span></a></li>
        <?php if ($esJefeMedico): ?>
            <li><a href="../insertusuarios.php"><i class="fa-solid fa-user-plus"></i> <span>Ingresar Medico</span></a></li>
        <?php endif; ?>
        <li><a href="../Logout.php"><i class="fas fa-sign-out-alt"></i> <span>LogOut</span></a></li>
    </ul>
</nav>

<div class="main-content">

<!-- Modal Editar Paciente -->
<div class="modal fade" id="editarPacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-user-edit me-2"></i>Editar Datos del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPaciente" method="POST" action="guardarUpdatePaciente.php">
                    <input type="hidden" name="id_paciente" id="id_paciente_editar">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tipo_diabetes" class="form-label">Tipo de Diabetes</label>
                            <select class="form-control" id="tipo_diabetes" name="tipo_diabetes" required>
                                <?php
                                $sql = "SELECT IdDiabetes, DESCRIPCION FROM TipoDiabetes";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['IdDiabetes'] . "'>" . $row['DESCRIPCION'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="primer_nombre" class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tercer_nombre" class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" id="tercer_nombre" name="tercer_nombre">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="primer_apellido" class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="dpi" class="form-label">DPI</label>
                            <input type="text" class="form-control" id="dpi" name="dpi">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-control" id="sexo" name="sexo" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="grupo_etnico" class="form-label">Grupo Étnico</label>
                            <select class="form-control" id="grupo_etnico" name="grupo_etnico" required>
                                <option value="Ladino">Ladino</option>
                                <option value="Mestizo">Mestizo</option>
                                <option value="Maya">Maya</option>
                                <option value="Garifuna">Garifuna</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarPaciente" class="btn btn-custom w-100 mt-3">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Paciente -->
<div class="modal fade" id="pacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-user-plus me-2"></i>Agregar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPaciente" method="POST" action="guardarUpdatePaciente.php">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo Diabetes</label>
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
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" name="nombredos" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" name="nombretres">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" name="apellidodos">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">DPI</label>
                            <input type="text" class="form-control" name="dpi" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="fechaNacimiento" class="form-label">Fecha Nacimiento</label>
                            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-control" name="sexo" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="grupoEtnico" class="form-label">Grupo Étnico</label>
                            <select class="form-control" name="grupoEtnico" required>
                                <option value="">Seleccione...</option>
                                <option value="Ladino">Ladino</option>
                                <option value="Maya">Maya</option>
                                <option value="Garifuna">Garifuna</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarPaciente" class="btn btn-custom w-100 mt-3">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Antecedentes Personales -->
<div class="modal fade" id="antecedentesPersonalesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-file-medical me-2"></i>Antecedentes Personales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAntecedentesPersonales" method="POST" action="guardarAntecedentesPersonales.php">
                    <input type="hidden" name="id_ant_personal" id="id_ant_personal">
                    <input type="hidden" name="id_paciente" id="id_paciente_antecedentes">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="medicos" class="form-label">Médicos</label>
                            <input type="text" class="form-control" id="medicos" name="medicos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="quirurgicos" class="form-label">Quirúrgicos</label>
                            <input type="text" class="form-control" id="quirurgicos" name="quirurgicos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="traumaticos" class="form-label">Traumáticos</label>
                            <input type="text" class="form-control" id="traumaticos" name="traumaticos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ginecobstetricos" class="form-label">Ginecobstétricos</label>
                            <input type="text" class="form-control" id="ginecobstetricos" name="ginecobstetricos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="alergias" class="form-label">Alergias</label>
                            <input type="text" class="form-control" id="alergias" name="alergias">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vicios_manias" class="form-label">Vicios y Manías</label>
                            <input type="text" class="form-control" id="vicios_manias" name="vicios_manias">
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarAntecedentes" class="btn btn-custom w-100 mt-3">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Responsable -->
<div class="modal fade" id="responsableModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-user-shield me-2"></i>Datos del Responsable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formResponsable" method="POST" action="guardarResponsable.php">
                    <input type="hidden" name="id_paciente" id="id_paciente_responsable" value="">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="primer_nombre" class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tercer_nombre" class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" id="tercer_nombre" name="tercer_nombre">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="primer_apellido" class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="dpi" class="form-label">DPI</label>
                            <input type="text" class="form-control" id="dpi" name="dpi" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarResponsable" class="btn btn-custom w-100 mt-3">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Responsable -->
<div class="modal fade" id="editarResponsableModal" tabindex="-1" aria-labelledby="editarResponsableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="editarResponsableModalLabel"><i class="fas fa-user-edit me-2"></i>Editar Responsable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarResponsable">
                    <input type="hidden" name="id_responsable" id="id_responsable_editar" value="">
                    <input type="hidden" name="id_paciente" id="id_paciente_editar_responsable" value="">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="primer_nombre_editar" class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" id="primer_nombre_editar" name="primer_nombre" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="segundo_nombre_editar" class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" id="segundo_nombre_editar" name="segundo_nombre">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tercer_nombre_editar" class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" id="tercer_nombre_editar" name="tercer_nombre">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="primer_apellido_editar" class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" id="primer_apellido_editar" name="primer_apellido" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="segundo_apellido_editar" class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" id="segundo_apellido_editar" name="segundo_apellido">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="dpi_editar" class="form-label">DPI</label>
                            <input type="text" class="form-control" id="dpi_editar" name="dpi" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono_editar" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono_editar" name="telefono" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="email_editar" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_editar" name="email" required>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-custom w-100 mt-3" onclick="guardarResponsable()">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historia Clínica -->
<div class="modal fade" id="historiaClinicaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-notes-medical me-2"></i>Historia Clínica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formHistoriaClinica" method="POST" action="guardarHistoriaClinica.php">
                    <input type="hidden" name="id_historia_clinica" id="id_historia_clinica">
                    <input type="hidden" name="id_paciente" id="id_paciente_historia">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="motivo_consulta" class="form-label">Motivo de Consulta</label>
                            <input type="text" class="form-control" id="motivo_consulta" name="motivo_consulta" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="historia_enf_actual" class="form-label">Historia Enfermedad Actual</label>
                            <input type="text" class="form-control" id="historia_enf_actual" name="historia_enf_actual" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="datos_subjetivos" class="form-label">Datos Subjetivos</label>
                            <input type="text" class="form-control" id="datos_subjetivos" name="datos_subjetivos" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="examen_fisico" class="form-label">Examen Físico</label>
                            <input type="text" class="form-control" id="examen_fisico" name="examen_fisico" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="impresion_clinica" class="form-label">Impresión Clínica</label>
                            <input type="text" class="form-control" id="impresion_clinica" name="impresion_clinica" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tratamiento" class="form-label">Tratamiento</label>
                            <input type="text" class="form-control" id="tratamiento" name="tratamiento" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="estudios_laboratorio" class="form-label">Estudios de Laboratorio</label>
                            <input type="text" class="form-control" id="estudios_laboratorio" name="estudios_laboratorio" required>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarHistoriaClinica" class="btn btn-custom w-100 mt-3">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Datos Vitales -->
<div class="modal fade" id="datosVitalesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-heartbeat me-2"></i>Datos Vitales del Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formDatosVitales" method="POST" action="guardarDatosVitales.php">
                    <input type="hidden" name="id_paciente" id="id_paciente" value="">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="peso" class="form-label">Peso (Lbs)</label>
                            <input type="number" class="form-control" id="peso" name="peso" placeholder="Ej. 70.00" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="talla" class="form-label">Talla (m)</label>
                            <input type="number" class="form-control" id="talla" name="talla" placeholder="Ej. 1.75" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="presionArterial" class="form-label">Presión Arterial</label>
                            <input type="text" class="form-control" id="presionArterial" name="presion_arterial" placeholder="Ej. 120/80 mmHg" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="imc" class="form-label">Índice de Masa Corporal</label>
                            <input type="number" class="form-control" id="imc" name="imc" placeholder="Ej. 22.86" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="temperatura" class="form-label">Temperatura (°C)</label>
                            <input type="number" class="form-control" id="temperatura" name="temperatura" placeholder="Ej. 36.50" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="frecuenciaCardiaca" class="form-label">Frecuencia Cardíaca</label>
                            <input type="number" class="form-control" id="frecuenciaCardiaca" name="frecuencia_cardiaca" placeholder="Ej. 72" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="oxigenacion" class="form-label">Oxigenación (%)</label>
                            <input type="number" class="form-control" id="oxigenacion" name="oxigenacion" placeholder="Ej. 98.50" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="frecuenciaRespiratoria" class="form-label">Frecuencia Respiratoria</label>
                            <input type="number" class="form-control" id="frecuenciaRespiratoria" name="frecuencia_respiratoria" placeholder="Ej. 16" required>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarDatosVitales" class="btn btn-custom w-100 mt-3">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Datos Vitales -->
<div class="modal fade" id="editarDatosVitalesModal" tabindex="-1" aria-labelledby="editarDatosVitalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="editarDatosVitalesModalLabel"><i class="fas fa-heartbeat me-2"></i>Editar Datos Vitales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarDatosVitales">
                    <input type="hidden" name="id_paciente" id="id_paciente_editar" value="">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="peso_editar" class="form-label">Peso (Lbs)</label>
                            <input type="number" class="form-control" id="peso_editar" name="peso" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="talla_editar" class="form-label">Talla (m)</label>
                            <input type="number" class="form-control" id="talla_editar" name="talla" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="presionArterial_editar" class="form-label">Presión Arterial</label>
                            <input type="text" class="form-control" id="presionArterial_editar" name="presion_arterial" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="imc_editar" class="form-label">Índice de Masa Corporal</label>
                            <input type="number" class="form-control" id="imc_editar" name="imc" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="temperatura_editar" class="form-label">Temperatura (°C)</label>
                            <input type="number" class="form-control" id="temperatura_editar" name="temperatura" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="frecuenciaCardiaca_editar" class="form-label">Frecuencia Cardíaca</label>
                            <input type="number" class="form-control" id="frecuenciaCardiaca_editar" name="frecuencia_cardiaca" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="oxigenacion_editar" class="form-label">Oxigenación (%)</label>
                            <input type="number" class="form-control" id="oxigenacion_editar" name="oxigenacion" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="frecuenciaRespiratoria_editar" class="form-label">Frecuencia Respiratoria</label>
                            <input type="number" class="form-control" id="frecuenciaRespiratoria_editar" name="frecuencia_respiratoria" required>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-custom w-100 mt-3" onclick="guardarDatosVitales()">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Antecedentes Familiares -->
<div class="modal fade" id="antecedentesFamiliaresModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-users me-2"></i>Antecedentes Familiares</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAntecedentesFamiliares" method="POST" action="guardarAntecedentesFamiliares.php">
                    <input type="hidden" name="id_ant_fam" id="id_ant_fam">
                    <input type="hidden" name="id_paciente" id="id_paciente_antecedentes_familiares">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="medicos_fam" class="form-label">Médicos</label>
                            <input type="text" class="form-control" id="medicos_fam" name="medicos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="quirurgicos_fam" class="form-label">Quirúrgicos</label>
                            <input type="text" class="form-control" id="quirurgicos_fam" name="quirurgicos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="traumaticos_fam" class="form-label">Traumáticos</label>
                            <input type="text" class="form-control" id="traumaticos_fam" name="traumaticos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ginecobstetricos_fam" class="form-label">Ginecobstétricos</label>
                            <input type="text" class="form-control" id="ginecobstetricos_fam" name="ginecobstetricos">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="alergias_fam" class="form-label">Alergias</label>
                            <input type="text" class="form-control" id="alergias_fam" name="alergias">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vicios_manias_fam" class="form-label">Vicios y Manías</label>
                            <input type="text" class="form-control" id="vicios_manias_fam" name="vicios_manias">
                        </div>
                    </div>
                    
                    <button type="submit" id="btnGuardarAntecedentesFamiliares" class="btn btn-custom w-100 mt-3">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="agregarPacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="fas fa-user-plus me-2"></i>Agregar Nuevo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPaciente" method="POST" action="guardar_paciente.php">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Primer Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Segundo Nombre</label>
                            <input type="text" class="form-control" name="nombredos" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tercer Nombre</label>
                            <input type="text" class="form-control" name="nombretres">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" name="apellido" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" name="apellidodos">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DPI</label>
                            <input type="text" class="form-control" name="dpi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono">
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
                                <option value="Femenino">Prefiero no decir</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="grupoEtnico" class="form-label">Grupo Étnico</label>
                            <select class="form-control" name="grupoEtnico" required>
                                <option value="">Seleccione...</option>
                                <option value="Ladino">Ladino</option>
                                <option value="Mestizo">Mestizo</option>
                                <option value="Maya">Maya</option>
                                <option value="Garifuna">Garifuna</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-custom w-100 mt-3">
                        <i class="fas fa-save me-2"></i>Guardar Paciente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

    <div class="main-content">
    <div class="container-fluid px-0">
        <div class="patient-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user-injured"></i> Datos del Paciente
                </h2>
                
                <div class="search-add-container">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchPaciente" class="form-control form-control-sm" 
                               placeholder="Buscar Paciente" oninput="buscarPaciente()">
                    </div>
                    <button class="btn btn-add-patient" data-bs-toggle="modal" data-bs-target="#agregarPacienteModal">
    <i class="fas fa-plus me-2"></i>Agregar
</button>

                </div>
            </div>
            
            <div class="table-container-wrapper">
    <div class="table-scroll-container">
        <table class="table-custom">
            <thead>
                <tr>
                    <th width="60px">No</th>
                    <th>Nombre Paciente</th>
                    <th width="140px">Signos Vitales</th>
                    <th width="140px">Ant. Personales</th>
                    <th width="140px">Ant. Familiares</th>
                    <th width="140px">Responsable</th>
                    <th width="140px">Historia Clínica</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para obtener todos los pacientes
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
                                    <button class='btn btn-action btn-edit' onclick='abrirModalEditarPaciente(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "<td>
                                <div class='d-flex justify-content-center'>
                                    <button class='btn btn-action btn-add' onclick='abrirModalDatosVitales(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-plus'></i> 
                                    </button>
                                    <button class='btn btn-action btn-view' onclick='abrirModalEditarDatosVitales(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "<td>
                                <div class='d-flex justify-content-center'>
                                    <button class='btn btn-action btn-add' onclick='abrirModalAntecedentesPersonales(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-plus'></i> 
                                    </button>
                                    <button class='btn btn-action btn-view' onclick='abrirModalEditarAntecedentesPersonales(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "<td>
                                <div class='d-flex justify-content-center'>
                                    <button class='btn btn-action btn-add' onclick='abrirModalAntecedentesFamiliares(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-plus'></i> 
                                    </button>
                                    <button class='btn btn-action btn-view' onclick='abrirModalEditarAntecedentesFamiliares(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "<td>
                                <div class='d-flex justify-content-center'>
                                    <button class='btn btn-action btn-add' onclick='abrirModalResponsable(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-plus'></i> 
                                    </button>
                                    <button class='btn btn-action btn-view' onclick='abrirModalEditarResponsable(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "<td>
                                <div class='d-flex justify-content-center'>
                                    <button class='btn btn-action btn-add' onclick='abrirModalHistoriaClinica(" . $row['IdPaciente'] . ", null)'>
                                        <i class='fas fa-plus'></i> 
                                    </button>
                                    <button class='btn btn-action btn-view' onclick='abrirModalEditarHistoriaClinica(" . $row['IdPaciente'] . ")'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='no-data'>No hay pacientes registrados</td></tr>";
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#formPaciente').on('submit', function(event) {
        event.preventDefault(); 

       
        $.ajax({
            url: $(this).attr('action'), 
            type: $(this).attr('method'), 
            data: $(this).serialize(), 
            success: function(response) {
                $('#mensaje').html('<div class="alert alert-success">Paciente guardado exitosamente!</div>');
                $('#formPaciente')[0].reset(); 
            },
            error: function(xhr, status, error) {
                $('#mensaje').html('<div class="alert alert-danger">Error al guardar el paciente. Intente de nuevo.</div>');
            }
        });
    });
});

function buscarPaciente() {
    const searchValue = document.getElementById('searchPaciente').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase(); 
        if (name.includes(searchValue)) {
            row.style.display = ''; 
        } else {
            row.style.display = 'none'; 
        }
    });
    
}

function abrirModalDatosVitales(idPaciente) {
    document.getElementById('id_paciente').value = idPaciente; 
    const modal = new bootstrap.Modal(document.getElementById('datosVitalesModal'));
    modal.show();
}

function abrirModalEditarDatosVitales(idPaciente) {
    $.ajax({
        url: 'obtenerDatosVitales.php',
        method: 'POST',
        data: { id_paciente: idPaciente },
        success: function(data) {
            const datos = JSON.parse(data);
            if (datos) {
                $('#id_paciente').val(idPaciente);
                $('#peso').val(datos.Peso);
                $('#talla').val(datos.Talla);
                $('#presionArterial').val(datos.PresionArterial);
                $('#imc').val(datos.IndiceMasaCorporal);
                $('#temperatura').val(datos.Temperatura);
                $('#frecuenciaCardiaca').val(datos.FrecuenciaCardiaca);
                $('#oxigenacion').val(datos.Oxigenacion);
                $('#frecuenciaRespiratoria').val(datos.FrecuenciaRespiratoria);

                $('#btnGuardarDatosVitales').text('Guardar Cambios');

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

function abrirModalResponsable(idPaciente) {
    document.getElementById('id_paciente_responsable').value = idPaciente;
    const modal = new bootstrap.Modal(document.getElementById('responsableModal'));
    modal.show();
}

function abrirModalEditarResponsable(idPaciente) {
    $.ajax({
        url: 'obtenerResponsable.php',
        method: 'POST',
        data: { id_paciente: idPaciente },
        success: function(data) {
            const responsable = JSON.parse(data);
            if (responsable) {
                $('#id_responsable_editar').val(responsable.IdResponsable);
                $('#id_paciente_editar_responsable').val(idPaciente);
                $('#primer_nombre_editar').val(responsable.PrimerNombre);
                $('#segundo_nombre_editar').val(responsable.SegundoNombre);
                $('#tercer_nombre_editar').val(responsable.TercerNombre);
                $('#primer_apellido_editar').val(responsable.PrimerApellido);
                $('#segundo_apellido_editar').val(responsable.SegundoApellido);
                $('#dpi_editar').val(responsable.NoDpi);
                $('#telefono_editar').val(responsable.Telefono);
                $('#email_editar').val(responsable.Email);

                const modal = new bootstrap.Modal(document.getElementById('editarResponsableModal'));
                modal.show();
            } else {
                alert('No se encontró un responsable registrado para este paciente.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener datos:', error);
        }
    });
}

function guardarResponsable() {
    const formData = $('#formEditarResponsable').serialize();
    
    $.ajax({
        url: 'guardarResponsable.php',
        method: 'POST',
        data: formData,
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert('Datos del responsable actualizados correctamente');
                $('#editarResponsableModal').modal('hide');
            } else {
                alert('Error al actualizar: ' + result.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al guardar:', error);
            alert('Error al guardar los cambios');
        }
    });
}


function abrirModalDatosVitales(idPaciente) {
    $('#formDatosVitales')[0].reset();
    $('#id_paciente').val(idPaciente);
    $('#btnGuardarDatosVitales').text('Guardar');
    const modal = new bootstrap.Modal(document.getElementById('datosVitalesModal'));
    modal.show();
}

function abrirModalAntecedentesPersonales(idPaciente) {
    console.log('Abriendo modal para el paciente:', idPaciente); 
    $('#formAntecedentesPersonales')[0].reset();
    $('#id_paciente_antecedentes').val(idPaciente);
    $('#btnGuardarAntecedentes').text('Guardar');
    const modal = new bootstrap.Modal(document.getElementById('antecedentesPersonalesModal'));
    modal.show();
}

function abrirModalEditarAntecedentesPersonales(idPaciente) {
    console.log('Abriendo modal para editar antecedentes personales del paciente:', idPaciente); 
    $.ajax({
        url: 'obtenerAntecedentesPersonales.php', 
        method: 'POST',
        data: { id_paciente: idPaciente }, 
        success: function(data) {
            const antecedentes = JSON.parse(data); 
            if (antecedentes) {
                $('#id_ant_personal').val(antecedentes.IdAntPersonal);
                $('#id_paciente_antecedentes').val(antecedentes.IdPaciente);
                $('#medicos').val(antecedentes.Medicos);
                $('#quirurgicos').val(antecedentes.Quirurgicos);
                $('#traumaticos').val(antecedentes.Traumaticos);
                $('#ginecobstetricos').val(antecedentes.Ginecobstetricos);
                $('#alergias').val(antecedentes.Alergias);
                $('#vicios_manias').val(antecedentes.ViciosManias);

                $('#btnGuardarAntecedentes').text('Guardar Cambios');

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

$(document).ready(function() {
    $('#formAntecedentesPersonales').on('submit', function(event) {
        event.preventDefault(); 

        const formData = $(this).serialize(); 

        $.ajax({
            url: $(this).attr('action'), 
            type: $(this).attr('method'), 
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Antecedentes personales guardados exitosamente!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('antecedentesPersonalesModal'));
                    modal.hide();
                   
                    location.reload(); 
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
    console.log('Abriendo modal para el paciente:', idPaciente); 
    $('#formAntecedentesFamiliares')[0].reset();
    $('#id_paciente_antecedentes_familiares').val(idPaciente);
    $('#btnGuardarAntecedentesFamiliares').text('Guardar');
    const modal = new bootstrap.Modal(document.getElementById('antecedentesFamiliaresModal'));
    modal.show();
}

function abrirModalEditarAntecedentesFamiliares(idPaciente) {
    console.log('Abriendo modal para editar antecedentes familiares del paciente:', idPaciente); // Depuración
    $.ajax({
        url: 'obtenerAntecedentesFamiliares.php', 
        method: 'POST',
        data: { id_paciente: idPaciente }, 
        success: function(data) {
            const antecedentes = JSON.parse(data); 
            if (antecedentes) {
                $('#id_ant_fam').val(antecedentes.IdAntFam);
                $('#id_paciente_antecedentes_familiares').val(antecedentes.IdPaciente);
                $('#medicos_fam').val(antecedentes.Medicos);
                $('#quirurgicos_fam').val(antecedentes.Quirurgicos);
                $('#traumaticos_fam').val(antecedentes.Traumaticos);
                $('#ginecobstetricos_fam').val(antecedentes.Ginecobstetricos);
                $('#alergias_fam').val(antecedentes.Alergias);
                $('#vicios_manias_fam').val(antecedentes.ViciosManias);

                $('#btnGuardarAntecedentesFamiliares').text('Guardar Cambios');

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

$(document).ready(function() {
    $('#formAntecedentesFamiliares').on('submit', function(event) {
        event.preventDefault(); 

        const formData = $(this).serialize(); 

        $.ajax({
            url: $(this).attr('action'), 
            type: $(this).attr('method'), 
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Antecedentes familiares guardados exitosamente!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('antecedentesFamiliaresModal'));
                    modal.hide();
                    location.reload(); 
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


function guardarDatosVitales() {
    const formData = $('#formEditarDatosVitales').serialize(); 

    $.ajax({
        url: 'guardarDatosVitales.php', 
        method: 'POST',
        data: formData,
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                alert('Datos guardados exitosamente!');
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('editarDatosVitalesModal'));
                modal.hide();
                
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
        event.preventDefault(); 

        const formData = $(this).serialize(); 

        $.ajax({
            url: $(this).attr('action'), 
            type: $(this).attr('method'), 
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Datos guardados exitosamente!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('datosVitalesModal'));
                    modal.hide();
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




function abrirModalHistoriaClinica(idPaciente) {
    console.log('Abriendo modal para el paciente:', idPaciente); 
    $('#formHistoriaClinica')[0].reset();
    $('#id_paciente_historia').val(idPaciente);
    $('#btnGuardarHistoriaClinica').text('Guardar');
    const modal = new bootstrap.Modal(document.getElementById('historiaClinicaModal'));
    modal.show();
}

function abrirModalEditarHistoriaClinica(idHistoriaClinica) {
    console.log('Abriendo modal para editar historia clínica:', idHistoriaClinica); 
    $.ajax({
        url: 'obtenerHistoriaClinica.php', 
        method: 'POST',
        data: { id_historia_clinica: idHistoriaClinica }, 
        success: function(data) {
            const historia = JSON.parse(data); 
            if (historia) {
                $('#id_historia_clinica').val(historia.IdHistoriaClinica);
                $('#id_paciente_historia').val(historia.IdPaciente);
                $('#motivo_consulta').val(historia.MotivoConsulta);
                $('#historia_enf_actual').val(historia.HistoriaEnfActual);
                $('#datos_subjetivos').val(historia.DatosSubjetivos);
                $('#examen_fisico').val(historia.ExamenFisico);
                $('#impresion_clinica').val(historia.ImpresionClinica);
                $('#tratamiento').val(historia.Tratamiento);
                $('#estudios_laboratorio').val(historia.EstudiosLaboratorio);

                $('#btnGuardarHistoriaClinica').text('Guardar Cambios');

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
        event.preventDefault(); 

        const formData = $(this).serialize(); 

        $.ajax({
            url: $(this).attr('action'), 
            type: $(this).attr('method'), 
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Historia clínica guardada exitosamente!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('historiaClinicaModal'));
                    modal.hide();
                    
                    location.reload(); 
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

$(document).ready(function() {
    $('#formPaciente').on('submit', function(event) {
        event.preventDefault(); 
        $.ajax({
            url: 'guardar_paciente.php', 
            type: 'POST', 
            data: $(this).serialize(), 
            success: function(response) {
                const result = JSON.parse(response); 

                if (result.status === 'success') {
                    alert(result.message); 
                    window.location.href = 'pacientesPrueba.php'; 
                } else {
                    alert(result.message); 
                }
            },
            error: function(xhr, status, error) {
                alert('Error en la solicitud AJAX: ' + error); 
            }
        });
    });
});

function abrirModalEditarPaciente(idPaciente) {
    console.log('Abriendo modal para editar paciente:', idPaciente); 

    $('#formEditarPaciente')[0].reset();

    $.ajax({
        url: 'obtenerPaciente.php', 
        method: 'POST',
        data: { id_paciente: idPaciente },
        success: function(data) {
            console.log('Datos del paciente:', data); 
            const paciente = JSON.parse(data);
            if (paciente) {
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

                $('#tipo_diabetes').val(paciente.IdDiabetes);

                $('#btnGuardarPaciente').text('Guardar Cambios');

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
        event.preventDefault(); 

        const formData = $(this).serialize(); 

        $.ajax({
            url: 'guardarUpdatePaciente.php', 
            type: 'POST', 
            data: formData,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    alert('Datos del paciente actualizados exitosamente!');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editarPacienteModal'));
                    modal.hide();
                    location.reload(); 
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#formPaciente').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Guardando paciente',
            html: 'Por favor espere...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'guardar_paciente.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#agregarPacienteModal').modal('hide');                
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Paciente registrado correctamente',
                    confirmButtonColor: '#3a7bd5'
                }).then((result) => {
                    location.reload();
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al guardar el paciente',
                    confirmButtonColor: '#3a7bd5'
                });
            }
        });
    });
});

$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    
    if(success === 'true') {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Paciente registrado correctamente',
            confirmButtonColor: '#3a7bd5'
        }).then(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        });
    }
});
</script>

</body>
</html>