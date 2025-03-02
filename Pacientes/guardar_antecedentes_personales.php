<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar datos del formulario
    $idPaciente = $_POST['id_paciente'];
    $medicos = $_POST['enfermedades_previas'];
    $quirurgicos = $_POST['cirugias'];
    $traumaticos = $_POST['traumatismos'];
    $ginecobstetricos = $_POST['ginecobstetricos'];
    $alergias = $_POST['alergias'];
    $viciosManias = $_POST['vicios_manias'];

    // Insertar datos en la tabla AntecedentesPersonales
    $stmt = $conn->prepare("INSERT INTO AntecedentesPersonales (IdPaciente, Medicos, Quirurgicos, Traumaticos, Ginecobstetricos, Alergias, ViciosManias) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $idPaciente, $medicos, $quirurgicos, $traumaticos, $ginecobstetricos, $alergias, $viciosManias);

    if ($stmt->execute()) {
        header("Location: pacientes.php?id=" . $idPaciente);
        exit();
    } else {
        echo "Error al guardar los antecedentes personales: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>