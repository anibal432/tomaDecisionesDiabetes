<?php
include '../conexion.php';

$idPaciente = $_POST['id_paciente'];
$medicos = $_POST['medicos'];
$quirurgicos = $_POST['quirurgicos'];
$traumaticos = $_POST['traumaticos'];
$ginecobstetricos = $_POST['ginecobstetricos'];
$alergias = $_POST['alergias'];
$viciosManias = $_POST['vicios_manias'];

// Verificar si ya existe un registro para este paciente
$sqlCheck = "SELECT IdAntPersonal FROM AntecedentesPersonales WHERE IdPaciente = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $idPaciente);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    // Actualizar el registro existente
    $sqlUpdate = "UPDATE AntecedentesPersonales SET Medicos = ?, Quirurgicos = ?, Traumaticos = ?, Ginecobstetricos = ?, Alergias = ?, ViciosManias = ? WHERE IdPaciente = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssssssi", $medicos, $quirurgicos, $traumaticos, $ginecobstetricos, $alergias, $viciosManias, $idPaciente);
    if ($stmtUpdate->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmtUpdate->error]);
    }
} else {
    // Insertar un nuevo registro
    $sqlInsert = "INSERT INTO AntecedentesPersonales (IdPaciente, Medicos, Quirurgicos, Traumaticos, Ginecobstetricos, Alergias, ViciosManias) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("issssss", $idPaciente, $medicos, $quirurgicos, $traumaticos, $ginecobstetricos, $alergias, $viciosManias);
    if ($stmtInsert->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmtInsert->error]);
    }
}

$conn->close();
?>