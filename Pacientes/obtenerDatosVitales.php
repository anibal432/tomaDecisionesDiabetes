<?php
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = (int)$_POST['id_paciente'];

    $sql = "SELECT * FROM SignosVitales WHERE idPaciente = ? ORDER BY Fecha DESC LIMIT 1"; // Obtener solo el último registro
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row); // Devolver los datos como JSON
    } else {
        echo json_encode([]);
    }

    $stmt->close();
}
$conn->close();
?>