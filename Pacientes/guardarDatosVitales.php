<?php
include '../conexion.php';

$idPaciente = $_POST['id_paciente'];
$peso = $_POST['peso'];
$talla = $_POST['talla'];
$presionArterial = $_POST['presion_arterial'];
$imc = $_POST['imc'];
$temperatura = $_POST['temperatura'];
$frecuenciaCardiaca = $_POST['frecuencia_cardiaca'];
$oxigenacion = $_POST['oxigenacion'];
$frecuenciaRespiratoria = $_POST['frecuencia_respiratoria'];

// Verificar si ya existe un registro para este paciente
$sqlCheck = "SELECT idSignosVitales FROM SignosVitales WHERE idPaciente = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $idPaciente);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    // Actualizar el registro existente
    $sqlUpdate = "UPDATE SignosVitales SET Peso = ?, Talla = ?, PresionArterial = ?, IndiceMasaCorporal = ?, Temperatura = ?, FrecuenciaCardiaca = ?, Oxigenacion = ?, FrecuenciaRespiratoria = ?, Fecha = NOW() WHERE idPaciente = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ddssddddi", $peso, $talla, $presionArterial, $imc, $temperatura, $frecuenciaCardiaca, $oxigenacion, $frecuenciaRespiratoria, $idPaciente);
    if ($stmtUpdate->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmtUpdate->error]);
    }
} else {
    // Insertar un nuevo registro
    $sqlInsert = "INSERT INTO SignosVitales (Peso, Talla, PresionArterial, IndiceMasaCorporal, Temperatura, FrecuenciaCardiaca, Oxigenacion, FrecuenciaRespiratoria, Fecha, idPaciente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ddssddddi", $peso, $talla, $presionArterial, $imc, $temperatura, $frecuenciaCardiaca, $oxigenacion, $frecuenciaRespiratoria, $idPaciente);
    if ($stmtInsert->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmtInsert->error]);
    }
}

$conn->close();
?>