<?php
session_start();
include('conexionL.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMedico = $_POST['idMedico'] ?? null;
    $primerNombre = $_POST['primerNombre'] ?? '';
    $segundoNombre = $_POST['segundoNombre'] ?? null;
    $tercerNombre = $_POST['tercerNombre'] ?? null;
    $primerApellido = $_POST['primerApellido'] ?? '';
    $segundoApellido = $_POST['segundoApellido'] ?? null;

    if (empty($idMedico) || empty($primerNombre) || empty($primerApellido)) {
        echo json_encode([
            'success' => false,
            'message' => 'Los campos marcados con * son obligatorios'
        ]);
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE Medico SET 
            PrimerNombre = ?,
            SegundoNombre = ?,
            TercerNombre = ?,
            PrimerApellido = ?,
            SegundoApellido = ?
            WHERE IdMedico = ?");
        
        $stmt->bind_param("sssssi", 
            $primerNombre,
            $segundoNombre,
            $tercerNombre,
            $primerApellido,
            $segundoApellido,
            $idMedico
        );
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Médico actualizado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el médico'
            ]);
        }
    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>