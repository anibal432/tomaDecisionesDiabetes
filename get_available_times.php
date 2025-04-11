<?php
include('conexionL.php');

$date = $_GET['date'];
$medicoId = $_GET['medicoId'];

$query = "SELECT hora FROM citas WHERE IdMedico = ? AND fecha = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $medicoId, $date);
$stmt->execute();
$result = $stmt->get_result();

$bookedTimes = [];
while ($row = $result->fetch_assoc()) {
    $formattedTime = date('H:i', strtotime($row['hora']));
    $bookedTimes[] = $formattedTime;
}

header('Content-Type: application/json');
echo json_encode(['bookedTimes' => $bookedTimes]);
?>