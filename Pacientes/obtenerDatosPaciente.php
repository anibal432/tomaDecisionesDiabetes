<?php
include '../conexionDiabetes.php';

if (isset($_POST['id_paciente'])) {
    $idPaciente = $_POST['id_paciente'];

    $query = "SELECT * FROM Paciente WHERE IdPaciente = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idPaciente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $paciente = $result->fetch_assoc();
        echo json_encode($paciente);
    } else {
        echo json_encode(null);
    }

    $stmt->close();
    $conn->close();
}
?>