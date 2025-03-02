<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar datos del formulario
    $idPaciente = $_POST['id_paciente'];
    $motivoConsulta = $_POST['motivo_consulta'];
    $historiaEnfermedad = $_POST['historia_enfermedad'];
    $datosSubjetivos = $_POST['datos_subjetivos'];
    $examenFisico = $_POST['examen_fisico'];
    $impresionClinica = $_POST['impresion_clinica'];
    $tratamiento = $_POST['tratamiento'];
    $estudiosLaboratorio = $_POST['estudios_laboratorio'];

    // Insertar datos en la tabla HistoriaClinica
    $stmt = $conn->prepare("INSERT INTO HistoriaClinica (IdPaciente, MotivoConsulta, HistoriaEnfActual, DatosSubjetivos, ExamenFisico, ImpresionClinica, Tratamiento, EstudiosLaboratorio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $idPaciente, $motivoConsulta, $historiaEnfermedad, $datosSubjetivos, $examenFisico, $impresionClinica, $tratamiento, $estudiosLaboratorio);

    if ($stmt->execute()) {
        header("Location: pacientes.php?id=" . $idPaciente);
        exit();
    } else {
        echo "Error al guardar la historia clínica: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>