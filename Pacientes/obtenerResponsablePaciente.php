<?php
include '../conexionDiabetes.php';

header('Content-Type: application/json'); // Asegurar que la respuesta es JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPaciente = $_POST['id_paciente'];

    // Consulta mejorada con manejo de errores
    try {
        $sql = "SELECT * FROM ResponsablePaciente WHERE IdPaciente = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param("i", $idPaciente);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verificación de campos nulos
            $response = [
                'success' => true,
                'data' => [
                    'IdResponsable' => $row['IdResponsable'],
                    'PrimerNombre' => $row['PrimerNombre'] ?? '',
                    'SegundoNombre' => $row['SegundoNombre'] ?? '',
                    'TercerNombre' => $row['TercerNombre'] ?? '',
                    'PrimerApellido' => $row['PrimerApellido'] ?? '',
                    'SegundoApellido' => $row['SegundoApellido'] ?? '',
                    'NoDpi' => $row['NoDpi'] ?? '',
                    'Telefono' => $row['Telefono'] ?? '',
                    'Email' => $row['Email'] ?? '',
                    'IdPaciente' => $row['IdPaciente']
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'No se encontró responsable para este paciente'];
        }
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>