<?php
include '../conexionDiabetes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos del paciente
    $nombreUno = $_POST['nombre'];
    $nombreDos = $_POST['nombredos'];
    $nombreTres = $_POST['nombretres'];
    $primerApellido = $_POST['apellido'];
    $segundoApellido = $_POST['apellidodos'];
    $dpi = $_POST['dpi'];
    $telefono = $_POST['telefono'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $sexo = $_POST['sexo'];
    $grupoEtnico = $_POST['grupoEtnico'];

    // Insertar datos en la tabla Paciente (sin IdDiabetes)
    $sqlPaciente = "INSERT INTO Paciente (NombreUno, NombreDos, NombreTres, PrimerApellido, SegundoApellido, NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlPaciente);
    $stmt->bind_param("ssssssssss", $nombreUno, $nombreDos, $nombreTres, $primerApellido, $segundoApellido, $dpi, $telefono, $fechaNacimiento, $sexo, $grupoEtnico);
    
    if ($stmt->execute()) {
        echo "<script>alert('Paciente registrado'); window.location.href='pacientesPrueba.php';</script>";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar el paciente.']);
    }

    $stmt->close();
}

$conn->close();
?>