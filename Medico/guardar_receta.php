<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Error desconocido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['id_paciente']) || empty($_POST['id_paciente'])) {
            throw new Exception('ID de paciente no proporcionado');
        }

        if (!isset($_POST['medicamentos']) || !is_array($_POST['medicamentos'])) {
            throw new Exception('No se proporcionaron medicamentos');
        }

        $conn->begin_transaction();
        
        $sqlReceta = "INSERT INTO Receta (IdPaciente, FechaReceta, Observaciones) 
                      VALUES (?, NOW(), ?)";
        $stmtReceta = $conn->prepare($sqlReceta);
        $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';
        $stmtReceta->bind_param("is", $_POST['id_paciente'], $observaciones);
        
        if ($stmtReceta->execute()) {
            $idReceta = $conn->insert_id;
            
            $sqlDetalle = "INSERT INTO DetalleReceta (IdReceta, Medicamento, Cantidad, Observacion) 
                           VALUES (?, ?, ?, ?)";
            $stmtDetalle = $conn->prepare($sqlDetalle);
            
            $success = true;
            $errorMessage = '';
            
            foreach ($_POST['medicamentos'] as $index => $medicamento) {
                if (empty($medicamento)) continue; // Saltar medicamentos vacíos
                
                $cantidad = isset($_POST['cantidades'][$index]) ? $_POST['cantidades'][$index] : 1;
                $observacion = isset($_POST['observaciones_detalle'][$index]) ? $_POST['observaciones_detalle'][$index] : '';
                
                $stmtDetalle->bind_param("isis", $idReceta, $medicamento, $cantidad, $observacion);
                
                if (!$stmtDetalle->execute()) {
                    $success = false;
                    $errorMessage = $conn->error;
                    break;
                }
            }
            
            if ($success) {
                $conn->commit();
                $response = ['status' => 'success', 'message' => 'Receta guardada correctamente'];
            } else {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => 'Error al guardar los medicamentos: ' . $errorMessage];
            }
        } else {
            $conn->rollback();
            $response = ['status' => 'error', 'message' => 'Error al guardar la receta principal: ' . $conn->error];
        }
    } catch (Exception $e) {
        $conn->rollback();
        $response = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Método no permitido'];
}

echo json_encode($response);
$conn->close();
?>