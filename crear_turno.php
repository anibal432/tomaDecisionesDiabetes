<?php
session_start();
include('conexionL.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMedico = $_POST['idMedico'];
    $primerNombre = $_POST['primerNombre'];
    $segundoNombre = $_POST['segundoNombre'] ?? null;
    $primerApellido = $_POST['primerApellido'];
    $segundoApellido = $_POST['segundoApellido'] ?? null;
    $estadoCita = "Pendiente";
    
    $query = "INSERT INTO Turnos (IdMedico, PrimerNombrePac, SegundoNombrePac, PrimerApellidoPac, SegundoApellidoPac, EstadoCita) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $idMedico, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $estadoCita);
    
    if ($stmt->execute()) {
        header("Location: turnospacientes.php?success=Turno creado correctamente");
    } else {
        header("Location: turnospacientes.php?error=Error al crear el turno");
    }
    
    $stmt->close();
} else {
    header("Location: turnospacientes.php");
}

$conn->close();
exit();
?>