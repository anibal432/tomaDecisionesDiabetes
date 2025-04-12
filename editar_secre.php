<?php
session_start();
include('conexionL.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$required_fields = ['idSecre', 'primerNombre', 'primerApellido'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos requeridos deben estar completos']);
        exit;
    }
}

try {
    $query = "UPDATE Secretarias SET 
              PrimerNombre = ?,
              SegundoNombre = ?,
              TercerNombre = ?,
              PrimerApellido = ?,
              SegundoApellido = ?
              WHERE IdSecre = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", 
        $_POST['primerNombre'],
        $_POST['segundoNombre'] ?? '',
        $_POST['tercerNombre'] ?? '',
        $_POST['primerApellido'],
        $_POST['segundoApellido'] ?? '',
        $_POST['idSecre']
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Secretaria actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la secretaria']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>