<?php
include '../conexionDiabetes.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = $_POST['id_paciente'];

    $sql = "SELECT * FROM ResponsablePaciente WHERE IdPaciente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Estructura de respuesta corregida
        $response = [
            'success' => true,
            'data' => [
                'id_responsable' => $row['IdResponsable'],
                'id_paciente' => $row['IdPaciente'],
                'primer_nombre' => $row['PrimerNombre'],
                'segundo_nombre' => $row['SegundoNombre'] ?? '',
                'tercer_nombre' => $row['TercerNombre'] ?? '',
                'primer_apellido' => $row['PrimerApellido'],
                'segundo_apellido' => $row['SegundoApellido'] ?? '',
                'no_dpi' => $row['NoDpi'],
                'telefono' => $row['Telefono'],
                'email' => $row['Email']
            ]
        ];
        
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontró responsable']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>