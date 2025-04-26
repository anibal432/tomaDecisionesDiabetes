<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Error desconocido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idDiagnostico'])) {
    try {
        $idDiagnostico = (int)$_POST['idDiagnostico'];
        
        $sql = "DELETE FROM Diagnostico WHERE IdDiagnostico = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idDiagnostico);
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Diagnóstico eliminado correctamente'];
        } else {
            $response = ['status' => 'error', 'message' => 'Error al eliminar el diagnóstico'];
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Solicitud no válida'];
}

echo json_encode($response);
$conn->close();
?>