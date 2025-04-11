<?php
include('conexionL.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de cita no proporcionado']);
    exit;
}

$query = "DELETE FROM citas WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $data['id']);
$success = $stmt->execute();

header('Content-Type: application/json');
echo json_encode(['success' => $success]);
?>