<?php
include '../conexionDiabetes.php'; // Asegúrate de que la ruta de conexión sea correcta

$sql = "SELECT IdDiabetes, DESCRIPCION FROM TipoDiabetes";
$result = $conn->query($sql);

$tiposDiabetes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tiposDiabetes[] = $row;
    }
}

echo json_encode($tiposDiabetes);
?>