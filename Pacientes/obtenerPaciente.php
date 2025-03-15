<?php
include '../conexionDiabetes.php';

if (isset($_POST['id_paciente'])) {
    $idPaciente = $_POST['id_paciente'];
    $sql = "SELECT * FROM Paciente WHERE IdPaciente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPaciente);
    $stmt->execute();
    $result = $stmt->get_result();
    $paciente = $result->fetch_assoc();
    echo json_encode($paciente);
}
?> 