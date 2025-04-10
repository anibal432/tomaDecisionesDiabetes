<?php
session_start();
include('conexionL.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primerNombre = $_POST['primerNombre'] ?? '';
    $segundoNombre = $_POST['segundoNombre'] ?? null;
    $tercerNombre = $_POST['tercerNombre'] ?? null;
    $primerApellido = $_POST['primerApellido'] ?? '';
    $segundoApellido = $_POST['segundoApellido'] ?? null;
    $correoMedico = $_POST['correoMedico'] ?? '';
    $contraMedico = $_POST['contraMedico'] ?? '';
    $noColegiado = $_POST['noColegiado'] ?? '';

    if (empty($primerNombre) || empty($primerApellido) || empty($correoMedico) || empty($contraMedico) || empty($noColegiado)) {
        echo json_encode([
            'success' => false,
            'message' => 'Todos los campos marcados con * son obligatorios'
        ]);
        exit();
    }

    try {
        $stmt = $conn->prepare("CALL InsertarMedico(?, ?, ?, ?, ?, ?, ?, NULL, ?)");
        $stmt->bind_param("ssssssss", 
            $primerNombre,
            $segundoNombre,
            $tercerNombre,
            $primerApellido,
            $segundoApellido,
            $correoMedico,
            $contraMedico,
            $noColegiado
        );
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Médico creado exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear el médico'
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