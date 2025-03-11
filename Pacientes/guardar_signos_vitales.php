<?php
include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar datos del formulario
    $idPaciente = $_POST['id_paciente'];
    $peso = $_POST['peso'];
    $talla = $_POST['talla'];
    $presionArterial = $_POST['presion_arterial'];
    $temperatura = $_POST['temperatura'];
    $frecuenciaCardiaca = $_POST['frecuencia_cardiaca'];
    $oxigenacion = $_POST['oxigenacion'];
    $frecuenciaRespiratoria = $_POST['frecuencia_respiratoria'];

    // Insertar datos en la tabla SignosVitales
    $stmt = $conn->prepare("INSERT INTO SignosVitales (Peso, Talla, PresionArterial, Temperatura, FrecuenciaCardiaca, Oxigenacion, FrecuenciaRespiratoria, idPaciente) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ddssiddi", $peso, $talla, $presionArterial, $temperatura, $frecuenciaCardiaca, $oxigenacion, $frecuenciaRespiratoria, $idPaciente);

    if ($stmt->execute()) {
        header("Location: pacientes.php?id=" . $idPaciente);
        exit();
    } else {
        echo "Error al guardar los signos vitales: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>