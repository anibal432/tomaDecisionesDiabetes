<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$idMedico = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT IdMedico, PrimerNombre, SegundoNombre, TercerNombre, PrimerApellido, SegundoApellido FROM Medico WHERE IdMedico = ?");
    $stmt->bind_param("i", $idMedico);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $medico = $result->fetch_assoc();
        echo json_encode(['success' => true, 'medico' => $medico]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Médico no encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>