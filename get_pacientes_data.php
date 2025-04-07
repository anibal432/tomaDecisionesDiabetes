<?php
require 'conexionL.php';

$query = "SELECT td.DESCRIPCION, COUNT(p.IdPaciente) as total 
          FROM Paciente p
          JOIN TipoDiabetes td ON p.IdDiabetes = td.IdDiabetes
          WHERE td.DESCRIPCION != 'No tiene...'
          GROUP BY td.DESCRIPCION";

$result = $conn->query($query);

$data = [
    'labels' => [],
    'values' => [],
    'total' => 0
];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data['labels'][] = $row['DESCRIPCION'];
        $data['values'][] = (int)$row['total'];
        $data['total'] += $row['total'];
    }
}

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>