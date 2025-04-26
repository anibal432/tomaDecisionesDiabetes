<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Error desconocido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idReceta'])) {
    try {
        $idReceta = (int)$_POST['idReceta'];
        
        $conn->begin_transaction();
        
        $sqlDetalles = "DELETE FROM DetalleReceta WHERE IdReceta = ?";
        $stmtDetalles = $conn->prepare($sqlDetalles);
        $stmtDetalles->bind_param("i", $idReceta);
        
        if ($stmtDetalles->execute()) {
            $sqlReceta = "DELETE FROM Receta WHERE IdReceta = ?";
            $stmtReceta = $conn->prepare($sqlReceta);
            $stmtReceta->bind_param("i", $idReceta);
            
            if ($stmtReceta->execute()) {
                $conn->commit();
                $response = ['status' => 'success', 'message' => 'Receta eliminada correctamente'];
            } else {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => 'Error al eliminar la receta principal'];
            }
        } else {
            $conn->rollback();
            $response = ['status' => 'error', 'message' => 'Error al eliminar los detalles de la receta'];
        }
    } catch (Exception $e) {
        $conn->rollback();
        $response = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Solicitud no válida'];
}

echo json_encode($response);
$conn->close();
?>