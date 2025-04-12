<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['idTurno']) || !isset($data['estado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$idTurno = $data['idTurno'];
$estado = $data['estado'];

if ($estado === 'Atendido') {
    echo json_encode(['success' => false, 'message' => 'No se permite cambiar a estado Atendido']);
    exit();
}

$query = "UPDATE Turnos SET EstadoCita = ? WHERE IdTurno = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $estado, $idTurno);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>