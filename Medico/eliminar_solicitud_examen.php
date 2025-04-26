<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Error desconocido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idSolicitud'])) {
    try {
        $idSolicitud = (int)$_POST['idSolicitud'];
        
        // Verificar que la solicitud esté pendiente antes de eliminar
        $sqlCheck = "SELECT Estado FROM SolicitudExamenes WHERE IdSolicitud = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $idSolicitud);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('La solicitud no existe');
        }
        
        $solicitud = $result->fetch_assoc();
        if ($solicitud['Estado'] !== 'Pendiente') {
            throw new Exception('Solo se pueden eliminar solicitudes pendientes');
        }
        
        // Eliminar la solicitud
        $sqlDelete = "DELETE FROM SolicitudExamenes WHERE IdSolicitud = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $idSolicitud);
        
        if ($stmtDelete->execute()) {
            $response = ['status' => 'success', 'message' => 'Solicitud eliminada correctamente'];
        } else {
            throw new Exception('Error al eliminar la solicitud');
        }
    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Solicitud no válida'];
}

echo json_encode($response);
$conn->close();
?>