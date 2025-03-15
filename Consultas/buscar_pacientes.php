<?php
require 'conexion.php';

if (isset($_GET['q'])) {
    $searchTerm = $_GET['q'];
    $query = "SELECT IdPaciente, CONCAT(NombreUno, ' ', PrimerApellido) AS NombreCompleto FROM Paciente WHERE NombreUno LIKE ? OR PrimerApellido LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['IdPaciente'],
            'text' => $row['NombreCompleto']
        ];
    }

    echo json_encode($data);
}
?>