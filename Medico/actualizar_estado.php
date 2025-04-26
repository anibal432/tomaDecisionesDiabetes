<?php
require_once '../conexionL.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['id_solicitud'])) {
        throw new Exception('ID de solicitud es requerido');
    }
    if (empty($_POST['estado'])) {
        throw new Exception('Estado es requerido');
    }

    $idSolicitud = intval($_POST['id_solicitud']);
    $estado = $_POST['estado'];

    $stmt = $conn->prepare("UPDATE SolicitudExamenes SET Estado = ? WHERE IdSolicitud = ?");
    $stmt->bind_param("si", $estado, $idSolicitud);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        throw new Exception('No se pudo actualizar el estado de la solicitud.');
    }

    echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>