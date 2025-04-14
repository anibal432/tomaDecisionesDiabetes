<?php
require_once 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

try {
    // Validar datos de entrada con mensajes más descriptivos
    $errors = [];
    
    if (empty($_POST['IdPaciente'])) {
        $errors[] = 'El campo Paciente es requerido';
    }
    
    if (empty($_POST['IdMedico'])) {
        $errors[] = 'El campo Médico es requerido';
    }
    
    if (empty($_POST['ExamenesSolicitados'])) {
        $errors[] = 'Debe especificar los exámenes solicitados';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
        exit();
    }

    // Sanitizar y validar datos
    $idPaciente = filter_var($_POST['IdPaciente'], FILTER_VALIDATE_INT);
    $idMedico = filter_var($_POST['IdMedico'], FILTER_VALIDATE_INT);
    $examenes = trim($_POST['ExamenesSolicitados']);
    $instrucciones = !empty($_POST['Instrucciones']) ? trim($_POST['Instrucciones']) : null;

    if ($idPaciente === false || $idMedico === false) {
        throw new Exception('ID de paciente o médico inválido');
    }

    // Insertar en la base de datos con manejo de errores mejorado
    $stmt = $conn->prepare("INSERT INTO SolicitudExamenes 
                          (IdPaciente, IdMedico, ExamenesSolicitados, Instrucciones) 
                          VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    $stmt->bind_param("iiss", $idPaciente, $idMedico, $examenes, $instrucciones);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Solicitud guardada con éxito',
            'id' => $stmt->insert_id // Opcional: devolver el ID generado
        ]);
    } else {
        throw new Exception('No se pudo guardar la solicitud');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>