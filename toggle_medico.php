<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id_medico = $_POST['id'] ?? null;
$desactivar = $_POST['desactivar'] ?? null;

if (!$id_medico || !isset($desactivar)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    if ($desactivar == '1') {
        $stmt = $conn->prepare("INSERT INTO Desactivado (IdMedico) VALUES (?)");
        $stmt->bind_param("i", $id_medico);
    } else {
        $stmt = $conn->prepare("DELETE FROM Desactivado WHERE IdMedico = ?");
        $stmt->bind_param("i", $id_medico);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>