<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_POST['idMedico'])) {
    echo json_encode(['success' => false, 'message' => 'ID de médico no proporcionado']);
    exit;
}

$idMedico = filter_var($_POST['idMedico'], FILTER_VALIDATE_INT);

if ($idMedico === false || $idMedico <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de médico inválido']);
    exit;
}

try {
    $conn->begin_transaction();
    
    $delete_query = "DELETE FROM JefeMed";
    if (!$conn->query($delete_query)) {
        throw new Exception("Error al eliminar jefe médico existente: " . $conn->error);
    }
    
    $insert_query = "INSERT INTO JefeMed (IdMedico) VALUES (?)";
    $stmt = $conn->prepare($insert_query);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $idMedico);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al asignar jefe médico: " . $stmt->error);
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Jefe médico asignado correctamente'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    
    error_log("Error en assign_chief.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>