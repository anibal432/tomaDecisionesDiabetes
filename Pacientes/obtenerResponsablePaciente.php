<?php
include '../conexionDiabetes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'error' => ''];
    $id_responsable = $_POST['id_responsable'];

    $sql = "SELECT IdResponsable, IdPaciente, PrimerNombre, SegundoNombre, TercerNombre, 
                   PrimerApellido, SegundoApellido, NoDpi, Telefono, Email 
            FROM ResponsablePaciente 
            WHERE IdResponsable = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_responsable);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['responsable'] = $result->fetch_assoc();
    } else {
        $response['error'] = 'No se encontró el responsable';
    }

    $stmt->close();
    $conn->close();
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>