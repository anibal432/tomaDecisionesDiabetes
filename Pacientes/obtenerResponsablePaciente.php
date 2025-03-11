<?php
include '../conexion.php';

$idResponsable = $_POST['id_responsable'];

$sql = "SELECT * FROM ResponsablePaciente WHERE IdResponsable = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idResponsable);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(null);
}

$conn->close();
?>