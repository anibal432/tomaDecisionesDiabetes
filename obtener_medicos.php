<?php
header('Content-Type: application/json');
include('conexionL.php');

$query = "SELECT IdMedico, PrimerNombre, PrimerApellido, CorreoMedico FROM Medico";
$result = $conn->query($query);

$medicos = array();
while ($row = $result->fetch_assoc()) {
    $medicos[] = $row;
}

echo json_encode($medicos);
$conn->close();
?>