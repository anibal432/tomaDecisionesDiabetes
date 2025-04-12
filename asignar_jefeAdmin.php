<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_POST['idSecre'])) {
    echo json_encode(['success' => false, 'message' => 'ID de Admin no proporcionado']);
    exit;
}

$idSecre = filter_var($_POST['idSecre'], FILTER_VALIDATE_INT);

if ($idSecre === false || $idSecre <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de Admin inválido']);
    exit;
}

try {
    $conn->begin_transaction();
    
    $delete_query = "DELETE FROM JefeSec";
    if (!$conn->query($delete_query)) {
        throw new Exception("Error al eliminar jefe Admin existente: " . $conn->error);
    }
    
    $insert_query = "INSERT INTO JefeSec (IdSecre) VALUES (?)";
    $stmt = $conn->prepare($insert_query);
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $idSecre);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al asignar jefe Admin: " . $stmt->error);
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Jefe Admin asignado correctamente'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    
    error_log("Error en asignar_jefeAdmin.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>