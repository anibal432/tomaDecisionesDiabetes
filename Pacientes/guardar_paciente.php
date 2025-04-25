<?php
include '../conexionDiabetes.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    
    $sqlPaciente = "INSERT INTO Paciente (NombreUno, NombreDos, NombreTres, PrimerApellido, SegundoApellido, NoDpi, Telefono, FechaNacimiento, Sexo, GrupoEtnico) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlPaciente);
    $stmt->bind_param("ssssssssss", $nombreUno, $nombreDos, $nombreTres, $primerApellido, $segundoApellido, $dpi, $telefono, $fechaNacimiento, $sexo, $grupoEtnico);
    
    if ($stmt->execute()) {
        // Para AJAX
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'success']);
            exit;
        }
        // Para submit tradicional (por si acaso)
        header("Location: datosPaciente.php?success=true");
        exit;
    } else {
        // Para AJAX
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el paciente.']);
            exit;
        }
        // Para submit tradicional (por si acaso)
        echo "<script>alert('Error al guardar el paciente'); window.location.href='datosPaciente.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>