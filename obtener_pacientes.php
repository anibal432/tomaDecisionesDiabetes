<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if (!isset($_GET['idMedico']) || !is_numeric($_GET['idMedico'])) {
    echo json_encode([]);
    exit();
}

$idMedico = $_GET['idMedico'];
$query = "SELECT IdTurno, PrimerNombrePac, PrimerApellidoPac, EstadoCita 
          FROM Turnos 
          WHERE IdMedico = ? AND EstadoCita != 'Atendido'
          ORDER BY 
            CASE EstadoCita 
                WHEN 'Atendiendo' THEN 1
                WHEN 'Adelante' THEN 2
                WHEN 'Pendiente' THEN 3
                ELSE 4
            END";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $idMedico);
$stmt->execute();
$result = $stmt->get_result();

$pacientes = [];
while ($row = $result->fetch_assoc()) {
    $pacientes[] = $row;
}

echo json_encode($pacientes);
$stmt->close();
$conn->close();
?>