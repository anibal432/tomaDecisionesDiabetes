<?php
session_start();
header('Content-Type: application/json');
include('conexionL.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMedico = $_POST['idMedico'];
    $primerNombre = $_POST['primerNombre'];
    $segundoNombre = $_POST['segundoNombre'] ?? null;
    $tercerNombre = $_POST['tercerNombre'] ?? null;
    $primerApellido = $_POST['primerApellido'];
    $segundoApellido = $_POST['segundoApellido'] ?? null;
    $noDpi = $_POST['noDpi'];
    $telefono = $_POST['telefono'] ?? null;
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $sexo = $_POST['sexo'];
    $grupoEtnico = $_POST['grupoEtnico'] ?? null;
    $estadoCita = "Pendiente";
    $fechaTurno = date('Y-m-d'); 
    
    $checkQuery = "SELECT IdTurno FROM Turnos WHERE NoDpi = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $noDpi);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El DPI ya está registrado']);
        exit();
    }
    
    $query = "INSERT INTO Turnos (
                IdMedico, PrimerNombrePac, SegundoNombrePac, TercerNombrePac, 
                PrimerApellidoPac, SegundoApellidoPac, NoDpi, Telefono, 
                FechaNacimiento, Sexo, GrupoEtnico, EstadoCita, FechaTurno
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "issssssssssss", 
        $idMedico, $primerNombre, $segundoNombre, $tercerNombre,
        $primerApellido, $segundoApellido, $noDpi, $telefono,
        $fechaNacimiento, $sexo, $grupoEtnico, $estadoCita, $fechaTurno
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Turno creado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el turno: ' . $conn->error]);
    }
    
    $stmt->close();
    $checkStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conn->close();
?>