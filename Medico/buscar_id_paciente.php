<?php
require_once 'conexion.php';

$nombre = $_POST['nombre'] ?? '';

$sql = "SELECT IdPaciente 
        FROM Paciente 
        WHERE 
            CONCAT(IFNULL(NombreUno, ''), ' ', IFNULL(NombreDos, ''), ' ', IFNULL(PrimerApellido, ''), ' ', IFNULL(SegundoApellido, '')) 
            LIKE CONCAT('%', ?, '%') 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre);
$stmt->execute();
$res = $stmt->get_result();

$data = ['id_paciente' => null];
if ($row = $res->fetch_assoc()) {
    $data['id_paciente'] = $row['IdPaciente'];
}

header('Content-Type: application/json');
echo json_encode($data);
