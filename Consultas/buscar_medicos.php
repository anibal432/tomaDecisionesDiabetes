<?php
require 'conexion.php';

if (isset($_GET['q'])) {
    $searchTerm = $_GET['q'];
    $query = "SELECT IdMedico, CONCAT(PrimerNombre, ' ', PrimerApellido) AS NombreCompleto FROM Medico WHERE PrimerNombre LIKE ? OR PrimerApellido LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['IdMedico'],
            'text' => $row['NombreCompleto']
        ];
    }

    echo json_encode($data);
}
?>