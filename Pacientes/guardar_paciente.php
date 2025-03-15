<?php
include '../conexionDiabetes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos del paciente
    $tipoDiabetes = $_POST['producto'][0]; // Suponiendo que el primer elemento es el tipo de diabetes
    $nombreUno = $_POST['nombre'];
    $nombreDos = $_POST['nombredos'];
    $nombreTres = $_POST['nombretres'];
    $primerApellido = $_POST['apellido'];
    $segundoApellido = $_POST['apellidodos'];
    $dpi = $_POST['dpi'];
    $telefono = $_POST['telefono'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $sexo = $_POST['sexo']; // Asegúrate de que el campo tenga el nombre correcto
    $grupoEtnico = $_POST['grupoEtnico']; // Asegúrate de que el campo tenga el nombre correcto
    $idResponsable = NULL; // Aquí puedes gestionar la inserción del responsable si es necesario

    // Insertar datos en la tabla Paciente
    $sqlPaciente = "INSERT INTO Paciente (IdDiabetes, NombreUno, NombreDos, NombreTres, PrimerApellido, SegundoApellido, NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlPaciente);
    $stmt->bind_param("issssssssss", $tipoDiabetes, $nombreUno, $nombreDos, $nombreTres, $primerApellido, $segundoApellido, $dpi, $telefono, $fechaNacimiento, $sexo, $grupoEtnico);
    
    if ($stmt->execute()) {
        echo "<script>alert('Paciente registrado'); window.location.href='pacientesPrueba.php';</script>";;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar el paciente.']);
    }

    $stmt->close();
}

$conn->close();
?>


