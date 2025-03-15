<?php
include '../conexionDiabetes.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar datos del formulario
    $idPaciente = $_POST['id_paciente'];
    $medicos = $_POST['historial_enfermedades'];
    $quirurgicos = $_POST['cirugias_familiares'];
    $traumaticos = $_POST['traumatismos_familiares'];
    $ginecobstetricos = $_POST['ginecobstetricos_familiares'];
    $alergias = $_POST['alergias_familiares'];
    $viciosManias = $_POST['vicios_manias_familiares'];

    // Insertar datos en la tabla AntecedentesFamiliares
    $stmt = $conn->prepare("INSERT INTO AntecedentesFamiliares (IdPaciente, Medicos, Quirurgicos, Traumaticos, Ginecobstetricos, Alergias, ViciosManias) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $idPaciente, $medicos, $quirurgicos, $traumaticos, $ginecobstetricos, $alergias, $viciosManias);

    if ($stmt->execute()) {
        header("Location: pacientes.php?id=" . $idPaciente);
        exit();
    } else {
        echo "Error al guardar los antecedentes familiares: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>