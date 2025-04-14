<?php
require_once 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
   
    if (empty($_POST['id_solicitud']) || empty($_POST['estado'])) {
        throw new Exception('ID de solicitud y estado son requeridos');
    }

    $idSolicitud = intval($_POST['id_solicitud']);
    $estado = $_POST['estado'];

    
    $estadosPermitidos = ['Pendiente', 'Completado', 'Cancelado'];
    if (!in_array($estado, $estadosPermitidos)) {
        throw new Exception('Estado no válido');
    }

    
    $stmt = $conn->prepare("UPDATE SolicitudExamenes SET Estado = ? WHERE IdSolicitud = ?");
    $stmt->bind_param("si", $estado, $idSolicitud);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
    } else {
        throw new Exception('No se pudo actualizar el estado');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
