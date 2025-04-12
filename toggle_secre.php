<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id_secre = $_POST['id'] ?? null;
$desactivar = $_POST['desactivar'] ?? null;

if (!$id_secre || !isset($desactivar)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    if ($desactivar == '1') {
        $stmt = $conn->prepare("INSERT INTO Desactivado (IdSecre) VALUES (?)");
        $stmt->bind_param("i", $id_secre);
    } else {
        $stmt = $conn->prepare("DELETE FROM Desactivado WHERE IdSecre = ?");
        $stmt->bind_param("i", $id_secre);
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