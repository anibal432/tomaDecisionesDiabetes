<?php
include('conexionL.php');

$searchTerm = $_GET['search'] ?? '';

$query = "SELECT c.*, CONCAT(m.PrimerNombre, ' ', m.PrimerApellido) as nombre_medico 
          FROM citas c
          JOIN Medico m ON c.IdMedico = m.IdMedico
          WHERE c.primer_nombre LIKE ? OR c.primer_apellido LIKE ? OR c.correo_electronico LIKE ? 
          OR c.fecha LIKE ? OR c.hora LIKE ? OR m.PrimerNombre LIKE ? OR m.PrimerApellido LIKE ?
          ORDER BY c.fecha DESC, c.hora DESC";

$searchParam = "%$searchTerm%";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssssss", 
    $searchParam, $searchParam, $searchParam, 
    $searchParam, $searchParam, $searchParam, $searchParam
);
$stmt->execute();
$result = $stmt->get_result();

$citas = [];
while ($row = $result->fetch_assoc()) {
    $citas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($citas);
?>