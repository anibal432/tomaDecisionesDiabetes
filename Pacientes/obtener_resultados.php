<?php
require_once '../conexionDiabetes.php'; 

header('Content-Type: application/json');

$idPaciente = isset($_GET['id_paciente']) ? intval($_GET['id_paciente']) : null;

if (!$idPaciente) {
    echo json_encode(['error' => 'ID de paciente no proporcionado']);
    exit;
}

$sql = "SELECT 
           rp.IdResultado,
           rp.NombreArchivo,
           rp.RutaArchivo,
           rp.TipoArchivo,
           rp.FechaSubida
        FROM ResultadosPaciente rp
        INNER JOIN SolicitudExamenes se ON rp.IdSolicitud = se.IdSolicitud
        WHERE se.IdPaciente = ?
        ORDER BY rp.FechaSubida DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPaciente);
$stmt->execute();
$result = $stmt->get_result();

$resultados = [];
while ($row = $result->fetch_assoc()) {
    $resultados[] = $row;
}

echo json_encode($resultados);

$stmt->close();
$conn->close();
?>