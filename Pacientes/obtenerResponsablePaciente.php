<?php
include '../conexionDiabetes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = $_POST['id_paciente'];

    $sql = "SELECT * FROM ResponsablePaciente WHERE IdPaciente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Depuración: Registrar los datos que se están enviando
        error_log("Datos del responsable: " . print_r($row, true));
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No se encontró responsable']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
?>