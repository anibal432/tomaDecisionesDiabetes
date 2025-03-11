<?php
include '../conexion.php';

$idPaciente = $_POST['id_paciente'];

$sql = "SELECT * FROM SignosVitales WHERE idPaciente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPaciente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(null);
}

$conn->close();
?>