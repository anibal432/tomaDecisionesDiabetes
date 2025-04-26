<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Error desconocido'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos requeridos
        if (empty($_POST['IdPaciente']) || empty($_POST['IdMedico']) || empty($_POST['ExamenesSolicitados'])) {
            throw new Exception('Todos los campos requeridos deben estar completos');
        }

        $conn->begin_transaction();
        
        // Insertar solicitud de examen
        $sql = "INSERT INTO SolicitudExamenes (IdMedico, IdPaciente, FechaSolicitud, ExamenesSolicitados, Instrucciones, Estado) 
                VALUES (?, ?, NOW(), ?, ?, 'Pendiente')";
        $stmt = $conn->prepare($sql);
        $instrucciones = isset($_POST['Instrucciones']) ? $_POST['Instrucciones'] : null;
        $stmt->bind_param("iiss", $_POST['IdMedico'], $_POST['IdPaciente'], $_POST['ExamenesSolicitados'], $instrucciones);
        
        if ($stmt->execute()) {
            $conn->commit();
            $response = ['status' => 'success', 'message' => 'Solicitud de exámenes guardada correctamente'];
        } else {
            $conn->rollback();
            $response = ['status' => 'error', 'message' => 'Error al guardar la solicitud: ' . $conn->error];
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